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
 * Reposición
 *
 */
class Reposicion extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Reposicion
	 */
	function __construct()
	{
		parent::__construct('compras.reposicion', 'compras/M_reposicion', TRUE, 'compras/reposicion.js', 'Reposición');
	}

	/**
	 * Devuelve un listado de libros de una sección
	 * @param date $d Fecha desde
	 * @param date $h Fecha hasta
	 * @param int $ids Id de la sección
	 * @param int $idp Id del proveedor
	 * @param int $idm Id de la materia
	 * @param int $ide Id de la editorial
	 * @param int $idl Id del libro
	 * @return JSON
	 */
	function get_list($d = null, $h = null, $ids = null, $idp = null, $idm = null, $ide = null, $idl = null)
	{
		$this->userauth->roleCheck(($this->auth .'.get_list'));

		$ids		= isset($ids)?$ids:$this->input->get_post('ids');
		$idp		= isset($idp)?$idp:$this->input->get_post('idp');
		$idm		= isset($idm)?$idm:$this->input->get_post('idm');
		$ide		= isset($ide)?$ide:$this->input->get_post('ide');
		$idl		= isset($idl)?$idl:$this->input->get_post('idl');

		$d			= isset($d)?$d:$this->input->get_post('d');
		$h			= isset($h)?$h:$this->input->get_post('h');
		$d			= to_date($d);
		$h			= to_date($h);

		if ($d && $h )
		{
			set_time_limit(0);
			$data = $this->reg->get_libros($d, $h, $ids, $idp, $idm, $ide, $idl);
			$res = TRUE;
		}
		else
		{
			$res = sprintf($this->lang->line('mensaje_faltan_datos'));
		}

		if ($res === TRUE)
		{
			$res = array(
				'success' 	=> true,
				'total_data' => $this->reg->get_count(),
				'value_data' => $data
			);
		}
		else
		{
			$res = array(
				'success' 	=> false,
				'message'	=> $res
			);
		}
		// Respuesta
		echo $this->out->send($res);
	}

	/**
	 * Devuelve los datos de las ventas de un libro
	 * @param int $id Id del libro
	 * @param int $ids Id de la sección
	 * @return JSON
	 */
	function get_datos_venta($id = null, $ids = null, $dialog = null)
	{
		$this->userauth->roleCheck(($this->auth .'.get_list'));

		$id		= isset($id)?$id:$this->input->get_post('id');
		$ids	= isset($ids)?$ids:$this->input->get_post('ids');
		$dialog	= isset($dialog)?$dialog:$this->input->get_post('dialog');
		if ($dialog !== FALSE) $dialog = format_tobool($dialog);
		$data = array();
		if ($id)
		{
			//set_time_limit(0);
			$this->load->model('catalogo/m_articulo');
			$data = $this->m_articulo->load($id, 'secciones');
			$data['proveedores'] = $this->m_articulo->get_proveedores($id);
			#var_dump($data['proveedores']); die();
			if (is_numeric($ids))
			{
				//Ventas secciones
				$data['semana']  = $this->reg->get_ventas($id, 7, 'd', $ids);
				$data['mes'] = $this->reg->get_ventas($id, 1, 'm', $ids);
				$data['mes3'] = $this->reg->get_ventas($id, 3, 'm', $ids);
				$data['mes6'] = $this->reg->get_ventas($id, 6, 'm', $ids);
				$data['mes12'] = $this->reg->get_ventas($id, 12, 'm', $ids);
				$data['mes24'] = $this->reg->get_ventas($id, 24, 'm', $ids);
			}
			//Ventas totales
			$data['t_semana']  = $this->reg->get_ventas($id, 7, 'd');
			$data['t_mes'] = $this->reg->get_ventas($id, 1, 'm');
			$data['t_mes3'] = $this->reg->get_ventas($id, 3, 'm');
			$data['t_mes6'] = $this->reg->get_ventas($id, 6, 'm');
			$data['t_mes12'] = $this->reg->get_ventas($id, 12, 'm');
			$data['t_mes24'] = $this->reg->get_ventas($id, 24, 'm');

			$data['ult_docs_general'] = $this->m_articulo->get_last_docs($id);
			$data['idseccion'] = is_numeric($ids)?$ids:null;
			if (is_numeric($ids)) $data['ult_docs'] = $this->m_articulo->get_last_docs($id, $ids);

			//Vista
			$res = TRUE;
			$message = $this->load->view('compras/datos_venta', $data, TRUE);
		}
		else
		{
			$res = FALSE;
			$message = sprintf($this->lang->line('mensaje_faltan_datos'));
		}

		if ($dialog)
		{
			($res)?$this->out->lightbox($message):$this->out->error($message);
		}

		$res = array(
			'success' 	=> $res,
			'message' 	=> $message,
			'data'		=> $data
		);
		// Respuesta
		echo $this->out->send($res);
	}

	/**
	 * Marca los albaranes y líneas de movimiento como vistas
	 * @param int $idl Id del libro
	 * @param int $ids Id de la sección
	 * @param int $minmov Id del primer movimiento
	 * @param int $maxmov Id del último movimiento
	 * @param int $minalb Id de la primera línea de albarán
	 * @param int $maxalb Id de la última línea de albarán
	 * @return JSON
	 */
	function marcar($idl = null, $ids = null, $minmov = null, $maxmov = null, $minalb = null, $maxalb = null)
	{
		$this->userauth->roleCheck(($this->auth .'.pedir'));

		$idl	= isset($idl)?$idl:$this->input->get_post('idl');
		$ids	= isset($ids)?$ids:$this->input->get_post('ids');
		$minmov	= isset($minmov)?$minmov:$this->input->get_post('minmov');
		$maxmov	= isset($maxmov)?$maxmov:$this->input->get_post('maxmov');
		$minalb	= isset($minalb)?$minalb:$this->input->get_post('minalb');
		$maxalb	= isset($maxalb)?$maxalb:$this->input->get_post('maxalb');
		if ($idl && $ids)
		{
			$count = $this->reg->marcar($idl, $ids, $minmov, $maxmov, $minalb, $maxalb);
			$this->out->success(sprintf($this->lang->line('reposicion_lineas_marcadas'), $count));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Realiza el pedido de un libro
	 * @param int $id Id de la sección
	 * @param int $ids Id de la sección
	 * @return JSON
	 */
	function pedir_uno($id = null, $ids= null, $cantidad = null)
	{
		$this->userauth->roleCheck(($this->auth .'.pedir'));

		$id		= isset($id)?$id:$this->input->get_post('id');
		$ids	= isset($ids)?$ids:$this->input->get_post('ids');
		$cantidad = isset($cantidad)?$cantidad:$this->input->get_post('cantidad');

		if (is_numeric($id) && is_numeric($ids))
		{
			$data['nIdLibro'] = $id;
			$data['nIdSeccion'] = $ids;
			$data['nCantidad'] = is_numeric($cantidad)?$cantidad:1;
			$this->_show_js('pedir', 'compras/compraruno.js', $data);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Realiza el pedido del libro indicado
	 * @param int $id Id de la sección
	 * @param int $ids Id de la sección
	 * @param int $qt Cantidad
	 * @param bool $dp TRUE: depoósito, FALSE: firme
	 * @param int $idpd Id del pedido
	 * @param int $idp Id del proveedor
	 * @return JSON
	 */
	function pedir($id = null, $ids = null, $qt = null, $dp = null, $idpd = null, $idp = null, $ref = null)
	{
		$this->userauth->roleCheck(($this->auth .'.pedir'));

		$id		= isset($id)?$id:$this->input->get_post('id');
		$dp		= isset($dp)?$dp:$this->input->get_post('dp');
		$idpd	= isset($idpd)?$idpd:$this->input->get_post('idpd');
		$idp	= isset($idp)?$idp:$this->input->get_post('idp');
		$ids	= isset($ids)?$ids:$this->input->get_post('ids');
		$qt		= isset($qt)?$qt:$this->input->get_post('qt');
		$ref	= isset($ref)?$ref:$this->input->get_post('ref');

		if ($idp == '' ) $idp = null;
		if ($idpd == '' ) $idpd = null;
		if ($ids == '' ) $ids = null;
		if (empty($ref)) $ref = null;
		
		// Cantidad POSITIVA
		if ($id && ($qt > 0) && $ids)
		{
			$final = FALSE;
			$nuevo = FALSE;
			$this->load->model('compras/m_pedidoproveedor');
			$this->load->model('compras/m_pedidoproveedorlinea');
			// Hay pedido?
			if (!$idpd)
			{
				// Hay proveedor
				if (!$idp)
				{
					$this->load->model('catalogo/m_articulo');
					$libro = $this->m_articulo->load($id);
					$idp = $this->m_articulo->get_proveedor_habitual($libro);
				}
				if (!$idp)
				{
					$this->out->error($this->lang->line('mensaje_no_proveedor'));
				}
				// Crea el pedido
				unset($pedido);
				$pedido['nIdProveedor'] = $idp;
				$pedido['bDeposito'] = $dp;
				$pedido['cRefProveedor'] = $pedido['cRefInterna'] = $ref;
				#print '<pre>'; print_r($pedido); print '</pre>';
				$idpd = $this->m_pedidoproveedor->insert($pedido);
				if ($idpd < 1)
				{
					$this->out->error($this->m_pedidoproveedor->error_message());
				}
				#print 'nuevo pd ' . $idpd;
				$nuevo = TRUE;
			}
			$pedido = $this->m_pedidoproveedor->load($idpd, array('lineas','proveedor'));
			// Comprueba si hay pedidos del mismo libro y sección
			foreach ($pedido['lineas'] as $linea)
			{
				if (($linea['nIdSeccion'] == $ids) && ($linea['nIdLibro'] == $id) && ($linea['nIdEstado'] == 1))
				{
					#$l2['nIdLinea'] = $linea['nIdLinea'];
					#$l2['nCantidad'] = $linea['nCantidad'] + $qt;
					if (!$this->m_pedidoproveedorlinea->update($linea['nIdLinea'], array('nCantidad' => $linea['nCantidad'] + $qt)))
					{
						$this->out->error($this->m_pedidoproveedorlinea->error_message());
					}
					$final = TRUE;
					break;
				}
			}
			// No existe la línea, se crea
			if (!$final)
			{
				#echo 'NUEVO';
				if (!isset($libro))
				{
					$this->load->model('catalogo/m_articulo');
					$libro = $this->m_articulo->load($id);
				}

				$l2 = array();
				$l2['nCantidad'] = $qt;
				$l2['nIdSeccion'] = $ids;
				$l2['nIdLibro'] = $id;
				$l2['fPrecio'] = $libro['fPrecio'];
				$l2['fIVA'] = $libro['fIVA'];
				$l2['fRecargo'] = ($pedido['proveedor']['bRecargo']==1)?$libro['fRecargo']:0;;
				$l2['fDescuento'] = $this->m_articulo->get_descuento($id, $idp);
				$l2['nIdPedido'] = $idpd;
				$l2['cRefProveedor'] = $l2['cRefInterna'] = $ref;
				if ($this->m_pedidoproveedorlinea->insert($l2) <= 0)
				{
					$this->out->error($this->m_pedidoproveedorlinea->error_message());
				}
				#$final = TRUE;
			}
			//$update['lineas'][] = $l2;
			// Enlances
			$link_pv = format_enlace_cmd($pedido['cProveedor'], site_url('proveedores/proveedor/index/' . $pedido['nIdProveedor']));
			$link_pd = format_enlace_cmd($idpd, site_url('compras/pedidoproveedor/index/' . $idpd));

			$this->out->success(sprintf($this->lang->line($nuevo?'reposicion_add_pedido_nuevo':'reposicion_add_pedido_existente'), $link_pd, $link_pv));
		}

		// CANTIDAD NEGATIVA
		if ($id && ($qt < 0) && $ids && $idpd)
		{
			$this->load->model('compras/m_pedidoproveedor');
			$pedido = $this->m_pedidoproveedor->load($idpd, 'lineas');
			$count = 0;
			$qt = -$qt;
			// Comprueba si hay pedidos del mismo libro y sección
			foreach ($pedido['lineas'] as $linea)
			{
				if (($linea['nIdSeccion'] == $ids) && ($linea['nIdLibro'] == $id) && ($linea['nIdEstado'] == 1))
				{
					$d = min($qt, $linea['nCantidad']);
					$l2['nCantidad'] = $linea['nCantidad'] - $d;
					$count += $d;
					$qt -= $d;
					$update = array();
					$update['lineas'][] = $l2;
					if ($l2['nCantidad'] == 0)
					{
						if (!$this->m_pedidoproveedorlinea->delete($linea['nIdLinea']))
						{
							$this->out->error($this->m_pedidoproveedorlinea->error_message());
						}
					}
					else
					{
						if (!$this->m_pedidoproveedorlinea->update($linea['nIdLinea'], $l2))
						{
							$this->out->error($this->m_pedidoproveedorlinea->error_message());
						}
					}
					if ($qt==0)	break;
				}
			}
			$this->out->success(sprintf($this->lang->line('reposicion_del_lineas_pedido'), $count, $idpd));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}
}

/* End of file reposicion.php */
/* Location: ./system/application/controllers/compras/reposicion.php */