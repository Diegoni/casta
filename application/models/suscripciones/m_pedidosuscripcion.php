<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	suscripciones
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Pedidos de proveedor de una suscripción 
 *
 */
class M_pedidosuscripcion extends MY_Model
{
	/**
	 * Costructor 
	 * @return M_pedidosuscripcion
	 */
	function __construct()
	{
		$data_model = array(
			'nIdSuscripcion'	=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
			'nIdPedido'			=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
			'nIdLineaPedido'	=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
		);

		parent::__construct('Sus_PedidosSuscripcion', 'nIdPedidoSuscripcion', 'nIdPedidoSuscripcion', 'nIdPedidoSuscripcion', $data_model);	
		$this->_cache = TRUE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onAfterInsert($data)
	 */
	protected function onBeforeInsert(&$data)
	{
		if (parent::onBeforeInsert($data))
		{
			$this->obj->load->model('compras/m_pedidoproveedorlinea');
			$pd = $this->obj->m_pedidoproveedorlinea->load($data['nIdLineaPedido']);
			$data['nIdPedido'] = $pd['nIdPedido'];
			/*UPDATE Sus_PedidosSuscripcion
			SET nIdPedido = b.nIdPedido
			FROM Sus_PedidosSuscripcion a, 
				Inserted i,
				Doc_LineasPedidoProveedor b
			WHERE a.nIdPedidoSuscripcion = i.nIdPedidoSuscripcion
				AND i.nIdLineaPedido = b.nIdLinea*/
			return TRUE;
		}
		return FALSE;
	}
}

/* End of file M_pedidosuscripcion.php */
/* Location: ./system/application/models/suscripciones/M_pedidosuscripcion.php */