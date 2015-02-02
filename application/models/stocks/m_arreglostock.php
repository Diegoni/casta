<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	stocks
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Tipos de regulación
 *
 */
class M_arreglostock extends MY_Model
{
	/**
	 * Costructor 
	 * @return M_arreglostock
	 */
	function __construct()
	{
		$data_model = array(
			'nIdSeccion'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'generico/seccion/search')),
			'nIdLibro'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'catalogo/articulo/search')),		
			'nIdMotivo'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'stocks/tiporegulacion/search')),		
			'nCantidadFirme'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_DEFAULT_VALUE => 0),		
			'nCantidadDeposito'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_DEFAULT_VALUE => 0),		
			'fCoste' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT),
		);

		parent::__construct('Cat_Movimiento_Stock', 'nIdMovimiento', 'nIdMovimiento', 'nIdMovimiento', $data_model, TRUE);	
	}

	/**
	 * Realiza un ajuste de stock
	 * @param  int $id          Id del artículo
	 * @param  int $firme       Cantidad real en firme
	 * @param  int $deposito    Cantidad real en depósito
	 * @param  int $motivomas   Id del motivo si el stock aumenta
	 * @param  int $motivomenos Id del motivo si el stock disminuye
	 * @return bool
	 */
	function arreglar($id, $firme, $deposito, $motivomas, $motivomenos)
	{
		// Stock actual
		$this->obj->load->model('catalogo/m_articuloseccion');
		$data = $this->obj->m_articuloseccion->load($id);
		#echo '<pre>';
		#echo "f: {$firme}, d: {$deposito}\n";
		$firme = (is_numeric($firme))?$firme: $data['nStockFirme'];
		$deposito = (is_numeric($deposito))?$deposito : $data['nStockDeposito'];
		#echo "f: {$firme}, d: {$deposito}\n";
		#echo "sf: {$data['nStockFirme']} sd: {$data['nStockDeposito']}\n";

		// Diferencias
		$df = $data['nStockFirme'] - $firme;
		$dd = $data['nStockDeposito'] - $deposito;
		#echo "df: {$df} , dd: {$dd}\n";
		if ($df == 0 && $dd == 0)
		{ 
			$this->_set_error_message($this->lang->line('regulacion-no-diff'));
			return FALSE;
		}
		if (($df < 0 || $dd < 0) && $motivomas < 1) 
		{ 
			$this->_set_error_message($this->lang->line('regulacion-no-motivomenos'));
			return FALSE;
		}
		if (($df > 0 || $dd > 0) && $motivomenos < 1)
		{ 
			$this->_set_error_message($this->lang->line('regulacion-no-motivomas'));
			return FALSE;
		}

		// Coste
		$this->obj->load->model('catalogo/m_articulo');
		$art = $this->obj->m_articulo->load($data['nIdLibro']);
			
		// Crea los movimientos
		$this->db->trans_begin();
		$reg['nIdSeccion'] = $data['nIdSeccion'];
		$reg['nIdLibro'] = $data['nIdLibro'];
		$reg['fCoste'] = $art['fPrecioCompra'];
		if ($df < 0)
		{
			$reg['nIdMotivo'] = $motivomas;
			$reg['nCantidadFirme'] = -$df;
			$reg['nCantidadDeposito'] = 0;
			$idr = $this->insert($reg);
			if ($idr < 0)
			{
				$this->db->trans_rollback();
				return FALSE;
			}
		}
		if ($df > 0)
		{
			$reg['nIdMotivo'] = $motivomenos;
			$reg['nCantidadFirme'] = $df;
			$reg['nCantidadDeposito'] = 0;
			$idr = $this->insert($reg);
			if ($idr < 0)
			{
				$this->db->trans_rollback();
				return FALSE;
			}
		}
		if ($dd < 0)
		{
			$reg['nIdMotivo'] = $motivomas;
			$reg['nCantidadFirme'] = 0;
			$reg['nCantidadDeposito'] = -$dd;
			$idr = $this->insert($reg);
			if ($idr < 0)
			{
				$this->db->trans_rollback();
				return FALSE;
			}
		}
		if ($dd > 0)
		{
			$reg['nIdMotivo'] = $motivomenos;
			$reg['nCantidadFirme'] = 0;
			$reg['nCantidadDeposito'] = $dd;
			$idr = $this->insert($reg);
			if ($idr < 0)
			{
				$this->db->trans_rollback();
				return FALSE;
			}
		}

		// Actualiza el stock
		$upd['nStockFirme'] = $data['nStockFirme'] - $df;
		$upd['nStockDeposito'] = $data['nStockDeposito'] - $dd;
		if (!$this->obj->m_articuloseccion->update($id, $upd))
		{
			$this->db->trans_rollback();
			$this->_set_error_message($this->obj->m_articuloseccion->error_message);
			return FALSE;
		}
		$this->db->trans_commit();

		return array(
			'df' => $df, 
			'dd' => $dd, 
			'fnew' => $upd['nStockFirme'],
			'fold' => $data['nStockFirme'],
			'dnew' => $upd['nStockDeposito'],
			'dold' => $data['nStockDeposito']
		);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeSelect($id, $sort, $dir, $where)
	 */
	protected function onBeforeSelect($id = null, &$sort = null, &$dir = null, &$where = null)
	{
		if (parent::onBeforeSelect($id, $sort, $dir, $where))
		{
			$this->db->select('Cat_Secciones.cNombre cSeccion, Cat_Fondo.cTitulo, Gen_Movimiento_Stock.cDescripcion cMotivo');
			$this->db->join('Cat_Secciones', "Cat_Secciones.nIdSeccion = {$this->_tablename}.nIdSeccion");
			$this->db->join('Cat_Fondo', "Cat_Fondo.nIdLibro = {$this->_tablename}.nIdLibro");
			$this->db->join('Gen_Movimiento_Stock' , "Gen_Movimiento_Stock.nId = {$this->_tablename}.nIdMotivo");
			return TRUE;
		}
		return FALSE;
	}
}

/* End of file M_arreglostock.php */
/* Location: ./system/application/models/stocks/M_arreglostock.php */