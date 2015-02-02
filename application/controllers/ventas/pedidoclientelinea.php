<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	ventas
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Líneas de pedido de cliente
 *
 */
class Pedidoclientelinea extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Pedidoclientelinea
	 */
	function __construct()
	{
		parent::__construct('ventas.pedidoclientelinea', 'ventas/m_pedidoclientelinea', TRUE, null, 'Líneas pedido cliente');
	}

	/**
	 * Cancela un línaes de pedido de cliente
	 * @param int $id Ids de las líneas de pedido separados por ;
	 * @return MSG
	 */
	function cancelar($id = null)
	{
		$this->userauth->roleCheck($this->auth .'.add');

		$id = isset($id)?$id:$this->input->get_post('id');

		if ($id)
		{
			$ids = is_string($id)?preg_split('/\;/', $id):$id;
			$this->load->model('ventas/m_pedidocliente');
			foreach($ids as $k => $id)
			{
				if (is_numeric($id))
				{
					$res = $this->reg->cancelar($id);
					if (!$res) $this->out->error($this->reg->error_message());
		
					$data = $this->reg->load($id);
					$link = format_enlace_cmd($data['cTitulo'], site_url('catalogo/articulo/index/' . $data['nIdLibro']));
					$message = sprintf(sprintf($this->lang->line('pedido-linea-cancelada'), $link));
					$this->_add_nota(null, $data['nIdPedido'], NOTA_INTERNA, $message, $this->m_pedidocliente->get_tablename());
				}
				else
				{
					unset($ids[$k]);
				}
			}

			$this->out->success(sprintf($this->lang->line('pedido-linea-cancelada'), implode(', ', $ids)));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Marcar la línea del pedido como imposible de servir
	 * @param int $id Ids de las líneas de pedido separados por ;
	 * @return MSG
	 */
	function imposibleservir($id = null)
	{
		$this->userauth->roleCheck($this->auth .'.add');

		$id = isset($id)?$id:$this->input->get_post('id');

		if ($id)
		{
			$ids = is_string($id)?preg_split('/\;/', $id):$id;

			$this->load->model('ventas/m_pedidocliente');
			foreach($ids as $k => $id)
			{
				if (is_numeric($id))
				{
					$res = $this->reg->imposibleservir($id, $imposible);
					if (!$res) $this->out->error($this->reg->error_message());
		
					$data = $this->reg->load($id);
					$link = format_enlace_cmd($data['cTitulo'], site_url('catalogo/articulo/index/' . $data['nIdLibro']));
					$message = sprintf(sprintf($this->lang->line($imposible?'pedido-linea-imposibleservir':'pedido-linea-enproceso'), $link));
					$this->_add_nota(null, $data['nIdPedido'], NOTA_INTERNA, $message, $this->m_pedidocliente->get_tablename());
				}
				else
				{
					unset($ids[$k]);
				}
			}

			$this->out->success(sprintf($this->lang->line('pedido-linea-actualizada'), implode(', ', $ids)));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Actualiza el precio de una líenea de pedido
	 * @param int $id Ids de las líneas de pedido separados por ;
	 * @param int $tarifa Id de la tarifa
	 * @return MSG
	 */
	function actualizarprecio($id = null, $tarifa = null)
	{
		$this->userauth->roleCheck($this->auth .'.add');

		$id = isset($id)?$id:$this->input->get_post('id');
		$tarifa = isset($tarifa)?$tarifa:$this->input->get_post('tarifa');

		if (is_numeric($id))
		{
			$this->load->model('catalogo/m_articulo');
			$this->load->model('ventas/m_pedidocliente');
			$this->load->model('clientes/m_cliente');

			$lpd = $this->reg->load($id);
			$pd = $this->m_pedidocliente->load($lpd['nIdPedido']);
			$tarifascliente = $this->m_cliente->load($pd['nIdCliente'], 'tarifas');
			$tarifa = $tarifascliente['nIdTipoTarifa'];
			$tarifascliente = $tarifascliente['tarifas'];
			$ids = is_string($id)?preg_split('/\;/', $id):$id;

			foreach($ids as $k => $id)
			{
				if (is_numeric($id))
				{
					$data = $this->reg->actualizarprecio($id, $tarifa, $tarifascliente);
		
					if (!$data) $this->out->error($this->reg->error_message());
		
					$link = format_enlace_cmd($data['art']['cTitulo'], site_url('catalogo/articulo/index/' . $data['art']['nIdLibro']));
					$message = sprintf(sprintf($this->lang->line('pedido-linea-cambioprecio'), $link,
					format_add_iva($data['old'], $data['art']['fIVA']), $data['old'],
					format_add_iva($data['new'], $data['art']['fIVA']), $data['new']));
					$this->_add_nota(null, $data['linea']['nIdPedido'], NOTA_INTERNA, $message, $this->m_pedidocliente->get_tablename());
				}
				else
				{
					unset($ids[$k]);
				}
			}

			$this->out->success(sprintf($this->lang->line('pedido-linea-actualizada'), implode(', ', $ids)));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Marcar la línea del pedido como catalogada
	 * @param int $id Ids de las líneas de pedido separados por ;
	 * @return MSG
	 */
	function catalogado($id = null)
	{
		$this->userauth->roleCheck($this->auth .'.add');

		$id = isset($id)?$id:$this->input->get_post('id');

		if ($id)
		{
			$ids = is_string($id)?preg_split('/\;/', $id):$id;
			$this->load->model('ventas/m_pedidocliente');
			foreach($ids as $k => $id)
			{
				if (is_numeric($id))
				{
					$res = $this->reg->catalogado($id);
					if (!$res) $this->out->error($this->reg->error_message());
		
					$data = $this->reg->load($id);
					$link = format_enlace_cmd($data['cTitulo'], site_url('catalogo/articulo/index/' . $data['nIdLibro']));
					$message = sprintf(sprintf($this->lang->line('pedido-linea-catalogada'), $link));
					$this->_add_nota(null, $data['nIdPedido'], NOTA_INTERNA, $message, $this->m_pedidocliente->get_tablename());
				}
				else
				{
					unset($ids[$k]);
				}
			}

			$this->out->success(sprintf($this->lang->line('pedido-linea-actualizada'), implode(', ', $ids)));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Reserva las unidades del cliente
	 * @param int $id Ids de las líneas de pedido separados por ;
	 * @return MSG
	 */
	function reservar($id = null)
	{
		$this->userauth->roleCheck($this->auth .'.add');

		$id = isset($id)?$id:$this->input->get_post('id');

		if ($id)
		{
			$ids = is_string($id)?preg_split('/\;/', $id):$id;
			$this->load->model('ventas/m_pedidocliente');
			foreach($ids as $k => $id)
			{
				if (is_numeric($id))
				{
					$data = $this->reg->load($id);
					$res = $this->reg->update($id, array('nCantidadServida' => $data['nCantidad']));
					if (!$res) $this->out->error($this->reg->error_message());		
				}
				else
				{
					unset($ids[$k]);
				}
			}

			$this->out->success(sprintf($this->lang->line('pedido-linea-reservada'), implode(', ', $ids)));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Marcar la línea del pedido como avisada
	 * @param int $id Id de la línea
	 * @return MSG
	 */
	function avisado($id = null, $aviso = null)
	{
		$this->userauth->roleCheck($this->auth .'.add');

		$id = isset($id)?$id:$this->input->get_post('id');
		$aviso = isset($aviso)?$aviso:$this->input->get_post('aviso');

		if ($id)
		{
			$ids = is_string($id)?preg_split('/\;/', $id):$id;
			$this->load->model('ventas/m_pedidocliente');
			foreach($ids as $k => $id)
			{
				if (is_numeric($id))
				{
					$aviso = (!empty($aviso)?format_tobool($aviso):null);
					$res = $this->reg->avisado($id, $aviso);
					if (!$res) $this->out->error($this->reg->error_message());
		
					$data = $this->reg->load($id);
					$link = format_enlace_cmd($data['cTitulo'], site_url('catalogo/articulo/index/' . $data['nIdLibro']));
					$message = sprintf(sprintf($this->lang->line(($aviso)?'pedido-linea-avisado':'pedido-linea-quitar-avisado'), $link));
					$this->_add_nota(null, $data['nIdPedido'], NOTA_INTERNA, $message, $this->m_pedidocliente->get_tablename());
				}
				else
				{
					unset($ids[$k]);
				}
			}

			$this->out->success(sprintf($this->lang->line('pedido-linea-actualizada'), implode(', ', $ids)));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Líneas de pedido pendientes
	 * @param $id Id del pedido
	 * @return DATA
	 */
	function pendientes($id = null)
	{
		$id	= isset($id)?$id:$this->input->get_post('id');

		if ($id)
		{
			$this->load->model('ventas/m_pedidoclientelineapendiente');
			$this->load->model('ventas/m_pedidocliente');
			//$this->load->model('catalogo/m_articulo');
			$this->load->model('generico/m_seccion');
			$this->load->model('ventas/m_factura');

			// Lee las líneas de pedido pendiente
			$data = $this->m_pedidoclientelineapendiente->pendientes_pedido($id);

			//¿Tiene un anticipo?
			$pedido = $this->m_pedidocliente->load($id);
			if (($pedido['fAnticipo'] > 0) && (isset($pedido['nIdFactura']) && !isset($pedido['nIdAlbaranDescuentaAnticipo'])))
			{
				$ft = $this->m_factura->load($pedido['nIdFactura']);
				if ($ft['nIdEstado'] != FACTURA_STATUS_EN_PROCESO)
				{
					$iva = $this->config->item('bp.anticipo.iva');
					$pvp = $pedido['fAnticipo'];
					$precio = format_quitar_iva($pvp, $iva);
					$seccion = $this->m_seccion->load($this->config->item('bp.anticipo.idseccion'));
					$idanticipo = $this->config->item('bp.anticipo.idarticulo');
					$anticipo = array(
						'nIdLibro' 		=> $idanticipo,
						'id' 			=> $idanticipo,
						'cTitulo'		=> $this->lang->line('ANTICIPO'),
						'nIdEstado'		=> 3,
						'cEstado'		=> $this->lang->line('ANTICIPO'),
						'nCantidad'		=> -1,
						'fDescuento'	=> 0,
						'fIVA'			=> $iva,
						'fPrecio'		=> $precio,
						'fPVP'			=> $pvp,
						'fPrecio2'		=> $precio,
						'fPVP2'			=> $pvp,
						'fBase'			=> $precio,
						'fCoste'		=> 0,
						'cRefCliente'	=> $id,
						'nIdSeccion'	=> $seccion['nIdSeccion'],
						'cSeccion'		=> $seccion['cNombre'],				
					);
					$data[] = $anticipo;
				}
			}
			$this->out->data($data);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Indica el estado de la líneas del pedido
	 * @param int $status Id del estado 
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
			$this->load->model('ventas/m_pedidocliente');
			foreach($ids as $id)
			{
				if (is_numeric($id))
				{
					$upd = array(
						'nIdInformacion' => $status,
						'bAviso' => FALSE, 
						'dAviso' => null, 
						'dFechaInformacion' => time(),
					);
					$res = $this->reg->update($id, $upd);
					$linea = $this->reg->load($id);
					
					++$count;
					if (!$res)
					{
						$this->db->trans_rollback();
						$this->out->error("[{$id}]: ". $this->reg->error_message());
					}
					$link_l = format_enlace_cmd($linea['nIdLibro'], site_url('catalogo/articulo/index/' . $linea['nIdLibro']));
					$message = sprintf($this->lang->line('estado-pedido-cliente-titulo'), $linea['cInformacion'], $link_l, $linea['cTitulo']);
					$this->_add_nota(null, $linea['nIdPedido'], NOTA_INTERNA, $message, $this->m_pedidocliente->get_tablename());
				}
			}
			$this->db->trans_commit();
			$this->out->success(($count == 1)?sprintf($this->lang->line('pedido-linea-estado'), $ids[0]):sprintf($this->lang->line('pedido-lineas-estado'), $count));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Cancela un línaes de pedido de cliente
	 * @param int $id Ids de las líneas de pedido separados por ;
	 * @return MSG
	 */
	function aceptar($id = null)
	{
		$this->userauth->roleCheck($this->auth .'.add');

		$id = isset($id)?$id:$this->input->get_post('id');


		if ($id)
		{
			$ids = is_string($id)?preg_split('/\;/', $id):$id;
			$this->load->model('ventas/m_pedidocliente');
			foreach($ids as $k => $id)
			{
				if (is_numeric($id))
				{
					$res = $this->reg->aceptar($id);
					if (!$res) $this->out->error($this->reg->error_message());
		
					$data = $this->reg->load($id);
					$link = format_enlace_cmd($data['cTitulo'], site_url('catalogo/articulo/index/' . $data['nIdLibro']));
					$message = sprintf(sprintf($this->lang->line('pedido-linea-aceptada'), $link));
					$this->_add_nota(null, $data['nIdPedido'], NOTA_INTERNA, $message, $this->m_pedidocliente->get_tablename());
				}
				else
				{
					unset($ids[$k]);
				}
			}

			$this->out->success(sprintf($this->lang->line('pedido-linea-aceptada'), implode(', ', $ids)));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Cancela un línaes de pedido de cliente
	 * @param int $id Ids de las líneas de pedido separados por ;
	 * @return MSG
	 */
	function rechazar($id = null)
	{
		$this->userauth->roleCheck($this->auth .'.add');

		$id = isset($id)?$id:$this->input->get_post('id');

		if ($id)
		{
			$ids = is_string($id)?preg_split('/\;/', $id):$id;
			$this->load->model('ventas/m_pedidocliente');
			foreach($ids as $k => $id)
			{
				if (is_numeric($id))
				{
					$res = $this->reg->rechazar($id);
					if (!$res) $this->out->error($this->reg->error_message());
		
					$data = $this->reg->load($id);
					$link = format_enlace_cmd($data['cTitulo'], site_url('catalogo/articulo/index/' . $data['nIdLibro']));
					$message = sprintf(sprintf($this->lang->line('pedido-linea-rechazada'), $link));
					$this->_add_nota(null, $data['nIdPedido'], NOTA_INTERNA, $message, $this->m_pedidocliente->get_tablename());
				}
				else
				{
					unset($ids[$k]);
				}
			}

			$this->out->success(sprintf($this->lang->line('pedido-linea-rechazada'), implode(', ', $ids)));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

}

/* End of file Pedidoclientelinea.php */
/* Location: ./system/application/controllers/ventas/Pedidoclientelinea.php */