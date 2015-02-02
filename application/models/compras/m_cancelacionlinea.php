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
 * Líneas de cancelación pedido proveedor
 *
 */
class M_cancelacionlinea extends MY_Model
{
	/**
	 * Constructor
	 * @return M_cancelacionlinea
	 */
	function __construct()
	{
		$obj = get_instance();
		$data_model = array(
			'nIdCancelacion'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'compras/cancelacion/search')),
			'nIdLineaPedido'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE),		
			'nCantidad' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE), 			
		);

		parent::__construct('Doc_LineasCancelacionPedidoProveedor', 'nIdLinea', 'nIdLinea', 'nIdLinea', $data_model, TRUE);
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeSelect($id, $sort, $dir, $where)
	 */
	protected function onBeforeSelect($id = null, &$sort = null, &$dir = null, &$where = null)
	{
		if (parent::onBeforeSelect($id, $sort, $dir, $where))
		{
			$this->db->select('Cat_Fondo.nIdLibro, Cat_Fondo.cTitulo, Cat_Fondo.cAutores, Cat_Fondo.cISBN, Cat_Secciones.cNombre cSeccion, Cat_Editoriales.cNombre cEditorial');
			$this->db->select('Doc_EstadosLineaPedidoProveedor.cDescripcion cEstado');
			$this->db->select('Doc_PedidosProveedor.nIdPedido, Doc_PedidosProveedor.cRefProveedor');
			$this->db->select($this->_date_field('Doc_PedidosProveedor.dFechaEntrega', 'dFechaEntrega'));
			$this->db->select('Doc_LineasPedidoProveedor.fPrecio, Doc_LineasPedidoProveedor.fDescuento, Doc_LineasPedidoProveedor.fIVA, Doc_LineasPedidoProveedor.fRecargo');
			$this->db->join('Doc_LineasPedidoProveedor', "{$this->_tablename}.nIdLineaPedido = Doc_LineasPedidoProveedor.nIdLinea");
			$this->db->join('Doc_PedidosProveedor', "Doc_PedidosProveedor.nIdPedido = Doc_LineasPedidoProveedor.nIdPedido");
			$this->db->join('Cat_Fondo', 'Cat_Fondo.nIdLibro = Doc_LineasPedidoProveedor.nIdLibro');
			$this->db->join('Cat_Secciones', 'Cat_Secciones.nIdSeccion = Doc_LineasPedidoProveedor.nIdSeccion');
			$this->db->join('Cat_Editoriales', 'Cat_Fondo.nIdEditorial = Cat_Editoriales.nIdEditorial', 'left');
			$this->db->join('Doc_EstadosLineaPedidoProveedor', 'Doc_EstadosLineaPedidoProveedor.nIdEstado = Doc_LineasPedidoProveedor.nIdEstado', 'left');
						
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onAfterSelect($data, $id)
	 */
	protected function onAfterSelect(&$data, $id = null)
	{
		if (parent::onAfterSelect($data, $id))
		{
			$importes = format_calculate_importes($data);
			$data = array_merge($data, $importes);
			return TRUE;
		}
		return FALSE;
	}
}

/* End of file M_cancelacionlinea.php */
/* Location: ./system/application/models/compras/M_cancelacionlinea.php */
