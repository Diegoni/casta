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
 * Pedido de cliente
 *
 */
class Pedidocliente extends MY_Controller
{
	/**
	 * Enlace a la página web de los artículos
	 * @var string
	 */
	var $_webpage = null;
	/**
	 * Constructor
	 *
	 * @return Pedidocliente
	 */
	function __construct()
	{
		parent::__construct('ventas.pedidocliente', 'ventas/m_pedidocliente', TRUE, 'ventas/pedidocliente.js', 'Pedido de cliente');
		$this->_webpage = $this->config->item('catalogo.webpage.url');
	}

	/**
	 * Crea un anticipo para el pedido indicado con el importe indicado
	 * @param int $id Id del pedido
	 * @param float $importe Importe del anticipo
	 * @return JSON
	 */
	function crearanticipo($id = null, $importe = null)
	{
		$this->userauth->roleCheck($this->auth .'.add');

		$id 		= isset($id)?$id:$this->input->get_post('id');
		$importe	= isset($importe)?$importe:$this->input->get_post('importe');

		if ($id && $importe)
		{
			// Comprueba que esté todo correcto
			$pedido = $this->reg->load($id);
			if (isset($pedido['nIdFactura']))
			{
				$this->out->error(sprintf($this->lang->line('anticipo-creado-error')));
			}
			// Datos del anticipo
			$iva = $this->config->item('bp.anticipo.iva');
			$precio = format_quitar_iva($importe, $iva);
			$seccion = $this->config->item('bp.anticipo.idseccion');
			$idanticipo = $this->config->item('bp.anticipo.idarticulo');

			// Crea la factura
			$this->load->model('ventas/m_factura');
			$this->load->library('Configurator');
			$pedido = $this->reg->load($id);

			$factura['nIdCliente'] 		= $pedido['nIdCliente'];
			$factura['nIdDireccion']	= $pedido['nIdDirEnv'];
			$factura['nIdCaja'] 		= $this->configurator->user('bp.tpv.caja');
			$factura['nIdSerie'] 		= $this->configurator->user('bp.tpv.serie');

			$factura['lineas'][] = array (
				'nIdLibro' 		=> $idanticipo,
				'nIdSeccion'	=> $seccion,
				'fPrecio'		=> $precio,
				'fIVA'			=> $iva,
				'cRefCliente'	=> $id,
				'nCantidad'		=> 1,
				'nEnFirme'		=> 1
			);
			$idf = $this->m_factura->insert($factura);
			if ($idf < 0)
			{
				$this->out->error($this->m_factura->error_message());
			}

			// Actualiza los datos del pedido
			$this->reg->update($id, array('nIdFactura' => $idf, 'fAnticipo' => $importe));

			// Resultado
			$link = format_enlace_cmd($idf, site_url('ventas/factura/index/' . $idf));
			$message = sprintf($this->lang->line('anticipo-creado-ok'), $link);
			$res = array(
				'success'	=> TRUE,
				'message'	=> $message,
				'id'		=> $idf
			);
			$this->_add_nota(null, $id, NOTA_INTERNA, $message);

			$this->out->send($res);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Devuelve el listado de albaranes que sirven un pedido
	 * @param int $id Id del pedido
	 * @return JSON
	 */
	function albaranes($id = null)
	{
		$this->userauth->roleCheck($this->auth .'.get_list');

		$id = isset($id)?$id:$this->input->get_post('id');

		if (is_numeric($id))
		{
			$this->load->library('Messages');
			$albaranes = $this->reg->albaranes($id);
			$data['albaranes'] = $albaranes;
			$data['id'] = $id;
			$message = $this->load->view('ventas/albaranespedido', $data, TRUE);
			
			$this->out->html_file($message, $this->lang->line('Albaranes del pedido') . ' ' . $id, 'iconoReportTab');
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));		
	}

	/**
	 * Devuelve el anticipo de un pedido
	 * @param int $id Id del pedido
	 * @return JSON
	 */
	function devolveranticipo($id = null)
	{
		$this->userauth->roleCheck($this->auth .'.add');

		$id 		= isset($id)?$id:$this->input->get_post('id');

		if ($id)
		{
			// Comprueba que esté todo correcto
			$pedido = $this->reg->load($id);
			if (!isset($pedido['nIdFactura']))
			{
				$this->out->error(sprintf($this->lang->line('anticipo-devolver-error')));
			}
			// Datos del anticipo
			$iva = $this->config->item('bp.anticipo.iva');
			$seccion = $this->config->item('bp.anticipo.idseccion');
			$idanticipo = $this->config->item('bp.anticipo.idarticulo');

			// Crea la factura
			$this->load->model('ventas/m_factura');
			$this->load->library('Configurator');
			$pedido = $this->reg->load($id);
			$precio = format_quitar_iva($pedido['fAnticipo'], $iva);

			$factura['nIdCliente'] 		= $pedido['nIdCliente'];
			$factura['nIdDireccion']	= $pedido['nIdDirEnv'];
			$factura['nIdCaja'] 		= $this->configurator->user('bp.tpv.caja');
			$factura['nIdSerie'] 		= $this->configurator->user('bp.tpv.serie');

			$factura['lineas'][] = array (
				'nIdLibro' 		=> $idanticipo,
				'nIdSeccion'	=> $seccion,
				'fPrecio'		=> $precio,
				'fIVA'			=> $iva,
				'cRefCliente'	=> $id,
				'nCantidad'		=> -1,
				'nEnFirme'		=> -1
			);
			$idf = $this->m_factura->insert($factura);
			if ($idf < 0)
			{
				$this->out->error($this->m_factura->error_message());
			}

			// Actualiza los datos del pedido
			$this->reg->update($id, array('nIdFactura' => null, 'fAnticipo' => 0));

			// Resultado
			$link = format_enlace_cmd($idf, site_url('ventas/factura/index/' . $idf));
			$message = sprintf($this->lang->line('anticipo-abonado-ok'), $link);
			$res = array(
				'success'	=> TRUE,
				'message'	=> $message,
				'id'		=> $idf
			);
			$this->_add_nota(null, $id, NOTA_INTERNA, $message);
			$this->out->send($res);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Abrir un pedido de cliente cerrado
	 * @param int $id Id del pedido
	 * @return MSG
	 */
	function abrir($id = null)
	{
		$this->userauth->roleCheck($this->auth .'.upd');

		$id = isset($id)?$id:$this->input->get_post('id');

		if ($id)
		{
			$res = $this->reg->abrir($id);
			if (!$res) $this->out->error($this->reg->error_message());
			$message = $this->lang->line('pedido-cliente-abierto');
			$this->_add_nota(null, $id, NOTA_INTERNA, $message);
			$this->out->success($message);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Cancela un pedido de cliente
	 * @param int $id Id del pedido
	 * @return MSG
	 */
	function enviado($id = null)
	{
		$this->userauth->roleCheck($this->auth .'.upd');

		$id = isset($id)?$id:$this->input->get_post('id');

		if (is_numeric($id))
		{
			$res = $this->reg->enviado($id);
			if ($res === FALSE) $this->out->error($this->reg->error_message());
			$links = array();
			foreach ($res['lineas'] as $l)
			{
				$links[] = format_enlace_cmd($l['cTitulo'], site_url('catalogo/articulo/index/' . $l['nIdLibro']));
			}
			$message = sprintf($this->lang->line('pedido-cliente-enviado'), $res['count'], implode("<br/>", $links));
			$this->_add_nota(null, $id, NOTA_INTERNA, $message);
			$this->out->success($message);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Cancela un pedido de cliente
	 * @param int $id Id del pedido
	 * @return MSG
	 */
	function cancelar($id = null)
	{
		$this->userauth->roleCheck($this->auth .'.cancelar');

		$id = isset($id)?$id:$this->input->get_post('id');

		if (is_numeric($id))
		{
			$res = $this->reg->cancelar($id);
			if ($res === FALSE) $this->out->error($this->reg->error_message());
			$message = sprintf($this->lang->line('pedido-cliente-cancelado'), $res);
			$this->_add_nota(null, $id, NOTA_INTERNA, $message);
			$this->out->success($message);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Duplica el pedido
	 * @param int $id Id del pedido
	 * @return JSON
	 */
	function duplicar($id = null)
	{
		$this->userauth->roleCheck($this->auth .'.add');

		$id = isset($id)?$id:$this->input->get_post('id');

		if ($id)
		{
			$d = $this->reg->load($id, 'lineas');
			unset($d['nIdEstado']);
			unset($d['nIdPedido']);
			foreach($d['lineas'] as $k => $v)
			{
				unset($d['lineas'][$k]['nCantidadServida']);
				unset($d['lineas'][$k]['nIdEstado']);
				unset($d['lineas'][$k]['nIdAlbaranSal']);
				unset($d['lineas'][$k]['nIdPedido']);
				unset($d['lineas'][$k]['nIdLinea']);
			}
			$id_n = $this->reg->insert($d);
			if ($id_n < 1)
			{
				$this->out->error($this->reg->error_message());
			}
			$link = format_enlace_cmd($id_n, site_url('ventas/pedidocliente/index/' . $id_n));
			$message = sprintf($this->lang->line('pedidocliente-duplicado-ok'), $id, $link);
			$res = array(
				'success'	=> TRUE,
				'message'	=> $message,
				'id'		=> $id_n
			);
			$this->_add_nota(null, $id, NOTA_INTERNA, $message);

			$this->out->send($res);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Cambia la sección de las líneas de pedido
	 * @param int $id Id del pedido
	 * @param int $ids Id de la sección
	 * @return JSON
	 */
	function cambiarseccion($id = null, $ids = null)
	{
		$this->userauth->roleCheck($this->auth .'.add');

		$id = isset($id)?$id:$this->input->get_post('id');
		$ids = isset($ids)?$ids:$this->input->get_post('ids');

		if (is_numeric($id) && is_numeric($ids))
		{
			$d = $this->reg->load($id, 'lineas');
			$this->load->model('ventas/m_pedidoclientelinea');
			$this->load->model('catalogo/m_articuloseccion');
			$this->load->model('generico/m_seccion');
			$sec = $this->m_seccion->load($ids);
			if ($sec === FALSE || $sec['bBloqueada'])
			{
				$this->out->error(sprintf($this->lang->line('albaranes-cerrar-seccion-bloqueada'), $sec['cNombre']));
			}
			$this->db->trans_begin();
			foreach($d['lineas'] as $k => $v)
			{
				if ($v['nIdEstado']  == ESTADO_LINEA_PEDIDO_CLIENTE_EN_PROCESO)
				{
					if (!$this->m_pedidoclientelinea->update($v['nIdLinea'], array('nIdSeccion' => $ids)))
					{
						$this->db->trans_rollback();
						$this->out->error($this->m_pedidoclientelinea->error_message());
					}
					// Quita stock
					$data = $this->m_articuloseccion->get(null, null, null, null, "nIdLibro={$v['nIdLibro']} AND nIdSeccion={$v['nIdSeccion']}");
					if (count($data) > 0)
					{
						if (!$this->m_articuloseccion->update($data[0]['nIdSeccionLibro'], array('nStockServir' => $data[0]['nStockServir'] - $v['nCantidad'])))
						{
							$this->db->trans_rollback();
							$this->out->error($this->m_articuloseccion->error_message());
						}
					}
					// Añade stock
					$data = $this->m_articuloseccion->get(null, null, null, null, "nIdLibro={$v['nIdLibro']} AND nIdSeccion={$ids}");
					if (count($data) > 0)
					{
						if (!$this->m_articuloseccion->update($data[0]['nIdSeccionLibro'], array('nStockServir' => $data[0]['nStockServir'] + $v['nCantidad'])))
						{
							$this->db->trans_rollback();
							$this->out->error($this->m_articuloseccion->error_message());
						}
					}
					else
					{
						// Crea sección si no existía
						$reg['nStockServir'] 	= $v['nCantidad'];
						$reg['nIdSeccion'] 	= $ids;
						$reg['nIdLibro']	 	= $v['nIdLibro'];

						if ($this->m_articuloseccion->insert($reg) < 0)
						{
							$this->db->trans_rollback();
							$this->out->error($this->m_articuloseccion->error_message());
						}

					}
				}
			}
			$this->db->trans_commit();
			$message = sprintf($this->lang->line('pedidocliente-cambiarseccion-ok'), $id, $sec['cNombre']);
			$res = array(
				'success'	=> TRUE,
				'message'	=> $message
			);
			$this->_add_nota(null, $id, NOTA_INTERNA, $message);

			$this->out->send($res);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Actualiza los precios del pedido
	 * @param int $id Id del pedido
	 * @param int $tatifa Id de la tarifa del cliente
	 * @return JSON
	 */
	function actualizarprecios($id = null, $tarifa = null)
	{
		$this->userauth->roleCheck($this->auth .'.add');
  
		$id = isset($id)?$id:$this->input->get_post('id');
		$tarifa = isset($tarifa)?$tarifa:$this->input->get_post('tarifa');

		if (is_numeric($id))
		{
			$d = $this->reg->load($id, 'lineas');
			$this->load->model('ventas/m_pedidoclientelinea');
			$this->load->model('catalogo/m_articulo');
			$this->load->model('clientes/m_cliente');
			$pd = $this->reg->load($id);
			$tarifascliente = $this->m_cliente->load($pd['nIdCliente'], 'tarifas');
			$tarifa = $tarifascliente['nIdTipoTarifa'];
			$tarifascliente = $tarifascliente['tarifas'];
			$this->db->trans_begin();
			$count = 0;
			foreach($d['lineas'] as $k => $v)
			{
				if (in_array($v['nIdEstado'], array(
					ESTADO_LINEA_PEDIDO_CLIENTE_EN_PROCESO,
					ESTADO_LINEA_PEDIDO_CLIENTE_NO_SE_PUEDE_SERVIR,
					ESTADO_LINEA_PEDIDO_CLIENTE_EN_ALBARAN,
					ESTADO_LINEA_PEDIDO_CLIENTE_SERVIDA,
					ESTADO_LINEA_PEDIDO_CLIENTE_CATALOGADA,
					ESTADO_LINEA_PEDIDO_CLIENTE_RECIBIDA
				)))
				{
					$data = $this->m_pedidoclientelinea->actualizarprecio($v['nIdLinea'], $tarifa, $tarifascliente, FALSE);

					if (!$data)
					{
						$this->db->trans_rollback();
						$this->out->error($this->m_pedidoclientelinea->error_message());
					}
					if ($data['old'] != $data['new'])
					{
						++$count;
						$link = format_enlace_cmd($data['art']['cTitulo'], site_url('catalogo/articulo/index/' . $data['art']['nIdLibro']));
						$message = sprintf(sprintf($this->lang->line('pedido-linea-cambioprecio'), $link,
						format_add_iva($data['old'], $data['art']['fIVA']), $data['old'],
						format_add_iva($data['new'], $data['art']['fIVA']), $data['new']));
						$this->_add_nota(null, $id, NOTA_INTERNA, $message);
					}
					if ($data['oldcoste'] != $data['newcoste'])
					{
						++$count;
						$link = format_enlace_cmd($data['art']['cTitulo'], site_url('catalogo/articulo/index/' . $data['art']['nIdLibro']));
						$message = sprintf(sprintf($this->lang->line('pedido-linea-cambiocoste'), $link, $data['oldcoste'],	$data['newcoste']));
						$this->_add_nota(null, $id, NOTA_INTERNA, $message);
					}
				}
			}
			$this->db->trans_commit();
			$message = sprintf($this->lang->line('pedidocliente-actualizaprecios-ok'),$count, $id);
			$res = array(
				'success'	=> TRUE,
				'message'	=> $message
			);
			//$this->_add_nota(null, $id, NOTA_INTERNA, $message);

			$this->out->send($res);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Calcula los pedidos a proveedor que se deben realizar del pedido de cliente
	 * @param int $id Id del pedido
	 * @param bool $seccion TRUE: fuerza a pedidos proveedor de la misma sección, FALSE: cualquiera
	 * @return array: errores: Líneas sin proveedor, apedir: pedidos a proveedor que se deben realizar
	 */
	protected function _pedir($id, $seccion)
	{
		$this->load->model('catalogo/m_articulo');
		$this->load->model('proveedores/m_proveedor');
		$this->load->model('compras/m_pedidoproveedor');
		$pedido = $this->reg->load($id, 'lineas');
		$apedir = array();
		$errores = array();
		// Cada línea del pedido
		foreach($pedido['lineas'] as $linea)
		{
			if ($linea['nIdEstado'] == ESTADO_LINEA_PEDIDO_CLIENTE_EN_PROCESO)
			{
				// Proveedor habitual
				$art = $this->m_articulo->load($linea['nIdLibro']);
				$idp = $this->m_articulo->get_proveedor_habitual($art);
				$linea['fPrecio'] = $art['fPrecio'];
				if (!isset($idp))
				{
					$errores[] = $art;
				}
				else
				{
					if (!isset($apedir[$idp]))
					{
						// Es un proveedor nuevo
						$apedir[$idp]['proveedor'] = $this->m_proveedor->load($idp);
						//Tiene alguna carta abierta?
					}
					if (!isset($apedir[$idp]['pedidos'][$linea['nIdSeccion']]))
					{
						$where = "ISNULL(bBloqueado, 0)=0 AND nIdProveedor={$idp} AND nIdEstado=1";
						$where.= ($seccion)?" AND nIdSeccion={$linea['nIdSeccion']}":
							" AND (nIdSeccion={$linea['nIdSeccion']} OR nIdSeccion IS NULL)";
						$ab = $this->m_pedidoproveedor->get(0, 1, null, null, $where);
						#$apedir[$idp][($seccion==0)?'pedido':$linea['nIdSeccion']] = $ab[0]['nIdPedido'];
						$apedir[$idp]['pedidos'][($seccion==0)?0:$linea['nIdSeccion']]['pedido'] = (count($ab) > 0)?$ab[0]['nIdPedido']:0;
					}
					$apedir[$idp]['pedidos'][($seccion==0)?0:$linea['nIdSeccion']]['lineas'][] = $linea;
				}
			}
		}

		return array('apedir' => $apedir, 'errores' => $errores);
	}

	/**
	 * Muestra los libros que se pedirán al proveedor de un pedido de cliente (TAREA)
	 * @param int $id Id del pedido
	 * @param bool $seccion TRUE: fuerza a pedidos proveedor de la misma sección, FALSE: cualquiera
	 * @return HTML_FILE
	 */
	function pedir_list($id = null, $seccion = null)
	{
		$this->userauth->roleCheck($this->auth .'.pedir');

		$id = isset($id)?$id:$this->input->get_post('id');
		$seccion = isset($seccion)?$seccion:$this->input->get_post('seccion');
		if (empty($seccion)) $seccion = FALSE;
		$seccion = format_tobool($seccion);

		if (is_numeric($id))
		{
			set_time_limit(0);
			$this->load->library('Messages');
			$this->load->model('generico/m_seccion');
			$this->messages->info(sprintf($this->lang->line('pedidocliente_pedido_id'), $id));

			$res = $this->_pedir($id, $seccion);
			#echo '<pre>'; print_r($res); die();
			$errores = $res['errores'];
			$apedir = $res['apedir'];

			// Libros sin proveedor
			foreach($errores as $art)
			{
				$this->messages->error(sprintf($this->lang->line('pedidocliente_pedido_no_proveedor'),
				format_enlace_cmd($art['nIdLibro'], site_url('catalogo/articulo/index/' .$art['nIdLibro'])),
				$art['cTitulo']), 1);
			}

			// Muestra el resultado
			foreach($apedir as $ped)
			{
				#var_dump($ped); continue;
				$idpr = format_enlace_cmd($ped['proveedor']['nIdProveedor'], site_url('proveedores/proveedor/index/' .$ped['proveedor']['nIdProveedor']));
				$name = format_name($ped['proveedor']['cNombre'], $ped['proveedor']['cApellido'], $ped['proveedor']['cEmpresa']);
				foreach($ped['pedidos'] as $sec => $pedidos)
				{
					if ($sec > 0)
						$data = $this->m_seccion->load($sec);

					if (empty($pedidos['pedido']))
					{
						($sec > 0)
							?$this->messages->info(sprintf($this->lang->line('pedidocliente_pedido_proveedor_nuevo_seccion'), $idpr, $name, $data['cNombre']), 1)
							:$this->messages->info(sprintf($this->lang->line('pedidocliente_pedido_proveedor_nuevo'), $idpr, $name), 1);
					}
					else
					{
						$idpd = format_enlace_cmd($pedidos['pedido'], site_url('compras/pedidoproveedor/index/' . $pedidos['pedido']));
						($sec > 0)
							?$this->messages->info(sprintf($this->lang->line('pedidocliente_pedido_proveedor_abierto_seccion'), $idpd, $idpr, $name, $data['cNombre']), 1)
							:$this->messages->info(sprintf($this->lang->line('pedidocliente_pedido_proveedor_abierto'), $idpd, $idpr, $name), 1);							
					}
					foreach($pedidos['lineas'] as $art)
					{
						$dto = $this->m_articulo->get_descuento($art['nIdLibro'], $ped['proveedor']['nIdProveedor']);
						$this->messages->info(sprintf($this->lang->line('pedidocliente_pedido_linea'),
						format_enlace_cmd($art['nIdLibro'], site_url('catalogo/articulo/index/' .$art['nIdLibro'])),
						$art['cTitulo'], $art['nCantidad'], $dto), 2);
					}
				}
			}

			if (count($errores) > 0)
			{
				$this->messages->error($this->lang->line('error-articulos-sinproveedor'));
			}

			if ((count($apedir) > 0) && (count($errores)==0))
			{
				$this->messages->info(format_button_cmd($this->lang->line('Realizar pedido ahora'), site_url("ventas/pedidocliente/pedir/{$id}/{$seccion}")), 0);
			}

			$body = $this->messages->out($this->lang->line('A Pedir'));
			#echo $body; die();
			$this->out->html_file($body, $this->lang->line('A Pedir'), 'iconoReportTab');
			//echo '<pre>'; print_r($apedir); echo '</pre>';
		}
	}

	/**
	 * Crea los pedidos al proveedor de un pedido de cliente (TAREA)
	 * @param int $id Id del pedido
	 * @param bool $seccion TRUE: fuerza a pedidos proveedor de la misma sección, FALSE: cualquiera
	 * @return HTML_FILE
	 */
	function pedir($id = null, $seccion = null)
	{
		$this->userauth->roleCheck($this->auth .'.pedir');

		$id = isset($id)?$id:$this->input->get_post('id');
		$seccion = isset($seccion)?$seccion:$this->input->get_post('seccion');
		if (empty($seccion)) $seccion = FALSE;
		$seccion = format_tobool($seccion);

		if (is_numeric($id))
		{
			set_time_limit(0);
			$this->load->library('Messages');
			$this->load->model('generico/m_seccion');

			$this->messages->info(sprintf($this->lang->line('pedidocliente_pedido_id_creando'), $id));

			$res = $this->_pedir($id, $seccion);
			$errores = $res['errores'];
			$apedir = $res['apedir'];
			foreach($errores as $art)
			{
				$this->messages->error(sprintf($this->lang->line('pedidocliente_pedido_no_proveedor'),
				format_enlace_cmd($art['nIdLibro'], site_url('catalogo/articulo/index/' .$art['nIdLibro'])),
				$art['cTitulo']), 1);
			}
			if (count($errores) > 0)
			{
				$this->messages->error($this->lang->line('error-articulos-sinproveedor'));
			}
			else
			{
				#$pedidos = array();
				$this->db->trans_begin();

				// Crea los pedidos
				foreach($apedir as $ped)
				{
					$idpr = format_enlace_cmd($ped['proveedor']['nIdProveedor'], site_url('proveedores/proveedor/index/' .$ped['proveedor']['nIdProveedor']));
					$name = format_name($ped['proveedor']['cNombre'], $ped['proveedor']['cApellido'], $ped['proveedor']['cEmpresa']);
					foreach($ped['pedidos'] as $sec => $pedidos)
					{
						if ($sec > 0)
							$data = $this->m_seccion->load($sec);

						if (empty($pedidos['pedido']))
						{
							($sec > 0)
								?$this->messages->info(sprintf($this->lang->line('pedidocliente_pedido_proveedor_nuevo_creando_seccion'), $idpr, $name, $data['cNombre']), 1)
								:$this->messages->info(sprintf($this->lang->line('pedidocliente_pedido_proveedor_nuevo_creando'), $idpr, $name), 1);
						}
						else
						{
							$idpd = format_enlace_cmd($pedidos['pedido'], site_url('compras/pedidoproveedor/index/' . $pedidos['pedido']));
							($sec > 0)
								?$this->messages->info(sprintf($this->lang->line('pedidocliente_pedido_proveedor_abierto_creando_seccion'), $idpd, $idpr, $name, $data['cNombre']), 1)
								:$this->messages->info(sprintf($this->lang->line('pedidocliente_pedido_proveedor_abierto_creando'), $idpd, $idpr, $name), 1);

						}
						$pedido = array(
							'nIdProveedor' 	=> $ped['proveedor']['nIdProveedor'],
						);
						foreach($pedidos['lineas'] as $art)
						{
							$dto = $this->m_articulo->get_descuento($art['nIdLibro'], $ped['proveedor']['nIdProveedor']);
							$this->messages->info(sprintf($this->lang->line('pedidocliente_pedido_linea'),
							format_enlace_cmd($art['nIdLibro'], site_url('catalogo/articulo/index/' .$art['nIdLibro'])),
							$art['cTitulo'], $art['nCantidad'], $dto), 2);
							$pedido['lineas'][] = array(
								'nIdLibro' 		=> $art['nIdLibro'],
								'nIdSeccion'	=> $art['nIdSeccion'],
								'nCantidad'		=> $art['nCantidad'],
								'fDescuento'	=> $dto,
								'fIVA'			=> $art['fIVA'],
								'fRecargo'		=> $art['fRecargo'],
								'fPrecio'		=> $art['fPrecio'],					
							);
						}

						if ($sec > 0)
						{
							$pedido['nIdSeccion'] = $sec;
						}

						if (empty($pedidos['pedido']))
						{
							$idpd = $this->m_pedidoproveedor->insert($pedido);
							if ($idpd < 1)
							{
								$this->db->trans_rollback();
								$this->out->error($this->m_pedidoproveedor->error_message());
							}
							$idpd = format_enlace_cmd($idpd, site_url('compras/pedidoproveedor/index/' . $idpd));
							($sec > 0)
							?$this->messages->info(sprintf($this->lang->line('pedidocliente_pedido_proveedor_creado_seccion'), $idpd, $idpr, $name, $data['cNombre']), 1)
							:$this->messages->info(sprintf($this->lang->line('pedidocliente_pedido_proveedor_creado'), $idpd, $idpr, $name), 1);
						}
						else
						{

							if (!$this->m_pedidoproveedor->update($pedidos['pedido'], $pedido))
							{
								$this->db->trans_rollback();
								$this->out->error($this->m_pedidoproveedor->error_message());
							}
						}
					}
				}
					
				$this->db->trans_commit();
			}

			$body = $this->messages->out($this->lang->line('Pedidos a proveedor de pedido de cliente'));
			#echo $body; die();
			$this->out->html_file($body, $this->lang->line('Pedidos'), 'iconoReportTab');
			//echo '<pre>'; print_r($apedir); echo '</pre>';
		}
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Controller#_pre_printer($id, $data, $css)
	 */
	protected function _pre_printer($id, &$data, &$css)
	{
		parent::_pre_printer($id, $data, $css);
		$css = $this->config->item('bp.documentos.css');
		return TRUE;
	}

	/**
	 * Realiza el pedido de un libro
	 * @param int $id Id de la sección
	 * @param int $ids Id de la sección
	 * @return JSON
	 */
	function reservar($id = null, $ids= null, $cantidad = null, $idpd = null, $idc = null, $dto = null, $ref = null)
	{
		$this->userauth->roleCheck(($this->auth .'.upd'));

		$id		= isset($id)?$id:$this->input->get_post('id');
		$ids	= isset($ids)?$ids:$this->input->get_post('ids');
		$idc	= isset($idc)?$idc:$this->input->get_post('idc');
		$idpd	= isset($idpd)?$idpd:$this->input->get_post('idpd');
		$cantidad = isset($cantidad)?$cantidad:$this->input->get_post('cantidad');
		$dto	= isset($dto)?$dto:$this->input->get_post('dto');
		$ref	= isset($ref)?$ref:$this->input->get_post('ref');

		if (is_numeric($id) && is_numeric($ids))
		{
			if (is_numeric($idc))
			{
				// Crea el pedido
				$this->load->model('ventas/m_pedidocliente');
				$this->load->model('ventas/m_pedidoclientelinea');
				$this->load->model('catalogo/m_articulo');
				$this->load->model('clientes/m_cliente');
				$cliente = $this->m_cliente->load($idc);
				$this->db->trans_begin();
				$nuevo = FALSE;
				if (!is_numeric($idpd))
				{
					// Crea el pedido
					$pedido['nIdCliente'] = $idc;
					$idpd = $this->m_pedidocliente->insert($pedido);
					if ($idpd < 1)
					{
						$this->db->trans_rollback();
						$this->out->error($this->m_pedidocliente->error_message());
					}
					$nuevo = TRUE;
				}
				$libro = $this->m_articulo->load($id);
				// Añade la línea
				$linea = array(
					'nIdPedido' 	=> $idpd,
					'nIdLibro'		=> $id,
					'nIdSeccion'	=> $ids,
					'cRefCliente' 	=> $ref,
					'fPrecio'  		=> $libro['fPrecio'],
					'fDescuento'	=> is_numeric($dto)?$dto:0,
					'fIVA'			=> $libro['fIVA'],
					'nCantidad'		=> is_numeric($cantidad)?$cantidad:1,
					'fRecargo'		=> $cliente['bRecargo']?$libro['fRecargo']:0
				);
				if ($this->m_pedidoclientelinea->insert($linea) < 1)
				{
					$this->db->trans_rollback();
					$this->out->error($this->m_pedidoclientelinea->error_message());
				}
				$this->db->trans_commit();
				$link_pd = format_enlace_cmd($idpd, site_url('ventas/pedidocliente/index/' . $idpd));
				$this->out->success(sprintf($this->lang->line($nuevo?'pedidocliente_add_pedido_nuevo':'pedidocliente_add_pedido_existente'), $link_pd));
			}
			else
			{
				$data['nIdLibro'] = $id;
				$data['nIdSeccion'] = $ids;
				$data['nCantidad'] = is_numeric($cantidad)?$cantidad:1;
				$this->_show_js('upd', 'ventas/reservar.js', $data);
			}
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Información para el envío del documento
	 * @param int $id Id del documento
	 * @return array, información para el envío
	 */
	protected function _get_profile_sender($id)
	{
		$this->load->model('clientes/m_email');
		$this->load->model('clientes/m_telefono');
		$pd = $this->reg->load($id, TRUE);
		$subject = $this->lang->line('pedidocliente-subject-email');
		$subject = str_replace('%id%', $id, $subject);
		return array(
			'perfil' 		=> PERFIL_PEDIDO,
			'emails'		=> $this->m_email,
			'faxes'			=> $this->m_telefono,
			'report_email' 	=> $this->config->item('sender.pedidocliente'),
			'report_normal' => $this->_get_report_default(),
			'report_lang'	=> (isset($pd['cliente']['cIdioma']) && trim($pd['cliente']['cIdioma'])!='')?$pd['cliente']['cIdioma']:(isset($pd['direccion'])?$pd['direccion']['cIdioma']:null),
			'subject'		=> $subject,
			'data'			=> $pd,
			'css'			=> $this->config->item('bp.documentos.css'),
			'id'			=> $pd['nIdCliente']		
		);
	}

	/**
	 * Muestra la ventana de pendientes de recibir
	 * @return FORM
	 */
	function pendienteservir()
	{
		$this->_show_form('get_list', 'ventas/pendienteservir.js', $this->lang->line('Pendientes de servir'));
	}

	/**
	 * Líneas de pedido de cliente pendiente de servir
	 *
	 * @param int $ids Id de la sección
	 * @param int $idcl Id del proveedor
	 * @param int $idl Id de la línea de pedido
	 * @param int $start Registro inicio
	 * @param int $limit Contador de registros
	 * @param string $sort Columna orden
	 * @param string $dir Dirección del orden (asc,desc)
	 * @param mixed $query Palabra clave de búsqueda
	 * @return JSON_DATA
	 */
	function get_pendienteservir($ids = null, $idl = null, $idcl = null, $pp = null, $start = null, $limit = null, $sort = null, $dir = null, $query = null)
	{
		$this->userauth->roleCheck($this->auth .'.get_list');

		$ids 	= isset($ids)?$ids:$this->input->get_post('ids');
		$idl 	= isset($idl)?$idl:$this->input->get_post('idl');
		$idcl 	= isset($idcl)?$idcl:$this->input->get_post('idcl');
		$pp 	= isset($pp)?$pp:$this->input->get_post('pp');
		$start 	= isset($start)?$start:$this->input->get_post('start');
		$limit 	= isset($limit)?$limit:$this->input->get_post('limit');
		$sort 	= isset($sort)?$sort:$this->input->get_post('sort');
		$dir 	= isset($dir)?$dir:$this->input->get_post('dir');
		$query 	= isset($query)?$query:$this->input->get_post('query');
		if (trim($query) == '') $query = null;

		$this->load->model('ventas/m_pedidoclientelineaex');
		$where = 'nIdEstado IN (' . ESTADO_LINEA_PEDIDO_CLIENTE_RECIBIDA . ', ' . ESTADO_LINEA_PEDIDO_CLIENTE_EN_PROCESO . ',' . ESTADO_LINEA_PEDIDO_CLIENTE_SERVIDA . ',' . ESTADO_LINEA_PEDIDO_CLIENTE_CATALOGADA .')';

		if ($ids) $where .= " AND (Cat_Secciones.cCodigo LIKE '{$ids}.%' OR Cat_Secciones.cCodigo LIKE '%.{$ids}.%' OR nIdSeccion={$ids})";
		if (is_numeric($idcl)) $where .= " AND (Cli_Clientes.nIdCliente={$idcl})";
		if (is_numeric($idl)) $where .= " AND (nIdLibro={$idl})";
		if ($pp == '1') $where .= " AND (Cat_Secciones_Libros.nStockRecibir > 0)";
		$data = $this->m_pedidoclientelineaex->get($start, $limit, $sort, $dir, $where, null, $query);
		$this->out->data($data, $this->m_pedidoclientelineaex->get_count());
	}

	/**
	 * Muestra las líneas de pedido de cliente de las que se va a enviar información 
	 * @param string $id Ids de las líneas separadas por ;
	 * @param string $cmpid Id del grid para refrescar 
	 * @return FORM
	 */
	function avisar($id = null, $cmpid = null)
	{
		$this->userauth->roleCheck($this->auth .'.cancelar');

		$id = isset($id) ? $id : $this->input->get_post('id');
		$cmpid = isset($cmpid) ? $cmpid : $this->input->get_post('cmpid');

		if (!empty($id)) 
		{
			$data['id']  = $id;
			$data['cmpid'] = $cmpid;
			$data['url'] = site_url('ventas/pedidocliente/get_avisos/' . $id);
			$this->_show_js('get_list', 'ventas/avisararticulo2.js', $data);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Carga la información de las líneas de pedido para enviar 
	 * @param string $id Ids de las líneas separadas por ;
	 * @return DATA
	 */
	function get_avisos($id = null) 
	{
		$this->userauth->roleCheck($this->auth . '.get_list');
		$id = isset($id) ? $id : $this->input->get_post('id');

		if (!empty($id)) 
		{
			$ids = preg_split('/;/', trim($id));
			foreach ($ids as $key => $value) 
			{
				if (!is_numeric($value)) unset($ids[$key]);
			}
			$id = implode(',', $ids);			
			$data = $this->reg->get_avisos($id);
			$this->load->model('clientes/m_email');
			$this->load->model('clientes/m_telefono');
			$this->load->library('Sender');
			
			foreach($data as $k => $v)
			{
				$data[$k]['cCliente'] = format_name($v['cNombre'], $v['cApellido'], $v['cEmpresa']);
				// Móvil o SMS?
				$emails = $this->m_email->get_list($v['nIdCliente']);
				#var_dump($emails);
				$em = $this->utils->get_profile($emails, PERFIL_GENERAL);
				#$em = null;
				if (isset($em))
				{
					$modo = $this->lang->line('EMAIL');
					$contacto = $em['text'];
				}
				else
				{
					$tel = $this->m_telefono->get_list($v['nIdCliente']);
					#var_dump($tel);
					$num = $this->sender->get_mobile($tel, PERFIL_GENERAL);
					if (isset($num))
					{
					 	$modo = $this->lang->line('SMS');
						$contacto = $num['text'];						
					}
					else
					{
						$modo = $this->lang->line('NO SE PUEDE AVISAR');
						$contacto = '';
							
					}
				}
				$data[$k]['cModo'] = $modo;
				$data[$k]['cContacto'] = $contacto;
				$data[$k]['bReservado'] = FALSE;
			}
			
			$this->out->data($data);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}


	/**
	 * Realiza el envío de la información de estado de las líneas de pedido 
	 * @param string $ids Ids de las líneas separadas por ;
	 * @param string $cmpid Id del grid para refrescar 
	 * @return MSG
	 */
	function avisar2($ids = null, $cmpid = null)
	{
		$this->userauth->roleCheck($this->auth .'.cancelar');

		$ids= isset($ids) ? $ids: $this->input->get_post('ids');
		$cmpid = isset($cmpid) ? $cmpid : $this->input->get_post('cmpid');

		if (!empty($ids))
		{
			$asig = preg_split('/;/', $ids);
			$precios = array();
			$this->load->model('ventas/m_pedidoclientelinea');
			$this->load->model('ventas/m_pedidocliente');
			$this->load->model('comunicaciones/m_sms');
			$this->obj->load->library('Emails');
			$this->load->library('SmsServer');				
			set_time_limit(0);
			$result = array();
			foreach ($asig as $k => $a)
			{
				if (trim($a) != '')
				{
					$a = preg_split('/\#\#/', $a);
					$linea = $a[0];
					$modo = $a[1];
					$contacto = $a[2];
					// Lee la línea
					$ln = $this->m_pedidoclientelinea->load($linea);
					$info = null;
					if (isset($ln['nIdInformacion']))
					{
						$info = $ln['cInformacion'] . '-' . $modo;
					} 
					elseif ($ln['nIdEstado'] == ESTADO_LINEA_PEDIDO_CLIENTE_SERVIDA) 
					{
						$info = 'aviso-reservado-' . $modo; 
					}
					elseif ($ln['nIdEstado'] == ESTADO_LINEA_PEDIDO_CLIENTE_EN_PROCESO)
					{
						$info = 'ESPERANDO STOCK-' . $modo; 							
					}
					$res = FALSE;
					if (isset ($info) && ($modo == $this->lang->line('EMAIL')))
					{
						if($this->_webpage)
						{
							$url = str_replace('%id%', $ln['nIdLibro'], $this->_webpage);
							$text = "<a href='{$url}'>{$ln['cTitulo']}</a>";
						}
						else 
						{
							$text = $ln['cTitulo'];
						}
						if (isset($info))
						{							
							$data['texto_email'] = str_replace('%t', $text, $this->lang->line($info));
							$message = $this->load->view('main/email', $data, TRUE);
							$res = $this->obj->emails->send($this->lang->line('aviso-informacion-email-subject'), $message, array($contacto), null, null, null, $this->config->item('bp.documentos.css'));
						}
					}
					elseif (isset ($info) && $modo == $this->lang->line('SMS'))
					{							
						$msg = str_replace('%t', format_title($ln['cTitulo'], 100), $this->lang->line($info));
						$sms = array(
							'cMensaje'	=> $msg,
							'cTo'		=> $contacto
						);
						$id = $data = $this->m_sms->insert($sms);						
						$res = $this->smsserver->send($contacto, $msg, $id);
						$res = TRUE;
					}
					else 
					{
						$res = $this->lang->line('no-info-no-contacto');
					}
					if ($res === TRUE)
					{
						$link_l = format_enlace_cmd($ln['cTitulo'], site_url('catalogo/articulo/index/' . $ln['nIdLibro']));
						$nota = sprintf($this->lang->line($info . '-nota'), $link_l, $modo, $contacto);
						$aviso = TRUE;
						$this->m_pedidoclientelinea->avisado($linea, $aviso);
						$this->_add_nota(null, $ln['nIdPedido'], NOTA_INTERNA, $nota, $this->m_pedidocliente->get_tablename());
						$result[] = $modo . ': ' . $ln['cTitulo'] .' -> <b>' . $contacto . '</b> : <font color="green">' . $this->lang->line('OK') . '</font>';
					}
					else
					{
						$result[] = $modo . ': ' . $ln['cTitulo'] .' -> <b>' . $contacto . '</b> : <font color="red">' . $this->lang->line('ERROR'). '</font> : ' . $res;
					}						
				}
			}
			$message = $this->lang->line('avisos-enviados') . '<br>' . implode('<br/>', $result);
			#echo $message; die(); 
			$this->out->dialog(TRUE, $message);							
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Actualiza el estado de los pedido de cliente
	 * @param int $id Id del pedido a procesar
	 * @return MSG
	 */
	function actualizar_estado($idp = null)
	{
		$idp = isset($idp) ? $idp: $this->input->get_post('idp');
		
		$this->load->library('Configurator');
		$last = (int)$this->configurator->system('albaransalida.estado.last');			
		$res = $this->reg->actualizar_estado($last, $idp);	
		$this->configurator->set_system('albaransalida.estado.last', (string)$res['last']);
		$this->out->dialog(TRUE, sprintf($this->lang->line('actualizar-estado-pedido.ok'), $res['count'], $res['act']));				
	}

	/**
	 * Añade el IVA al pedido
	 * @param int $id Id del pedido
	 * @return MSG
	 */
	function add_iva($id = null)
	{
		$this->userauth->roleCheck($this->auth .'.upd');

		$id = isset($id)?$id:$this->input->get_post('id');
		
		if (is_numeric($id))
		{
			if (!$this->reg->add_iva($id))
			{
				$this->out->error($this->reg->error_message());
			}
			$this->_add_nota(null, $id, NOTA_INTERNA, $this->lang->line('pedidocliente-iva-add-ok'));
			$this->out->success($this->lang->line('pedidocliente-iva-add-ok'));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Genera el envío del paquete
	 * @param int $id Id del pedido
	 * @param int $dia Fecha del envío. Por defecto hoy
	 * @param bool $reembolso Es un reembolso
	 * @param float $importe Valor del reembolso
	 * @param string $obs Observaciones sobre el envío
	 * @param int $bultos Número de bultos
	 * @return MSG
	 */
	function courier($id = null, $dia = null, $reembolso = null, $importe = null, $obs = null, $bultos = null)
	{
		$this->userauth->roleCheck($this->auth .'.get_list');

		$id = isset($id)?$id:$this->input->get_post('id');
		$reembolso = isset($reembolso)?$reembolso:$this->input->get_post('reembolso');
		$reembolso = format_tobool($reembolso);
		$importe = isset($importe)?$importe:$this->input->get_post('importe');
		$dia = isset($dia)?$dia:$this->input->get_post('dia');
		$obs = isset($obs)?$obs:$this->input->get_post('obs');
		$bultos = isset($bultos)?$bultos:$this->input->get_post('bultos');
		
		if (is_numeric($id))
		{
			#$this->out->success('OK COLEGA');
			$this->load->library('ASM');

			$this->load->model('ventas/m_pedidocliente');
			$this->load->model('clientes/m_direccioncliente');
			$this->load->model('clientes/m_email');
			$this->load->model('clientes/m_telefono');
			$pd = $this->m_pedidocliente->load($id, 'cliente');
			$dir = $this->m_direccioncliente->load($pd['nIdDirEnv']);
			$emails = $this->m_email->get_list($pd['nIdCliente']);
			$em = $this->utils->get_profile($emails, PERFIL_ENVIO);
			$tels = $this->m_telefono->get_list($pd['nIdCliente']);
			$tf = $this->utils->get_profile($tels, PERFIL_ENVIO);
		
			$ref = $id . substr(time(), 7);
			
			$resultado = '';
			if (!$idetq = $this->asm->enviar($ref, $dir, $pd['cliente'], $em['text'], $tf['text'], $dia, ($reembolso?$importe:null), $obs, $bultos, $resultado))
			{
				$this->out->error('<pre>' . $this->asm->get_error() . '</pre>');
			}

			$this->reg->update($id, array('cIdShipping' => $idetq));
			
			$res = $this->asm->etiqueta($idetq);
			$this->load->library('HtmlFile');
			$url = $this->htmlfile->url($res);
			$text = format_enlace_cmd($idetq, site_url('sys/codebar/etiqueta/' . $idetq));

			$msg = ($reembolso)?sprintf($this->lang->line('pedidocliente-courier-reembolso-ok'), $bultos, format_price($importe), $text, $resultado):
				sprintf($this->lang->line('pedidocliente-courier-ok'), $bultos, $text, $resultado);

			$this->_add_nota(null, $id, NOTA_INTERNA, $msg);

			$this->out->url($url, $this->lang->line('Enviar por courier'), 'iconoCourierTab');
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Genera un resumen del estado del pedido
	 * @param int $id Id del pedido
	 * @return MSG
	 */
	function resumen($id = null)
	{
		$this->userauth->roleCheck($this->auth .'.get_list');

		$id = isset($id)?$id:$this->input->get_post('id');
		
		if (is_numeric($id))
		{
			$pd = $this->reg->load($id, 'lineas');
			#var_dump($pd); die();
			$estados = array();
			foreach ($pd['lineas'] as $value)
			{
				if (!isset($estados[$value['cEstado']]))
					$estados[$value['cEstado']] = array(
						'lineas' 	=> 0,
						'unidades' 	=> 0,
						'base' 		=> 0,
						'total' 	=> 0
						);				
				++$estados[$value['cEstado']]['lineas'];
				$estados[$value['cEstado']]['unidades'] += $value['nCantidad'];
				$estados[$value['cEstado']]['base'] += $value['fBase'];
				$estados[$value['cEstado']]['total'] += $value['fTotal'];			 				
			}
			$data['estados'] = $estados;
			$data['pedido'] = $pd;
			$message = $this->load->view('ventas/resumen', $data, TRUE);
			
			$this->out->html_file($message, $this->lang->line('Resumen estado') . ' ' . $id, 'iconoReportTab');
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Importa un fichero EXCEL como pedido del cliente
	 * @param int $proveedor Id del proveedor
	 * @param string $file Fichero EXCEL de <upload> a importar
	 * @param string $filtro Ramgo EXCEL a tratar
	 * @param bool $crear TRUE: Crear el pedido, FALSE: solo analiza
	 * @param string $ref Referencia interna del cliente
	 * @param float $dto Descuento a aplicar
	 * @param int $seccion Sección de las líneas de pedido
	 */
	function importar($cliente = null, $file = null, $rango = null, $crear = null, $ref = null, $dto = null, $seccion = null, $crear_libros = null)
	{

		$this->userauth->roleCheck(($this->auth.'.add'));
		
		$file = isset($file) ? $file : $this->input->get_post('file');
		$cliente = isset($cliente)?$cliente:$this->input->get_post('cliente');
		$rango 		= isset($rango)?is_null_str($rango):$this->input->get_post('rango');
		$crear 		= isset($crear)?is_null_str($crear):$this->input->get_post('crear');
		$ref		= isset($ref)?is_null_str($ref):$this->input->get_post('ref');
		$dto		= isset($dto)?is_null_str($dto):$this->input->get_post('dto');
		$seccion	= isset($seccion)?is_null_str($seccion):$this->input->get_post('seccion');
		$crear_libros = isset($crear_libros)?is_null_str($crear_libros):$this->input->get_post('crear_libros');
		
		if (empty($file))
		{
			$this->_show_js('excel', 'concursos/excel.js', array('prv' => FALSE, 'seccion' => TRUE, 'url' => 'ventas/pedidocliente/importar'));
		}
		
		$files = preg_split('/;/', $file);
		$files = array_unique($files);
		$count = 0;
		if (isset($ref)) $ref = urldecode($ref);

		foreach ($files as $k => $file)
		{
			if (!empty($file))
			{
				$this->load->library('UploadLib');
				$file = urldecode($file);
				$name = $file;
				$file = $this->uploadlib->get_pathfile($file);
				set_time_limit(0);
	
				$this->load->library('Messages');
				$this->load->library('Importador');
	
				$crear = format_tobool($crear);
				$crear_libros = format_tobool($crear_libros);
	
				$this->db->trans_begin();
				$data = $this->importador->excel_generic($file, $rango, $crear_libros, FALSE, FALSE, $this->messages, $this->lang);
				// Crea el pedido
				$error = ($data === FALSE);
				#var_dump($creados); die();
				if ($data !== FALSE && is_numeric($cliente) && $crear)
				{
					if ((count($data['libros']) == 0))
					{
						$this->messages->error($this->lang->line('concurso_creando_pedido_nolibros'));
					}
					else
					{
						$this->load->model('clientes/m_cliente');
						$c = $this->m_cliente->load($cliente);
						$this->messages->info(sprintf($this->lang->line('concurso_creando_pedido'), format_name($c['cNombre'], $c['cApellido'], $c['cEmpresa'])));

						$dto = isset($dto)?$dto:0;
						$this->messages->info(sprintf($this->lang->line('concurso_usando_datos'), $dto, $ref));
							
						foreach($data['libros'] as $k => $l)
						{
							$data['libros'][$k]['nCantidad'] = isset($l['original']['cantidad'])?$l['original']['cantidad']:1;
							$data['libros'][$k]['fDescuento'] = isset($l['original']['descuento'])?$l['original']['descuento']:$dto;
							$data['libros'][$k]['fPrecio'] = isset($l['original']['precio'])?$l['original']['precio']:0;
							#$data['libros'][$k]['fPrecioVenta'] = isset($l['original']['pvp'])?$l['original']['pvp']:null;
						}
						$this->load->model('ventas/m_pedidocliente');
						$id = $this->importador->crear_documento_generico(TRUE, $cliente, $data['libros'], $this->m_pedidocliente, $seccion, $ref, $ref);
						if ($id === FALSE)
						{
							$this->messages->error($this->importador->get_error_message());
							$error = TRUE;
						}
						else
						{
							$link = format_enlace_cmd($id, site_url('ventas/pedidocliente/index/' . $id));
							$this->messages->info(sprintf($this->lang->line('concurso_pedido_creado'), $link));
						}
					}
				}
				else
				{
					$this->messages->warning($this->lang->line('concurso_no_crear_albaran'));
				}
				#var_dump($error); die();

				($error)?$this->db->trans_rollback():$this->db->trans_commit();
			}
		}
		$body = $this->messages->out($this->lang->line('Importar EXCEL'));
		$this->out->html_file($body, $this->lang->line('Importar EXCEL'), 'iconoConcursosImportarEXCELTab');
	}

	/**
	 * Calcula el coste de un pedido
	 * @param int $id Id de un pedido
	 * @return JSON
	 */
	function coste($id = null)
	{
		$this->userauth->roleCheck($this->auth .'.get_list');

		$id = isset($id)?$id:$this->input->get_post('id');

		if (is_numeric($id))
		{
			$ft = $this->reg->load($id, 'lineas');
			#var_dump($ft['lineas']); die();
			$t = $c = 0;
			foreach ($ft['lineas'] as $l)
			{
				$c += $l['fCoste'] * $l['nCantidad'];
				$t += $l['fBase'];
			}

			$data = array (
				'coste'	=> $c,
				'base'	=> $t,
				'id'	=> $id
				);
			$message = $this->load->view('ventas/costefactura', $data, TRUE);
			#echo $message; die();
			
			$this->out->html_file($message, $this->lang->line('Coste') . ' ' . $id, 'iconoReportTab');
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));		
	}

	/**
	 * Genera un fichero EXCEL con las líneas del pedido cliente
	 * @param  int $id Id del pedido del cliente
	 * @return FILE
	 */
	function exportar_excel($id = null)
	{
		$this->userauth->roleCheck($this->auth .'.get_list');
		$id = (int) (isset($id)?$id:$this->input->get_post('id'));
		if (is_numeric($id))
		{
			# Genera el REPORT
			$report = $this->config->item('bp.pedidocliente.excel.report');
			if (empty($report))
			{
				$this->out->error(sprintf($this->lang->line('error-no-config-report'), 'bp.pedidocliente.excel.report'));
			}
			$profile = $this->_get_profile_sender($id);
			#var_dump($profile)
			$html = $this->show_report($profile['subject'], $profile['data'], $report, null, FALSE, $profile['report_lang'], FALSE, FALSE);

			// Fichero
			$this->load->library('HtmlFile');
			$filename = time() . '.html';
			$file = $this->htmlfile->pathfile($filename);
			file_put_contents($file, $html);

			$url = site_url('sys/export/file/' . $filename . '/XLSX');

			$res = array(
				'success' 	=> TRUE,
				'message'	=> $this->lang->line('export-ok'),
				'file'		=> $filename,
				'src'		=> $url
			);

			// Respuesta
			$this->out->send($res);
			#var_dump($html); die();
			#$this->out->success(sprintf($this->lang->line('pedidoproveedor-cerrada-ok'), implode(', ', $ids)));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Convierte un pedido en presupuesto a la inversa
	 * @param int $id Id del pedido a cambiar
	 * @return MSG
	 */
	function presupuesto($id = null)
	{
		$id = isset($id) ? $id: $this->input->get_post('id');

		if (is_numeric($id))
		{
			$pd = $this->reg->load($id, 'lineas');
			#var_dump($pd); die();
			if ($pd['nIdEstado'] == ESTADO_PEDIDO_CLIENTE_CERRADO)
			{
				$this->out->error(sprintf($this->lang->line('pedido-cliente-cerrado'), $id));
				return;
			}
			$this->db->trans_begin();
			if ($pd['nIdEstado'] == ESTADO_PEDIDO_CLIENTE_EN_PROCESO)
			{
				# Estado del pedido
				$upd = array(
					'nIdEstado'	=> ESTADO_PEDIDO_CLIENTE_PRESUPUESTO
					);
				if (!$this->reg->update($id, $upd))
				{
					$this->db->trans_rollback();
					$this->out->error($this->reg->error_message());
					return;
				}

				# Estado de las líneas
				foreach ($pd['lineas'] as $linea)
				{
					$st = ESTADO_LINEA_PEDIDO_CLIENTE_PENDIENTE;
					if ($linea['nCantidadServida'] == $linea['nCantidad'])
						$st = ESTADO_LINEA_PEDIDO_CLIENTE_ACEPTADO;
					if (!$this->m_pedidoclientelinea->update($linea['nIdLinea'], array(
							'nIdEstado'	=> $st
						)))
					{
						$this->db->trans_rollback();
						$this->out->error($this->m_pedidoclientelinea->error_message());
						return;
					}
				}
				$msg = sprintf($this->lang->line('pedido-cliente-convertido-presupuesto'), $id);
			}
			if ($pd['nIdEstado'] == ESTADO_PEDIDO_CLIENTE_PRESUPUESTO)
			{
				# Estado del pedido
				$upd = array(
					'nIdEstado'	=> ESTADO_PEDIDO_CLIENTE_EN_PROCESO
					);
				if (!$this->reg->update($id, $upd))
				{
					$this->db->trans_rollback();
					$this->out->error($this->reg->error_message());
					return;
				}
				# Estado de las líneas
				foreach ($pd['lineas'] as $linea)
				{
					$st = ($linea['nIdEstado'] == ESTADO_LINEA_PEDIDO_CLIENTE_RECHAZADO)?ESTADO_LINEA_PEDIDO_CLIENTE_CANCELADO:ESTADO_LINEA_PEDIDO_CLIENTE_EN_PROCESO;
					if ($linea['nCantidadServida'] == $linea['nCantidad'])
						$st = ESTADO_LINEA_PEDIDO_CLIENTE_SERVIDA;
				
					if (!$this->m_pedidoclientelinea->update($linea['nIdLinea'], array(
							'nIdEstado'	=> $st
							)))
					{
						$this->db->trans_rollback();
						$this->out->error($this->m_pedidoclientelinea->error_message());
						return;
					}
				}
				$msg = sprintf($this->lang->line('pedido-cliente-convertido-pedido'), $id);
			}
			$this->_add_nota(null, $id, NOTA_INTERNA, $msg);
			$this->db->trans_commit();
			$this->out->success($msg);
			return;
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}
}

/* End of file Pedidocliente.php */
/* Location: ./system/application/controllers/ventas/Pedidocliente.php */