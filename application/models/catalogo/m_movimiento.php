<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	catalogo
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @filesource
 */

/**
 * Movimientos de sección
 *
 */
class M_movimiento extends MY_Model
{
	/**
	 * Costructor 
	 * @return M_movimiento
	 */
	function __construct()
	{
		$data_model = array(
			'nIdLibro'				=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'catalogo/articulo/search')),		
			'nIdSeccionOrigen'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'generico/seccion/search')),
			'nIdSeccionDestino'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'generico/seccion/search')),
			'nCantidad'				=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_DEFAULT_VALUE => 0),		
			'nEnFirme'				=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_DEFAULT_VALUE => 0),		
			'nEnDeposito'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_DEFAULT_VALUE => 0),		
			'fCoste' 				=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT),
			'bReposicion' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),
		);

		parent::__construct('Doc_Movimientos', 'nIdMovimiento', 'nIdMovimiento', 'nIdMovimiento', $data_model, TRUE);	
	}
	
	/**
	 * Mueve artículos entre secciones. Mueve primero el firme y luego el depósito
	 * @param  int $id       Id del artículo
	 * @param  int $ido      Id de la sección de origen
	 * @param  int $idd      Id de la sección de destino
	 * @param  int $cantidad Cantidad
	 * @return int Id del nuevo registro
	 */
	function mover($id = null, $ido = null, $idd = null, $cantidad = null)
	{
		if ($ido == $idd)
		{
			$this->_set_error_message($this->lang->line('mover-seccion-origen-destino-error'));
			return FALSE;
		}
		$this->obj->load->model('catalogo/m_articuloseccion');
		$this->obj->load->model('generico/m_seccion');
		$cantidad = is_numeric($cantidad)?$cantidad:1;
		//origen
		$data = $this->obj->m_articuloseccion->get(null, null, null, null, "nIdLibro={$id} AND nIdSeccion = {$ido}");
		if (count($data) != 1)
		{
			$this->_set_error_message(sprintf($this->lang->line('mover-seccion-origen-error'), $ido));
			return FALSE;
		}
		$origen = $data[0];
			
		//destino
		$seccion = $this->obj->m_seccion->load($idd);
		if (($seccion === FALSE) || ($seccion['bBloqueada']))
		{
			$this->_set_error_message(sprintf($this->lang->line('seccion-bloqueada'), $idd));
			return FALSE;
		}
		if (count($data) != 1)
		{
			$this->_set_error_message(sprintf($this->lang->line('mover-seccion-origen-error'), $ido));
			return FALSE;
		}
		$data = $this->obj->m_articuloseccion->get(null, null, null, null, "nIdLibro={$id} AND nIdSeccion = {$idd}");
		$this->db->trans_begin();
		if (count($data) == 0)
		{
			// La crea
			$id_n = $this->obj->m_articuloseccion->insert(array('nIdLibro' => $id, 'nIdSeccion' => $idd));
			if ($id_n < 0)
			{
				$this->_set_error_message($this->obj->m_articuloseccion->error_message());
				$this->db->trans_rollback();
				return FALSE;
			}
			$destino = $this->obj->m_articuloseccion->load($id_n);
		}
		else
		{
			$destino = $data[0];
		}
		$firme = min($origen['nStockFirme'], $cantidad);
		$cantidad -= $firme;
		$deposito = min($origen['nStockDeposito'], $cantidad);
		$cantidad -= $deposito;
		if ($cantidad != 0)
		{
			$this->_set_error_message(sprintf($this->lang->line('mover-seccion-no-stock-error'), $ido));
			$this->db->trans_rollback();
			return FALSE;
		}
		$norigen['nStockFirme'] = $origen['nStockFirme'] - $firme;
		$norigen['nStockDeposito'] = $origen['nStockDeposito'] - $deposito;
		$ndestino['nStockFirme'] = $destino['nStockFirme'] + $firme;
		$ndestino['nStockDeposito'] = $destino['nStockDeposito'] + $deposito;

		if (!$this->obj->m_articuloseccion->update($origen['id'], $norigen))
		{
			$this->_set_error_message($this->obj->m_articuloseccion->error_message());
			$this->db->trans_rollback();
			return FALSE;
		}
		if (!$this->obj->m_articuloseccion->update($destino['id'], $ndestino))
		{
			$this->_set_error_message($this->obj->m_articuloseccion->error_message());
			$this->db->trans_rollback();
			return FALSE;
		}
		$this->obj->load->model('catalogo/m_articulo');
		$libro = $this->obj->m_articulo->load($id);
		$data = array(
			'nIdLibro'	=> $id,
			'nIdSeccionOrigen'	=> $ido,
			'nIdSeccionDestino'	=> $idd,
			'nEnFirme'			=> $firme,
			'nEnDeposito'		=> $deposito,
			'nCantidad'			=> $firme + $deposito,
			'fCoste'			=> $libro['fPrecioCompra']
		);

		// La crea
		$id_n = $this->insert($data);
		if ($id_n < 0)
		{
			return FALSE;
		}
		$this->db->trans_commit();
		return $id_n;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeSelect($id, $sort, $dir, $where)
	 */
	protected function onBeforeSelect($id = null, &$sort = null, &$dir = null, &$where = null)
	{
		if (parent::onBeforeSelect($id, $sort, $dir, $where))
		{
			$this->db->select('Cat_Secciones.cNombre cSeccionOrigen, Cat_Secciones2.cNombre cSeccionDestino, Cat_Fondo.cTitulo');
			$this->db->join('Cat_Secciones', "Cat_Secciones.nIdSeccion = {$this->_tablename}.nIdSeccionOrigen");
			$this->db->join('Cat_Secciones Cat_Secciones2', "Cat_Secciones2.nIdSeccion = {$this->_tablename}.nIdSeccionDestino");
			$this->db->join('Cat_Fondo', "Cat_Fondo.nIdLibro = {$this->_tablename}.nIdLibro");
			return TRUE;
		}
		return FALSE;
	}
}

/* End of file M_movimiento.php */
/* Location: ./system/application/models/stocks/M_movimiento.php */