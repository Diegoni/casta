<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	compras
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Líneas de pedido de proveedor
 *
 */
class Pedidoproveedorlinea extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Pedidoproveedorlinea
	 */
	function __construct()
	{
		parent::__construct('compras.pedidoproveedorlinea', 'compras/m_pedidoproveedorlinea', TRUE, null, 'Líneas pedido proveedor');
	}

	/**
	 * Cancela un líneas de pedido de cliente
	 * @param int $id Id del pedido
	 * @return MSG
	 */
	function cancelar($id = null)
	{
		$this->userauth->roleCheck($this->auth .'.cancelar');

		$id = isset($id)?$id:$this->input->get_post('id');

		if ($id)
		{
			// Id de las líneas a reclamar
			$ids = is_string($id)?preg_split('/\;/', $id):$id;

			// Agrupa los datos
			$pedidos = array();
			$this->db->trans_begin();
			$count = 0;
			$this->load->model('compras/m_pedidoproveedor');
			foreach($ids as $id)
			{
				if (is_numeric($id))
				{
					$linea = null;
					$res = $this->reg->cancelar($id, $linea);

					++$count;
					if (!$res)
					{
						$this->db->trans_rollback();
						$this->out->error("[{$id}]: ". $this->reg->error_message());
					}
					$link_l = format_enlace_cmd($linea['cTitulo'], site_url('catalogo/articulo/index/' . $linea['nIdLibro']));
					$message = sprintf($this->lang->line('cancelacion-pedido-proveedor-titulo'), $linea['nPendientes'], $link_l, $linea['cTitulo']);
					$this->_add_nota(null, $linea['nIdPedido'], NOTA_INTERNA, $message, $this->m_pedidoproveedor->get_tablename());
				}
			}
			$this->db->trans_commit();
			$this->out->success(($count == 1)?sprintf($this->lang->line('pedido-linea-cancelada'), $ids[0]):sprintf($this->lang->line('pedido-lineas-canceladas'), $count));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Indica el estado de la líneas según el proveedor
	 * @param int $status Id del estado del proveedor
	 * @param int $id Id del pedido
	 * @return MSG
	 */
	function info($status = null, $id = null)
	{
		$this->userauth->roleCheck($this->auth .'.info');

		$id = isset($id)?$id:$this->input->get_post('id');
		$status = isset($status)?$status:$this->input->get_post('status');

		if ($id)
		{
			// Id de las líneas a reclamar
			$ids = is_string($id)?preg_split('/\;/', $id):$id;

			// Agrupa los datos
			$pedidos = array();
			$this->db->trans_begin();
			$count = 0;
			$this->load->model('compras/m_pedidoproveedor');
			foreach($ids as $id)
			{
				if (is_numeric($id))
				{
					$res = $this->reg->update($id, array('nIdInformacion' => $status, 'dFechaInformacion' => time()));
					$linea = $this->reg->load($id);
					
					++$count;
					if (!$res)
					{
						$this->db->trans_rollback();
						$this->out->error("[{$id}]: ". $this->reg->error_message());
					}
					$link_l = format_enlace_cmd($linea['nIdLibro'], site_url('catalogo/articulo/index/' . $linea['nIdLibro']));
					$message = sprintf($this->lang->line('estado-pedido-proveedor-titulo'), $linea['cInformacion'], $link_l, $linea['cTitulo']);
					$this->_add_nota(null, $linea['nIdPedido'], NOTA_INTERNA, $message, $this->m_pedidoproveedor->get_tablename());
				}
			}
			$this->db->trans_commit();
			$this->out->success(($count == 1)?sprintf($this->lang->line('pedido-linea-estado'), $ids[0]):sprintf($this->lang->line('pedido-lineas-estado'), $count));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}
	
	/**
	 * Cancela un pedido de proveedor
	 * @param int $id Id del pedido
	 * @return MSG
	 */
	/*function estado($status = null, $id = null)
	{
		$this->userauth->roleCheck($this->auth .'.cancelar');

		$id = isset($id)?$id:$this->input->get_post('id');
		$status = isset($status)?$status:$this->input->get_post('status');

		if ($id)
		{
			$ids = is_string($id)?preg_split('/\;/', $id):$id;
			$count = 0;
			foreach($ids as $id)
			{
				if (is_numeric($id))
				{
					$res = $this->reg->update($id, array('nIdInformacion' => $status, 'dFechaInformacion' => time()));
					if (!$res) $this->out->error($this->reg->error_message());
					$this->_add_nota(null, $id, NOTA_INTERNA, $this->lang->line('pedidoproveedor-cancelado-history'));
					++$count;
				}
			}
			$this->out->success(sprintf($this->lang->line('pedido-proveedor-cancelado'), implode(', ', $ids)));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}*/

}

/* End of file Pedidoproveedorlinea.php */
/* Location: ./system/application/controllers/compras/Pedidoproveedorlinea.php */