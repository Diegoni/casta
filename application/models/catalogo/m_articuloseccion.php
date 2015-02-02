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
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Secciones de un artículo
 *
 */
class M_Articuloseccion extends MY_Model
{
	/**
	 * Constructor
	 * @return M_Articuloseccion
	 */
	function __construct()
	{
		$obj = get_instance();
		$data_model = array(
			'nIdSeccion'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'generico/seccion/search', 'cNombre')),
			'nIdLibro'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'catalogo/articulo/search')),		
			'nStockFirme'		=> array(DATA_MODEL_READONLY => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_DEFAULT_VALUE => 0),		
			'nStockDeposito'	=> array(DATA_MODEL_READONLY => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_DEFAULT_VALUE => 0),		
			'nStockReservado'	=> array(DATA_MODEL_READONLY => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_DEFAULT_VALUE => 0),		
			'nStockRecibir'		=> array(DATA_MODEL_READONLY => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_DEFAULT_VALUE => 0),		
			'nStockAPedir'		=> array(DATA_MODEL_READONLY => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_DEFAULT_VALUE => 0),		
			'nStockServir'		=> array(DATA_MODEL_READONLY => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_DEFAULT_VALUE => 0),		
			'nStockADevolver'	=> array(DATA_MODEL_READONLY => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_DEFAULT_VALUE => 0),		
			#'nStockAExamen'		=> array(DATA_MODEL_READONLY => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_DEFAULT_VALUE => 0),		
			'nStockMinimo'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_DEFAULT_VALUE => 0),		
			'nStockMaximo'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_DEFAULT_VALUE => 0),		
		);

		parent::__construct('Cat_Secciones_Libros', 'nIdSeccionLibro', 'Cat_Secciones.cNombre', 'nIdSeccionLibro', $data_model);
	}

	/**
	 * Calcula los stocks de un artículo o una sección
	 * @param int $idl Id del artículo
	 * @param int $ids Id de la sección
	 * @return array
	 */
	function stocks($idl = null, $ids = null)
	{
		$this->db->flush_cache();
		$this->db->select_sum('nStockFirme', 'nStockFirme')
		->select_sum('nStockDeposito', 'nStockDeposito')
		->select_sum('nStockReservado', 'nStockReservado')
		->select_sum('nStockRecibir', 'nStockRecibir')
		->select_sum('nStockAPedir', 'nStockAPedir')
		->select_sum('nStockServir', 'nStockServir')
		->select_sum('nStockADevolver', 'nStockADevolver')
		->from("{$this->_tablename}");

		if (isset($idl)) $this->db->where("nIdLibro = {$idl}");
		if (isset($ids)) $this->db->where("nIdSeccion = {$ids}");
		
		$query = $this->db->get();
		$data = $this->_get_results($query);
				
		return isset($data[0])?$data[0]:null;		
	}
	
	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeSelect($id, $sort, $dir, $where)
	 */
	protected function onBeforeSelect($id = null, &$sort = null, &$dir = null, &$where = null)
	{
		if (parent::onBeforeSelect($id, $sort, $dir, $where))
		{
			$this->db->select('Cat_Secciones.cNombre');
			$this->db->join('Cat_Secciones', 'Cat_Secciones.nIdSeccion = Cat_Secciones_Libros.nIdSeccion');
			return TRUE;
		}
		return FALSE;
	}
	
	/**
	 * Pone a 0 el stock de los genéricos
	 * @return int, registros afectados
	 */
	function genericos()
	{
		$this->db->flush_cache();
		$sql = 'UPDATE Cat_Secciones_Libros SET nStockFirme = 0, nStockDeposito = 0  
		WHERE nIdLibro IN (SELECT nIdLibro FROM Cat_Fondo WHERE nIdEstado=16)
		AND (nStockFirme <> 0  OR nStockDeposito <> 0)';
		$this->db->query($sql);
		return $this->db->affected_rows();		
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onAfterSelect($data, $id)
	 */
	protected function onAfterSelect(&$data, $id = null)
	{
		if (parent::onAfterSelect($data, $id))
		{
			$disponible = isset($data['nStockFirme'])?$data['nStockFirme']:0;
			$disponible += isset($data['nStockDeposito'])?$data['nStockDeposito']:0;
			$disponible -= isset($data['nStockReservado'])?$data['nStockReservado']:0;
			$disponible -= isset($data['nStockADevolver'])?$data['nStockADevolver']:0;
			$data['nStockDisponible'] = $disponible;
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeDelete($id)
	 */
	protected function onBeforeDelete($id) 
	{
		$old = $this->load($id);
		# Comprueba si hay stocks
		if ($old['nStockFirme'] != 0 ||
			$old['nStockDeposito'] != 0 ||
			$old['nStockReservado'] != 0 ||
			$old['nStockRecibir'] != 0 ||
			$old['nStockServir'] != 0 ||
			$old['nStockAPedir'] != 0 ||
			$old['nStockADevolver'] != 0
			)
		{
			$this->_set_error_message($this->lang->line('articulo-seccion-con-stock'));
			return FALSE;
		}
		return parent::onBeforeDelete($id);
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeInsert($data)
	 */
	/*protected function onBeforeInsert(&$data)
	{
		if (parent::onBeforeInsert($data))
		{
		}
		return TRUE;
	}*/

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeUpdate($data)
	 */
	/*protected function onBeforeUpdate($id, &$data)
	{
		if (parent::onBeforeUpdate($id, $data))
		{
			# Genéricos a 0?
			if (isset($data['nStockFirme']) || isset($data['nStockDeposito']))
			{
				$old = $this->load($id);

			}
			UPDATE Cat_Secciones_Libros
			SET nStockFirme = 0, nStockDeposito = 0
			FROM Cat_Secciones_Libros c, Inserted i, Cat_Fondo f
			WHERE c.nIdLibro = i.nIdLibro and c.nIdSeccion = i.nIdSeccion
				and i.nIdLibro = f.nIdLibro and nIdEstado = 16
			

		}
		return TRUE;
	}*/

}

/* End of file M_Articuloseccion.php */
/* Location: ./system/application/models/catalogo/M_Articuloseccion.php */
