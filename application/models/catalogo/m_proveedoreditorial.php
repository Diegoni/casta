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
 * Proveedores de una editorial
 *
 */
class M_proveedoreditorial extends MY_Model
{
	/**
	 * Constructor
	 * @return M_proveedoreditorial
	 */
	function __construct()
	{
		$obj = get_instance();
		$data_model = array(
			'nIdProveedor'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'proveedores/proveedor/search', 'cProveedor')),
			'nIdEditorial'			=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'catalogo/editorial/search', 'cEditorial')),
			'nIdTipo'				=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'catalogo/tipolibro/search', 'cTipo')),
			'dCompra' 				=> array(DATA_MODEL_READONLY => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATETIME),
			'nIdPlazoEnvio' 		=> array(DATA_MODEL_READONLY => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'compras/plazoenvio/search')), 
			'nIdPlazoEnvioManual' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'compras/plazoenvio/search')), 
			'nDiasEnvio' 			=> array(DATA_MODEL_READONLY => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT), 
			'fDescuento' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT),		
		);

		parent::__construct('Prv_Proveedores_Editoriales_Tipos', 'nIdProveedorEditorial', 'nIdProveedorEditorial', 'nIdProveedorEditorial', $data_model, TRUE);
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
			$this->db->select('Prv_Proveedores.cNombre, Prv_Proveedores.cApellido, Prv_Proveedores.cEmpresa')
			->select('Cat_Tipos.cDescripcion cTipo, Cat_Editoriales.cNombre cEditorial')
			->select('Prv_Proveedores.bDisabled')
			->join('Prv_Proveedores', 'Prv_Proveedores.nIdProveedor = Prv_Proveedores_Editoriales_Tipos.nIdProveedor')
			->join('Cat_Tipos', 'Cat_Tipos.nIdTipo = Prv_Proveedores_Editoriales_Tipos.nIdTipo', 'left')
			->join('Cat_Editoriales' ,'Cat_Editoriales.nIdEditorial = Prv_Proveedores_Editoriales_Tipos.nIdEditorial', 'left');
			return TRUE;
		}
		return FALSE;
	}
}

/* End of file M_proveedoreditorial.php */
/* Location: ./system/application/models/catalogo/M_proveedoreditorial.php */
