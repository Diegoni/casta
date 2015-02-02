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
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Devolucones
 *
 */
class Devolucion extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Devolucion
	 */
	function __construct()
	{
		parent::__construct('compras.devolucion', 'compras/M_devolucion', TRUE, 'compras/devolucion.js', 'Devoluciones');
	}

	/**
	 * Crea una devolución con el contenido de otra rechazado
	 * @param int $id Id de la devolución
	 * @return JSON
	 */
	function contra($idd = null)
	{
		$this->userauth->roleCheck($this->auth . '.contra');

		$idd = isset($idd) ? $idd : $this->input->get_post('idd');

		if ($idd)
		{
			$id_n = $this->reg->contra($idd);
			if ($id_n < 1)
			{
				$this->out->error($this->reg->error_message());
			}
			$this->out->success(sprintf($this->lang->line('devolucion-contra-ok'), $id_n));
		}
		else
		{
			$data['title'] = $this->lang->line('Rechazar devolución');
			$data['url'] = site_url('compras/devolucion/contra');
			$this->_show_js('contra', 'compras/selectdevolucion.js', $data);
		}
	}

	/**
	 * Rechaza las línes de devolución
	 * @param int $ids Ids de las líneas en el formato Id|Cantidad;Id|Cantidad; ....
	 * @return JSON
	 */
	function rechazar($ids = null, $motivo = null)
	{
		$this->userauth->roleCheck($this->auth . '.contra');

		$ids = isset($idd) ? $ids : $this->input->get_post('ids');
		$motivo = isset($motivo) ? $motivo : $this->input->get_post('motivo');
		if ($ids)
		{
			$this->load->model('compras/m_devolucionlinea');
			$ids = is_string($ids)?preg_split('/\;/', $ids):$ids;
			$count = 0;
			$dev = null;
			$iddev = null;

			$this->db->trans_begin();
			$arts = array();
			foreach($ids as $parts)
			{
				if (trim($parts) != '')
				{
					$parts = preg_split('/\|/', $parts);
					$id = $parts[0];
					$cantidad = $parts[1];

					$linea = $this->m_devolucionlinea->load($id);
					// Lea la devolución
					if (!isset($dev))
					{
						$dev = $this->reg->load($linea['nIdDevolucion']);
						$iddev = $dev['nIdDevolucion'];
						unset($dev['nIdDevolucion']);
						unset($dev['nIdEstado']);
						unset($dev['dCierre']);
						unset($dev['dEntrega']);
						$dev['cRefInterna'] = $motivo;
					}
					// Añade la línea
					$n['nIdLibro'] 		= $linea['nIdLibro'];
					$n['nIdSeccion'] 	= $linea['nIdSeccion'];
					$n['fPrecio'] 		= $linea['fPrecio'];
					$n['fDescuento'] 	= $linea['fDescuento'];
					$n['fIVA'] 			= $linea['fIVA'];
					$n['fRecargo'] 		= $linea['fRecargo'];
					$n['nCantidad'] 	= -$cantidad;
					$n['nIdLineaDevolucion'] = $id;
					$dev['lineas'][] 	= $n;
					$arts[] = array('nIdLibro' => $linea['nIdLibro'], 'nCantidad' => $cantidad, 'cTitulo' => $linea['cTitulo']);

					// Actualiza rechazadas
					if (!$this->m_devolucionlinea->update($id, array('nRechazadas' => $linea['nRechazadas'] + $cantidad)))
					{
						$this->db->trans_rollback();
						$this->out->error($this->m_devolucionlinea->error_message());
					}
				}
			}
			$id_n = $this->reg->insert($dev);
			if ($id_n < 0)
			{
				$this->db->trans_rollback();
				$this->out->error($this->reg->error_message());
			}
			// Cierra
			$res = $this->reg->cerrar($id_n);
			if ($res === FALSE)
			{
				$this->db->trans_rollback();
				$this->out->error($this->reg->error_message());
			}
			// Entrega
			$res = $this->reg->entregar($id_n);
			if ($res === FALSE)
			{
				$this->db->trans_rollback();
				$this->out->error($this->reg->error_message());
			}
			$this->db->trans_commit();

			$link = format_enlace_cmd($id_n, site_url('compras/devolucion/index/' . $id_n));
			foreach($arts as $l)
			{
				$link_l = format_enlace_cmd($l['cTitulo'], site_url('catalogo/articulo/index/' . $l['nIdLibro']));
				$message = sprintf($this->lang->line('devolucion-contra-art-ok'), $l['nCantidad'], $link_l, $link);
				$this->_add_nota(null, $iddev, NOTA_INTERNA, $message);
			}
			$this->_add_nota(null, $id_n, NOTA_INTERNA, $this->lang->line('devolucion-cerrada-history'));
			$this->_add_nota(null, $id_n, NOTA_INTERNA, $this->lang->line('devolucion-entregada-history'));

			$this->out->dialog(TRUE, sprintf($this->lang->line('devolucion-contra-ok'), $link));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Abre la devolució
	 * @param int $id Id de la devolución
	 * @return MSG
	 */
	function abrir($id = null)
	{
		$this->userauth->roleCheck($this->auth . '.abrir');

		$id = isset($id) ? $id : $this->input->get_post('id');

		if ($id)
		{
			if ($this->reg->abrir($id))
			{
				$this->_add_nota(null, $id, NOTA_INTERNA, $this->lang->line('devolucion-abierta-history'));
				$this->out->success(sprintf($this->lang->line('devolucion-abierta-ok'), $id));
			}
			$this->out->error($this->reg->error_message());
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Entrega la devolució
	 * @param int $id Id de la devolución
	 * @return MSG
	 */
	function entregar($id = null)
	{
		$this->userauth->roleCheck($this->auth . '.upd');

		$id = isset($id) ? $id : $this->input->get_post('id');

		if ($id)
		{
			// Comprueba que el stock sea correcto
			$errores = $this->reg->check($id, DEVOLUCION_STATUS_CERRADA);
			if ($errores === FALSE)
				$this->out->error($this->reg->error_message());

			if (count($errores) > 0)
			{
				$data['errores'] = $errores;
				$message = $this->load->view('compras/sinstock', $data, TRUE);
				$this->load->library('HtmlFile');

				$filename = $this->obj->htmlfile->create($message, $this->lang->line('Devolución sin stock'));
				$url = $this->obj->htmlfile->url($filename);
				$url = format_enlace_url($url, $this->lang->line('Devolución sin stock'), 'iconoReportTab');
				$text = sprintf($this->lang->line('devolucion-error-stock'), $url);
				$this->out->dialog(FALSE, $text);
			}

			if ($this->reg->entregar($id))
			{
				$this->_add_nota(null, $id, NOTA_INTERNA, $this->lang->line('devolucion-entregada-history'));
				$this->out->success(sprintf($this->lang->line('devolucion-entregada-ok'), $id));
			}
			$this->out->error($this->reg->error_message());
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Cierra la devolución
	 * @param int $id Id del albarán
	 * @return MSG
	 */
	function cerrar($id = null)
	{
		$this->userauth->roleCheck($this->auth . '.cerrar');
		$id = isset($id) ? $id : $this->input->get_post('id');
		if ($id)
		{
			// Comprueba que el stock sea correcto
			$errores = $this->reg->check($id);
			if ($errores === FALSE)
				$this->out->error($this->reg->error_message());
			if (count($errores) > 0)
			{
				$data['errores'] = $errores;
				$message = $this->load->view('compras/sinstock', $data, TRUE);
				$this->load->library('HtmlFile');

				$filename = $this->obj->htmlfile->create($message, $this->lang->line('Devolución sin stock'));
				$url = $this->obj->htmlfile->url($filename);
				$url = format_enlace_url($url, $this->lang->line('Devolución sin stock'), 'iconoReportTab');
				$text = sprintf($this->lang->line('devolucion-error-stock'), $url);
				$this->out->dialog(FALSE, $text);
			}

			// La cierra
			$res = $this->reg->cerrar($id);
			if ($res === FALSE)	$this->out->error($this->reg->error_message());
			$this->_add_nota(null, $id, NOTA_INTERNA, $this->lang->line('devolucion-cerrada-history'));
			$this->out->success(sprintf($this->lang->line('devolucion-cerrada-ok'), $id));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Devuelve todos los libros de una sección
	 * HTML
	 *
	 * @param int $id Sección
	 */
	function devolver_seccion($ids = null, $crear = null, $habitual = null, $task = null)
	{
		$this->userauth->roleCheck(($this->auth . '.devolver_seccion'));
		$ids = isset($ids) ? $ids : $this->input->get_post('ids');
		$crear = isset($crear) ? $crear : $this->input->get_post('crear');
		$habitual = isset($habitual) ? $habitual : $this->input->get_post('habitual');

		if ($habitual === FALSE) $habitual = 0;
		$habitual = format_tobool($habitual);
		$task = isset($task)?$task:$this->input->get_post('task');
		$crear = isset($crear) ? format_tobool($crear) : FALSE;

		if ($task === FALSE) $task = 1;

		if ($ids)
		{
			set_time_limit(0);
			$data = $this->reg->devolver_seccion($ids, $habitual);
			if ($data === FALSE) $this->out->error($this->reg->error_message());
			//Creamos los pedidos
			$pedidos = array();
			if ($crear == 1)
			{
				foreach ($data as $pedido)
				{
					$idp = $this->reg->insert($pedido);
					if ($idp < 0)
					{
						show_error($this->reg->error_message());
						return;
					}
					$pedido['id'] = $idp;
					$pedidos[] = $pedido;
				}
				$data = array(
                	'pedidos' => $pedidos
				);
			}
			else
			{
				$data = array(
                	'pedidos' => $data
				);
			}

			$body = $this->load->view('compras/devolverseccion', $data, true);
			$this->out->html_file($body, $this->lang->line('Devolver Libros Sección'), 'iconoReportTab');
		}
		else
		{
			$this->_show_js('devolver_seccion', 'compras/devolverseccion.js');
		}
	}

	/**
	 * Crea una tarea que genera un listado con los artículos sin venta en una sección
	 * @param int $ids Id de la sección
	 * @param sting $tipo Tipo de stock
	 * @param string $orden Orden del listado
	 * @param bool $task Se ejecuta como tarea
	 * @return MSG
	 */
	function libros_sin_venta($ids = null, $tipo = null, $orden = null, $nacional = null, $task = null)
	{
		$this->userauth->roleCheck($this->auth . '.libros_sin_venta');
		$ids = isset($ids) ? $ids : $this->input->get_post('ids');
		$tipo = isset($tipo) ? $tipo : $this->input->get_post('tipo');
		$orden = isset($orden) ? $orden : $this->input->get_post('orden');		
		$task = isset($task)?$task:$this->input->get_post('task');
		$nacional = isset($nacional)?$nacional:$this->input->get_post('nacional');

		if ($task === FALSE) $task = 1;
		
		if ($ids)
		{
			$fecha = (!isset($fecha) || ($fecha == '')) ? $fecha = time() : to_date($fecha);
			$orden = urldecode($orden);
			#var_dump($orden); die();
			if (!is_string($orden) || $orden === FALSE || $orden === 0 || $orden =='') $orden = 'cTitulo';
			if ($task == 1)
			{
				$this->load->library('tasks');
				if (!is_numeric($ids)) $ids = 'null';
				$orden = urlencode($orden);
				$cmd = site_url("compras/devolucion/libros_sin_venta/{$ids}/{$tipo}/{$orden}/{$nacional}/0");
				$this->tasks->add2($this->lang->line('Libros sin ventas por antiguedad'), $cmd);
			}
			else
			{
				if ($tipo == '') $tipo = null;
				if (isset($orden)) $orden = urldecode($orden);
				if (!isset($orden) || ($orden == '')) $orden = 'cTitulo';
				$nacional = format_tobool($nacional);

				if (isset($runner))
				{
					$this->userauth->set_username($runner);
				}

				set_time_limit(0);
				$datos = $this->reg->libros_sin_venta($ids, $tipo, $orden, $nacional);

				$this->load->model('generico/m_seccion');
				$seccion = $this->m_seccion->load($ids);

				$data['titulos'] = $datos;
				$data['seccion'] = $seccion;
				$data['title'] = $this->lang->line('Libros sin ventas por antiguedad' . $tipo);

				$body = $this->load->view('compras/librossinventa', $data, true);

				$this->out->html_file($body, $this->lang->line('Libros sin ventas por antiguedad') . ' ' . $ids, 'iconoReportTab');
			}
		}
		else
		{
			$this->_show_js('libros_sin_venta', 'compras/librossinventa.js');
		}
	}

	/**
	 * Devoluciones a entregar. Formulario
	 * @return FORM
	 */
	function a_entregar()
	{
		$this->userauth->roleCheck($this->auth . '.get_list');
		$this->_show_js('a-entregar', 'compras/aentregar.js');
	}
	
	/**
	 * Devoluciones a entregar
	 * @param int @ids ID de la sección
	 * @param int $pv ID del proveedor
	 * @param bool $devolucion Mostrar las devoluciones 
	 * @param bool $lineas Mostrar las líneas de devolución
	 * @return DATA
	 */
	function a_entregar2($ids = null, $pv = null, $devolucion = null, $lineas = null)
	{
		$this->userauth->roleCheck($this->auth . '.get_list');
		
		$devolucion	= isset($devolucion) ? $devolucion : $this->input->get_post('devolucion');
		$lineas	= isset($lineas) ? $lineas : $this->input->get_post('lineas' );
		$ids = isset($ids) ? $ids : $this->input->get_post('ids');
		$pv = isset($pv) ? $pv : $this->input->get_post('pv');
		
		set_time_limit(0);
		$data = $this->reg->a_entregar($ids, $pv);
		$devolucion = format_tobool($devolucion);
		$vlineas = format_tobool($lineas);
		if ($vlineas)
		{
			$lineas = array();
			foreach($data['lineas'] as $linea)
			{
				$lineas[$linea['codigo']][] = $linea;
			}
			$data['lineas'] = $lineas;
		}
		if (!$devolucion) unset($data['devoluciones']);
		if (!$lineas) unset($data['lineas']);
		$body = $this->load->view('compras/aentregar', $data, TRUE);
		$this->out->html_file($body, $this->lang->line('a-entregar'), 'iconoReportTab');
	}

	/**
	 * Crea una tarea que genera un listado con los artículos sin venta en una sección
	 * @param int $ids Id de la sección
	 * @param sting $tipo Tipo de stock
	 * @param string $orden Orden del listado
	 * @return MSG
	 */
	function libros_sin_venta2($ids = null, $idp = null, $desde = null, $qty = null, $idm = null, $orden = null, $task = null)
	{
		$this->userauth->roleCheck($this->auth . '.libros_sin_venta');

		$ids 	= isset($ids) ? $ids : $this->input->get_post('ids');
		$idp 	= isset($idp) ? $idp : $this->input->get_post('idp');
		$idm 	= isset($idm) ? $idm : $this->input->get_post('idm');
		$qty 	= isset($qty) ? $qty : $this->input->get_post('qty');
		$desde 	= isset($desde) ? $desde: $this->input->get_post('desde');
		$orden 	= isset($orden) ? $orden : $this->input->get_post('orden');
		$task 	= isset($task)?$task:$this->input->get_post('task');

		if ($task === FALSE) $task = 1;
		if ($orden == '') $orden = 'cTitulo';
		$orden = urldecode($orden);
		if ($ids && $desde)
		{
			if ($task == 1)
			{
				$this->load->library('tasks');
				if (!is_numeric($ids)) $ids = 'null';
				if (!is_numeric($idp)) $idp = 'null';
				if (!is_numeric($idm)) $idm = 'null';
				if (!is_numeric($qty)) $qty = 1;
				$desde = str_replace('/', '-', $desde);
				$orden=urlencode($orden);
				$cmd = site_url("compras/devolucion/libros_sin_venta2/{$ids}/{$idp}/$desde/{$qty}/{$idm}/{$orden}/0");
				$this->tasks->add2($this->lang->line('Libros sin venta'), $cmd);
			}
			else
			{
				$desde = to_date($desde);
				#$orden = urlencode($orden);

				set_time_limit(0);
				$datos = $this->reg->libros_sin_venta2(($ids=='null')?null:$ids, ($idp=='null')?null:$idp, $desde, $qty, ($idm=='null')?null:$idm, $orden);

				#echo '<pre>'; print_r($datos); print '</pre>'; die();
				$this->load->model('generico/m_seccion');
				$seccion = $this->m_seccion->load($ids);

				$data['titulos'] = $datos;
				$data['seccion'] = $seccion;
				$data['entrada'] = TRUE;
				$data['title'] = $this->lang->line('Libros sin ventas');

				$body = $this->load->view('compras/librossinventa', $data, true);

				$this->out->html_file($body, $this->lang->line('Libros sin ventas') . ' ' . $ids, 'iconoReportTab');
			}
		}
		else
		{
			$data['url'] = 'compras/devolucion/libros_sin_venta2';
			$data['title'] = $this->lang->line('Libros sin ventas');
			$this->_show_js('libros_sin_venta', 'compras/librossinventas2.js');
		}
	}

	/**
	 * Crea una tarea que genera un listado con los artículos sin venta en una sección
	 * @param int $ids Id de la sección
	 * @param sting $tipo Tipo de stock
	 * @param string $orden Orden del listado
	 * @return MSG
	 */
	function libros_sin_venta3($ids = null, $idp = null, $desde = null, $qty = null, $idm = null, $orden = null, $task = null)
	{
		$this->userauth->roleCheck($this->auth . '.libros_sin_venta');

		$ids 	= isset($ids) ? $ids : $this->input->get_post('ids');
		$idp 	= isset($idp) ? $idp : $this->input->get_post('idp');
		$idm 	= isset($idm) ? $idm : $this->input->get_post('idm');
		$orden 	= isset($orden) ? $orden : $this->input->get_post('orden');
		$task 	= isset($task)?$task:$this->input->get_post('task');

		if ($task === FALSE) $task = 1;
		if ($orden == '') $orden = 'cTitulo';
		$orden = urldecode($orden);
		if ($ids)
		{
			/*if ($task == 1)
			{
				$this->load->library('tasks');
				if (!is_numeric($ids)) $ids = 'null';
				if (!is_numeric($idp)) $idp = 'null';
				if (!is_numeric($idm)) $idm = 'null';
				if (!is_numeric($qty)) $qty = 1;
				$desde = str_replace('/', '-', $desde);
				$orden=urlencode($orden);
				$cmd = site_url("compras/devolucion/libros_sin_venta3/{$ids}/{$idp}/$desde/{$qty}/{$idm}/{$orden}/0");
				$this->tasks->add2($this->lang->line('Libros de más de 1 año, stock > 1'), $cmd);
			}
			else*/
			{

				set_time_limit(0);
				$datos = $this->reg->libros_sin_venta3(($ids=='null')?null:$ids, ($idp=='null')?null:$idp, ($idm=='null')?null:$idm, $orden);

				#echo '<pre>'; print_r($datos); print '</pre>'; die();
				$this->load->model('generico/m_seccion');
				$seccion = $this->m_seccion->load($ids);

				$data['titulos'] = $datos;
				$data['seccion'] = $seccion;
				$data['entrada'] = TRUE;
				$data['salida'] = TRUE;
				$data['title'] = $this->lang->line('Libros de más de 1 año, stock > 1');

				$body = $this->load->view('compras/librossinventa', $data, true);

				$this->out->html_file($body, $this->lang->line('Libros de más de 1 año, stock > 1') . ' ' . $ids, 'iconoReportTab');
			}
		}
		else
		{
			$this->_show_js('libros_sin_venta', 'compras/librossinventas3.js');
		}
	}

	/**
	 * Información para el envío de los documentos
	 * @param int $id Id del pedido
	 * @return array, información para el envío
	 */
	protected function _get_profile_sender($id)
	{
		$this->load->model('proveedores/m_email');
		$this->load->model('proveedores/m_telefono');
		$pd = $this->reg->load($id, TRUE);
		$subject = $this->lang->line('devolucion-subject-email');
		$subject = str_replace('%id%', $id, $subject);
		return array(
			'perfil' 		=> PERFIL_DEVOLUCION,
			'emails'		=> $this->m_email,
			'faxes'			=> $this->m_telefono,
			'report_email' 	=> $this->config->item('sender.devolucion'),
			'report_normal' => $this->_get_report_default(),
			'report_lang'	=> (isset($pd['proveedor']['cIdioma']) && trim($pd['proveedor']['cIdioma'])!='')?$pd['proveedor']['cIdioma']:(isset($pd['direccion'])?$pd['direccion']['cIdioma']:null),
			'subject'		=> $subject,
			'data'			=> $pd,
			'css'			=> $this->config->item('bp.documentos.css'),
			'id'			=> $pd['nIdProveedor']		
		);
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

			$this->load->model('compras/m_devolucion');
			$this->load->model('proveedores/m_direccion');
			$this->load->model('proveedores/m_email');
			$this->load->model('proveedores/m_telefono');
			$pd = $this->m_devolucion->load($id, 'proveedor');
			$idd = $pd['nIdDireccion'];
			$dir = $this->m_direccion->load($idd);
			if (!$dir)
				$this->out->error($this->lang->line('courier-no-hay-direccion'));

			$emails = $this->m_email->get_list($pd['nIdProveedor']);
			$em = $this->utils->get_profile($emails, PERFIL_ENVIO);
			$tels = $this->m_telefono->get_list($pd['nIdProveedor']);
			$tf = $this->utils->get_profile($tels, PERFIL_ENVIO);
		
			$ref = $id . substr(time(), 7);

			$resultado = '';
			if (!$idetq = $this->asm->enviar($ref, $dir, $pd['proveedor'], $em['text'], $tf['text'], $dia, ($reembolso?$importe:null), $obs, $bultos, $resultado))
			{
				$this->out->error($this->asm->get_error());
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

	function set_albaranentrada($idln = null, $idlnae = null)
	{
		$this->userauth->roleCheck($this->auth . '.get_list');
		$idln = isset($idln) ? $idln : $this->input->get_post('idln');
		if (is_numeric($idln) && is_numeric($idlnae))
		{
			$this->load->model('compras/m_albaranentradalinea');
			$this->load->model('compras/m_devolucionlinea');
			$l = $this->m_devolucionlinea->load($idln);
			$ct = $l['nCantidad'];
			#$old = $l[''];
			#$ae = $this->m_albaranentradalinea->load($idlnae);
			var_dump($l); die();
			if ($l)
			{
				$data = $this->m_albaranentradalinea->get(0, 10, 'dCreacion', 'DESC', "nIdLibro={$l['nIdLibro']} AND nCantidadDevuelta + {$l['nCantidad']} <= nCantidadReal AND nIdAlbaran <> {$l['nIdAlbaran']} AND nIdEstado=2");
				$this->out->data($data);
			}
			$this->out->data(array());
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));		

	}
}

/* End of file Devolucion.php */
/* Location: ./system/application/controllers/compras/devolucion.php */
