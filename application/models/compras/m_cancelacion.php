<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	compras
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Cancelación Pedido Proveedor
 *
 */
class M_cancelacion extends MY_Model {

	/**
	 * Constructor
	 * @return M_cancelacion
	 */
	function __construct()
	{
		$data_model = array(
            'nIdProveedor'	=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'proveedores/proveedor/search', 'cProveedor')),
            'nIdDireccion'	=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'proveedores/direccion/search')),
		);

		parent::__construct('Doc_CancelacionesPedidoProveedor', 'nIdCancelacion', 'nIdCancelacion', 'nIdCancelacion', $data_model, TRUE);

		$this->_relations['lineas'] = array(
            'ref' => 'compras/m_cancelacionlinea',
            'cascade' => TRUE,
            'type' => DATA_MODEL_RELATION_1N,
            'fk' => 'nIdCancelacion');

		$this->_relations['proveedor'] = array(
            'ref' => 'proveedores/m_proveedor',
            'fk' => 'nIdProveedor');

		$this->_relations['direccion'] = array(
            'ref' => 'proveedores/m_direccion',
            'fk' => 'nIdDireccion');
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeSelect($id, $sort, $dir, $where)
	 */
	protected function onBeforeSelect($id = null, &$sort = null, &$dir = null, &$where = null) {
		if (parent::onBeforeSelect($id, $sort, $dir, $where)) {
			$this->db->select('Prv_Proveedores.cNombre, Prv_Proveedores.cApellido, Prv_Proveedores.cEmpresa');
			$this->db->join('Prv_Proveedores', "Prv_Proveedores.nIdProveedor = {$this->_tablename}.nIdProveedor", 'left');
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onAfterSelect($data, $id)
	 */
	protected function onAfterSelect(&$data, $id = null) {
		if (parent::onAfterSelect($data, $id)) {
			$data['cProveedor'] = format_name($data['cNombre'], $data['cApellido'], $data['cEmpresa']);
			return TRUE;
		}
		return FALSE;
	}

}

/* End of file M_cancelacion.php */
/* Location: ./system/application/models/compras/M_cancelacion.php */
