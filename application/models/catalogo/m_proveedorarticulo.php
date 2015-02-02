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
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Proveedores de un artículo
 *
 */
class M_proveedorarticulo extends MY_Model
{
	/**
	 * Constructor
	 * @return M_proveedorarticulo
	 */
	function __construct()
	{
		$obj = get_instance();
		$data_model = array(
			'nIdProveedor'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'proveedores/proveedor/search', 'cProveedor')),
			'nIdLibro'				=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'catalogo/articulo/search')),
			'bAutomatico' 			=> array(DATA_MODEL_READONLY => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),		
			'nIdPlazoEnvio' 		=> array(DATA_MODEL_READONLY => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'compras/plazoenvio/search')), 
			'nIdPlazoEnvioManual' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'compras/plazoenvio/search')), 
			'nDiasEnvio' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT), 
			'fDescuento' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DOUBLE),		
			'dCompra' 				=> array(DATA_MODEL_READONLY => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATETIME),
		);

		parent::__construct('Prv_Proveedores_Cat_Fondo', 'nIdProveedorFondo', 'nIdProveedorFondo', 'nIdProveedorFondo', $data_model, TRUE);
		#$this->_cache = TRUE;
	}
		
	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onAfterSelect($data, $id)
	 */
	protected function onAfterSelect(&$data, $id = null)
	{
		if (parent::onAfterSelect($data, $id))
		{
			$data['cProveedor'] = format_name($data['cNombre'], $data['cApellido'], $data['cEmpresa']);
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeSelect($id, $sort, $dir, $where)
	 */
	protected function onBeforeSelect($id = null, &$sort = null, &$dir = null, &$where = null)
	{
		if (parent::onBeforeSelect($id, $sort, $dir, $where))
		{
			$this->db->select('pv.cNombre, pv.cApellido, pv.cEmpresa')
			->select('pv.bDisabled')
			->join('Prv_Proveedores pv', 'pv.nIdProveedor = Prv_Proveedores_Cat_Fondo.nIdProveedor');
			return TRUE;
		}
		return FALSE;
	}

}

/* End of file M_proveedorarticulo.php */
/* Location: ./system/application/models/catalogo/M_proveedorarticulo.php */
