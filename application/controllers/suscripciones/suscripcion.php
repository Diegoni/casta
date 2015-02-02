<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	suscripciones
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Suscripciones
 *
 */
class Suscripcion extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Suscripcion
	 */
	function __construct()
	{
		parent::__construct('suscripciones.suscripcion', 'suscripciones/M_suscripcion', TRUE, 'suscripciones/suscripcion.js', 'Suscripciones');
	}

	/**
	 * Hook para las llamadas después de leer los datos
	 * @param int $id Id del registro
	 * @param mixed $relations Relaciones
	 * @param array $data Datos leídos
	 */
	protected function _post_get($id, $relations, &$data, $cmpid = null)
	{
		parent::_post_get($id, $relations, $data, $cmpid);
		
		$data['avisos'] = $this->reg->get_avisosrenovacion($id);
		$data['pedidosproveedor'] = $this->reg->get_pedidosproveedor($id);
		$this->load->model('catalogo/m_articulo');
		$this->load->model('proveedores/m_proveedor');
		$idp = $this->m_articulo->get_proveedor_habitual($data);
		if ($idp > 0) $data['proveedor'] = $this->m_proveedor->load($idp);
		$data['facturas'] = $this->reg->get_facturas($id);
		#var_dump($data); die();
		$data['presupuestos'] = $this->m_articulo->get_presupuestos($data['nIdRevista'], null, null, $data['nIdCliente']);
		foreach($data['presupuestos'] as $k => $pre)
		{
			$data['presupuestos'][$k] = array_merge($pre, format_calculate_importes($pre));
		}
		$this->load->model('suscripciones/m_reclamacion');		
		$data['reclamaciones'] = $this->m_reclamacion->get(NULL, NULL, 'dCreacion', 'DESC', "nIdSuscripcion={$id}");
		$data['cmpid'] = $cmpid;
		$message = $this->load->view('suscripciones/suscripcion', $data, TRUE);
		$this->load->library('HtmlFile');
		$css = array($this->config->item('bp.data.css'), array('style.css', 'main'), array('icons.css', 'main'));
		$filename = $this->obj->htmlfile->create($message, $this->lang->line('Suscripción') . ' ' . $id, $css);
		$url = $this->obj->htmlfile->url($filename);
		$data['info'] = $url;

		return TRUE;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Controller#_pre_printer($id, $data, $css)
	 */
	protected function _pre_printer($id, &$data, &$css) 
	{
		parent::_pre_printer($id, $data, $css);

		$data['avisos'] = $this->reg->get_avisosrenovacion($id);
		$data['pedidosproveedor'] = $this->reg->get_pedidosproveedor($id);
		$this->load->model('catalogo/m_articulo');
		$this->load->model('proveedores/m_proveedor');
		$idp = $this->m_articulo->get_proveedor_habitual($data);
		if ($idp > 0) $data['proveedor'] = $this->m_proveedor->load($idp);
		$data['facturas'] = $this->reg->get_facturas($id);
		$this->load->model('suscripciones/m_reclamacion');		
		$data['reclamaciones'] = $this->m_reclamacion->get(NULL, NULL, 'dCreacion', 'DESC', "nIdSuscripcion={$id}");
		
		$css = $this->config->item('bp.data.css');
		return TRUE;
	}
	
	/**
	 * Informe del estado de las suscripciones
	 * @param bool $obras TRUE: Mostrar solo obras, FALSE: mostrar todo
	 * @param bool $activas TRUE: Mostrar solo suscricpiones activas, FALSE: Mostrar todo
	 * @return MSG
	 */
	function estado($obras = null, $activas = null)
	{
		$this->userauth->roleCheck($this->auth .'.index');

		$obras = isset($obras)?$obras:$this->input->get_post('obras');
		$activas = isset($activas)?$activas:$this->input->get_post('activas');

		$cmd = site_url("suscripciones/suscripcion/estado_task/{$obras}/{$activas}");

		$this->load->library('tasks');
		$this->tasks->add2($this->lang->line('Estados de las suscripciones') , $cmd);
	}

	/**
	 * Informe de las suscripciones anticipadas
	 * @param bool $obras TRUE: Mostrar solo obras, FALSE: mostrar todo
	 * @param bool $activas TRUE: Mostrar solo suscricpiones activas, FALSE: Mostrar todo
	 * @return MSG
	 */
	function anticipadas($obras = null, $activas = null)
	{
		$this->userauth->roleCheck($this->auth .'.index');

		$obras = isset($obras)?$obras:$this->input->get_post('obras');
		$activas = isset($activas)?$activas:$this->input->get_post('activas');

		set_time_limit(0);
		if (empty($obras)) $obras = TRUE;
		if (empty($activas)) $activas = FALSE;
		$data = $this->reg->anticipadas($obras, $activas);
		#echo '<pre>'; echo array_pop($this->db->queries); die();
		$suscripciones = array();
		foreach($data as $reg)
		{
			if (!isset($suscripciones[$reg['nIdCliente']]))
			{
				$suscripciones[$reg['nIdCliente']]['cliente'] = array(
					'id' 		=>  $reg['nIdCliente'],
					'Nombre'	=> format_name($reg['cNombre'], $reg['cApellido'], $reg['cEmpresa']),
					'nif'		=> $reg['cNIF']
				);
			}
			$suscripciones[$reg['nIdCliente']]['suscripciones'][] = array_merge(
				$reg, 
				array(
					'id'			=> $reg['nIdSuscripcion'],
					'revista'		=> $reg['cTitulo'],
					'revista_id'	=> $reg['nIdLibro'],
					'renovacion'	=> $reg['dRenovacion'],
					'tipo'			=> $reg['cTipoSuscripcion'],
					'activa'		=> $reg['bActiva'],
					#'nEntradas'		=> $reg['nEntradas'],
					#'nFacturas'		=> $reg['nFacturas'],
					#'nIdUltimaFactura'		=> $reg['nIdUltimaFactura'],
					'coste'			=> $this->reg->coste($reg['nIdSuscripcion']),
					'precio'		=> $reg['fPrecio']
				)
			);
		}
		$data['clientes'] = $suscripciones;
		$data['obras'] = $obras;
		$data['activas'] = $activas;
		$this->load->helper('asset');
		$body = $this->load->view('suscripciones/suscripcionesanticipadas', $data, true);

		$datos['title'] = $this->lang->line('Suscripciones antipadas');
		$datos['body'] = $body;
		$r = $this->load->view('oltp/reports', $datos, TRUE);
		$this->out->html_file($r, $this->lang->line('Suscripciones antipadas'), 'iconoReportTab', null, TRUE);
	}

	/**
	 * Informe del estado de las suscripciones. TAREA
	 * @param bool $obras TRUE: Mostrar solo obras, FALSE: mostrar todo
	 * @param bool $activas TRUE: Mostrar solo suscricpiones activas, FALSE: Mostrar todo
	 * @return HTML_FILE
	 */
	function estado_task($obras = null, $activas = null)
	{
		$this->userauth->roleCheck($this->auth .'.index');

		$obras = isset($obras)?$obras:$this->input->get_post('obras');
		$activas = isset($activas)?$activas:$this->input->get_post('activas');
		set_time_limit(0);
		$data = $this->reg->estado($obras, $activas);
		$suscripciones = array();
		foreach($data as $reg)
		{
			if (!isset($suscripciones[$reg['nIdCliente']]))
			{
				$suscripciones[$reg['nIdCliente']]['cliente'] = array(
					'id' 		=>  $reg['nIdCliente'],
					'Nombre'	=> format_name($reg['cNombre'], $reg['cApellido'], $reg['cEmpresa']),
					'nif'		=> $reg['cNIF']
				);
			}
			$suscripciones[$reg['nIdCliente']]['suscripciones'][] = array(
				'id'			=> $reg['nIdSuscripcion'],
				'revista'		=> $reg['cTitulo'],
				'revista_id'	=> $reg['nIdLibro'],
				'renovacion'	=> $reg['dRenovacion'],
				'tipo'			=> $reg['cTipoSuscripcion'],
				'activa'		=> $reg['bActiva'],
				'coste'			=> $this->reg->coste($reg['nIdSuscripcion']),
				'precio'		=> $reg['fPrecio']
			);
		}
		$data['clientes'] = $suscripciones;
		$data['obras'] = $obras;
		$data['activas'] = $activas;
		$this->load->helper('asset');
		$body = $this->load->view('suscripciones/estadosuscripciones', $data, true);

		$datos['title'] = $this->lang->line('Estados de las suscripciones');
		$datos['body'] = $body;
		$r = $this->load->view('oltp/reports', $datos, TRUE);
		$this->out->html_file($r, $this->lang->line('Estados de las suscripciones'), 'iconoReportTab', null, TRUE);
	}

	/**
	 * Lanza una formulario de búsqueda de suscripciones
	 * @param int $revista Id de la revista
	 * @param int $cliente Id del cliente
	 * @param int $proveedor Id del proveedor
	 * @return FORM
	 */
	function buscar($revista = null, $cliente = null, $proveedor = null)
	{
		$this->userauth->roleCheck($this->auth . '.get_list');
		$revista = isset($revista)?$revista:$this->input->get_post('revista');
		$cliente = isset($cliente)?$cliente:$this->input->get_post('cliente');
		$proveedor = isset($proveedor)?$proveedor:$this->input->get_post('proveedor');
		$data['revista'] = !empty($revista)?$revista:'';
		$data['cliente'] = !empty($cliente)?$cliente:'';
		$data['proveedor'] = !empty($proveedor)?$proveedor:'';
		$this->_show_js('a-entregar', 'suscripciones/buscar.js', $data);		
	}
	
	/**
	 * Realiza las búsqueda de suscripiones según el filtro indicado
	 * @param int $revista Id de la revista
	 * @param int $cliente Id del cliente
	 * @param int $proveedor Id del proveedor
	 * @param bool $obras TRUE: Muestra solo las obras
	 * @param bool $activas TRUE: Muestra solo la activas
	 * 
	 * @return HTML
	 */
	function buscar2($revista = null, $cliente = null, $proveedor = null, $obras = null, $activas = null)
	{
		$this->userauth->roleCheck($this->auth . '.get_list');
		$revista = isset($revista)?$revista:$this->input->get_post('revista');
		$cliente = isset($cliente)?$cliente:$this->input->get_post('cliente');
		$proveedor = isset($proveedor)?$proveedor:$this->input->get_post('proveedor');
		$obras = isset($obras)?$obras:$this->input->get_post('obras');
		$activas = isset($activas)?$activas:$this->input->get_post('activas');
		set_time_limit(0);
		$obras = format_tobool($obras);
		$activas = format_tobool($activas);
		$data = $this->reg->buscar($revista, $cliente, $proveedor, $obras, $activas);
		$this->load->model('catalogo/m_articulo');
		$this->load->model('proveedores/m_proveedor');
		$suscripciones = array();
		
		foreach($data as $reg)
		{
			$sus = $this->reg->load($reg['nIdSuscripcion'], TRUE);
			$idp = $this->m_articulo->get_proveedor_habitual($sus['articulo']);
			if (isset($idp))
			{
				$sus['proveedor'] = $this->m_proveedor->load($idp);
			}
			$sus['cTipoSuscripcion'] = $reg['cTipoSuscripcion'];
			$suscripciones[] = $sus;
		}
		$data['suscripciones'] = $suscripciones; 
		$this->load->helper('asset');
		$body = $this->load->view('suscripciones/listado', $data, true);
		$this->out->html_file($body, $this->lang->line('Suscripciones'), 'iconoReportTab');
	}

	/**
	 * Activa una o varias suscripciones
	 * @param int $id Id de las suscripciones seperadas por ;
	 * @param string $contacto Persona que realiza la cancelación
	 * @param int $id Id del modo en el que ha dado la orden de cancelación
	 * @param date $fecha Fecha en la que se ha recibido la cancelación
	 * @param string $cmpid Id del componente que realiza la tarea
	 * @return FORM
	 */
	function activar($id = null, $contacto = null, $modo = null, $fecha = null, $cmpid = null)
	{
		$this->userauth->roleCheck($this->auth .'.activar');
		$id = isset($id)?$id:$this->input->get_post('id');
		$contacto = isset($contacto) ? $contacto : $this->input->get_post('contacto');
		$modo = isset($modo) ? $modo : $this->input->get_post('modo');
		$fecha = isset($fecha) ? $fecha : $this->input->get_post('fecha');
		$cmpid = isset($cmpid) ? $cmpid : $this->input->get_post('cmpid');

		if (is_numeric($id) && !empty($contacto) && is_numeric($modo))
		{			
			$fecha = (empty($fecha))?time():to_date($fecha);
			$ids = is_string($id)?preg_split('/\;/', $id):$id;
			$ids = array_unique($ids);
			$count = 0;
			$this->db->trans_begin();
			$this->load->model('suscripciones/m_mediorenovacion');
			$medio = $this->m_mediorenovacion->load($modo);
			foreach($ids as $id)
			{
				if (is_numeric($id))
				{
					# Crea la nota de activación
					$this->load->model('suscripciones/m_tiporeclamacion');
					$this->load->model('suscripciones/m_reclamacion');
					$sus = $this->reg->load($id, 'direccionenvio');

					$rec = $this->m_reclamacion->create(TIPORECLAMACION_ACTIVARSUSCRIPCION, 
						$sus['nIdCliente'], $sus['nIdProveedor'], $id, $sus);
					if (!$rec)
					{
						$this->db->trans_rollback();
						$this->out->error("[{$id}]: ". $this->m_reclamacion->error_message());
					}

					# Activa la suscripción
					if (!$this->reg->activar($id))
					{
						$this->db->trans_rollback();
						$this->out->error("[{$id}]: ". $this->reg->error_message());
					}

					$this->_add_nota(null, $id, NOTA_INTERNA, 
						sprintf($this->lang->line('suscripcion-activado-history'), 
							$medio['cDescripcion'], $contacto, format_date($fecha)));
					++$count;				
				}
			}
			$this->db->trans_commit();
			$this->out->success(sprintf($this->lang->line('suscripciones-activadas-ok'), implode(', ', $ids)));
		}
		elseif (is_numeric($id))
		{
			$data['id'] = $id;
			$data['url'] = site_url('suscripciones/suscripcion/activar');
			$data['title'] = $this->lang->line('Activar suscripción');
			$data['ref'] = FALSE;
			$data['icon'] = 'icon-accept';
			$data['cmpid'] = $cmpid;
			$this->_show_js('upd', 'suscripciones/aceptarrenovar.js', $data);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Reclama el pedido de una suscripción a un proveedor
	 * @param int $id Id de las suscripción
	 * @return DATA, id: id de la reclamación
	 */
	function reclamar_pedido($id = null)
	{
		$this->userauth->roleCheck($this->auth .'.upd');
		$id = isset($id)?$id:$this->input->get_post('id');

		if (is_numeric($id))
		{			
			# Crea la nota de activación
			$this->load->model('suscripciones/m_tiporeclamacion');
			$this->load->model('suscripciones/m_reclamacion');
			$sus = $this->reg->load($id, 'direccionenvio');

			$rec = $this->m_reclamacion->create(TIPORECLAMACION_RECLAMACIONPEDIDOPROVEEDOR, 
				$sus['nIdCliente'], $sus['nIdProveedor'], $id, $sus);
			if (!$rec)
			{
				$this->out->error("[{$id}]: ". $this->m_reclamacion->error_message());
			}
			$data = array(
				'success' => TRUE, 
				'message' => sprintf($this->lang->line('suscripciones-reclamar-factura-ok'), $rec),
				'id' => $rec
				);
			$this->out->send($data);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Reclama el pedido de una suscripción a un proveedor
	 * @param int $id Id de las suscripción
	 * @return DATA, id: id de la reclamación
	 */
	function cambio_direccion($id = null, $old_id = null, $new_id = null)
	{
		$this->userauth->roleCheck($this->auth .'.upd');
		$id = isset($id)?$id:$this->input->get_post('id');
		$old_id = isset($old_id)?$old_id:$this->input->get_post('old_id');
		$new_id = isset($new_id)?$new_id:$this->input->get_post('new_id');

		if (is_numeric($id) && is_numeric($new_id))
		{			
			# Crea la nota de activación
			$this->load->model('suscripciones/m_tiporeclamacion');
			$this->load->model('suscripciones/m_reclamacion');
			$this->load->model('clientes/m_direccioncliente');
			$sus = $this->reg->load($id);			
			$sus['cDireccionNueva'] = format_address_print($this->m_direccioncliente->load($new_id));
			if (is_numeric($old_id))
				$sus['direccionenvio'] = $this->m_direccioncliente->load($old_id);

			$rec = $this->m_reclamacion->create(TIPORECLAMACION_CAMBIODIRECCION, 
				$sus['nIdCliente'], $sus['nIdProveedor'], $id, $sus);
			if (!$rec)
			{
				$this->out->error("[{$id}]: ". $this->m_reclamacion->error_message());
			}
			$link_l = format_enlace_cmd($rec, site_url('suscripciones/reclamacion/index/' . $rec));
			$message = sprintf($this->lang->line('cambio-direccion-suscripcion-history'), $link_l, 
				isset($sus['direccionenvio'])?format_address_print($sus['direccionenvio']):'',
				$sus['cDireccionNueva']);
			$this->_add_nota(null, $id, NOTA_INTERNA, $message);
			$data = array(
				'success' => TRUE, 
				'message' => sprintf($this->lang->line('suscripciones-cambio-direccion-ok'), $rec),
				'id' => $rec
				);
			$this->out->send($data);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Reclama el pedido de una suscripción a un proveedor
	 * @param int $id Id de la reclamación
	 * @param string $texto Texto de respuesta
	 * @param string $cmpid Id del componente que realiza la tarea
	 * @return DATA, id: id de la reclamación
	 */
	function respuesta($id = null, $texto = null, $cmpid = null)
	{
		$this->userauth->roleCheck($this->auth .'.add');
		$id = isset($id)?$id:$this->input->get_post('id');

		$texto = isset($texto)?$texto:$this->input->get_post('Texto');
		$cmpid = isset($cmpid) ? $cmpid : $this->input->get_post('cmpid');

		if (is_numeric($id))
		{	
			$this->load->model('suscripciones/m_reclamacion');
			$this->load->model('suscripciones/m_tiporeclamacion');
			$original = $this->m_reclamacion->load($id);
			if (is_numeric($original['nIdReclamacionAsociada']))
			{
				$this->out->error(sprintf($this->lang->line('error-respuesta-anterior'), $original['nIdReclamacionAsociada']));
			}
			if ($original['nIdTipoReclamacion'] != TIPORECLAMACION_RECLAMACIONCLIENTE)
			{
				$this->out->error($this->lang->line('error-respuesta-no-peticion'));
			}
			if (!empty($texto))
			{
				# Crea la nota de activación
				$sus = $this->reg->load($original['nIdSuscripcion'], 'direccionenvio');
				$sus['cRespuesta'] = $texto;
				$sus['cReclamacion'] = $original['tDescripcion'];

				$this->db->trans_begin();
				$rec = $this->m_reclamacion->create(TIPORECLAMACION_RESPUESTAACLIENTE, 
					$sus['nIdCliente'], $sus['nIdProveedor'], $original['nIdSuscripcion'], $sus, $id);
				if (!$rec)
				{
					$this->db->trans_rollback();
					$this->out->error("[{$id}]: ". $this->m_reclamacion->error_message());
				}
				if (!$this->m_reclamacion->update($id, array('nIdReclamacionAsociada' => $rec)))
				{
					$this->db->trans_rollback();
					$this->out->error("[{$id}]: ". $this->m_reclamacion->error_message());					
				}
				$this->db->trans_commit();

				$data = array(
					'success' => TRUE, 
					'message' => sprintf($this->lang->line('suscripciones-respuesta-cliente-ok'), $rec),
					'id' => $rec
					);
				$this->out->send($data);
			}
			else
			{
				$data['id'] = $id;
				$data['cmpid'] = $cmpid;
				$data['title'] = $this->lang->line('Respuesta a cliente');
				$data['url'] = site_url('suscripciones/suscripcion/respuesta');
				$data['ref'] = FALSE;
				$this->_show_js('upd', 'suscripciones/reclamacioncliente.js', $data);
			}
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Reclama el pedido de una suscripción a un proveedor
	 * @param int $id Id de las suscripción
	 * @param string $volumen Volumen que se reclama
	 * @param string $texto Texto que se reclama
	 * @param string $cmpid Id del componente que realiza la tarea
	 * @return DATA, id: id de la reclamación
	 */
	function reclamar_cliente($id = null, $volumen = null, $texto = null, $cmpid = null)
	{
		$this->userauth->roleCheck($this->auth .'.upd');
		$id = isset($id)?$id:$this->input->get_post('id');
		$volumen = isset($volumen)?$volumen:$this->input->get_post('volumen');
		$texto = isset($texto)?$texto:$this->input->get_post('Texto');
		$cmpid = isset($cmpid) ? $cmpid : $this->input->get_post('cmpid');

		if (is_numeric($id))
		{	
			if (!empty($volumen) || !empty($texto))
			{
				# Crea la nota de activación
				$this->load->model('suscripciones/m_tiporeclamacion');
				$this->load->model('suscripciones/m_reclamacion');
				$sus = $this->reg->load($id, 'direccionenvio');
				$sus['cTexto'] = $texto;
				$sus['cVolumen'] = $volumen;

				$rec = $this->m_reclamacion->create(TIPORECLAMACION_RECLAMACIONCLIENTE, 
					$sus['nIdCliente'], $sus['nIdProveedor'], $id, $sus);
				if (!$rec)
				{
					$this->out->error("[{$id}]: ". $this->m_reclamacion->error_message());
				}
				$data = array(
					'success' => TRUE, 
					'message' => sprintf($this->lang->line('suscripciones-reclamar-cliente-ok'), $rec),
					'id' => $rec
					);
				$this->out->send($data);
			}
			else
			{
				$data['id'] = $id;
				$data['cmpid'] = $cmpid;
				$data['title'] = $this->lang->line('Reclamación de cliente');
				$data['url'] = site_url('suscripciones/suscripcion/reclamar_cliente');
				$data['ref'] = TRUE;
				$this->_show_js('upd', 'suscripciones/reclamacioncliente.js', $data);
			}
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Cancela una o varias suscripciones
	 * @param int $id Id de las suscripciones seperadas por ;
	 * @param string $contacto Persona que realiza la cancelación
	 * @param int $id Id del modo en el que ha dado la orden de cancelación
	 * @param date $fecha Fecha en la que se ha recibido la cancelación
	 * @param string $cmpid Id del componente que realiza la tarea
	 * @return FORM
	 */
	function cancelar($id = null, $contacto = null, $modo = null, $fecha = null, $cmpid = null)
	{
		$this->userauth->roleCheck($this->auth .'.cancelar');

		$id = isset($id)?$id:$this->input->get_post('id');
		$contacto = isset($contacto) ? $contacto : $this->input->get_post('contacto');
		$modo = isset($modo) ? $modo : $this->input->get_post('modo');
		$fecha = isset($fecha) ? $fecha : $this->input->get_post('fecha');
		$cmpid = isset($cmpid) ? $cmpid : $this->input->get_post('cmpid');

		if (is_numeric($id) && !empty($contacto) && is_numeric($modo))
		{			
			$fecha = (empty($fecha))?time():to_date($fecha);
			$ids = is_string($id)?preg_split('/\;/', $id):$id;
			$ids = array_unique($ids);
			$count = 0;
			$this->db->trans_begin();
			$this->load->model('suscripciones/m_mediorenovacion');
			$medio = $this->m_mediorenovacion->load($modo);
			foreach($ids as $id)
			{
				if (is_numeric($id))
				{
					# Si hay aviso de renovación lo cancela
					$avisos = $this->reg->get_avisosrenovacion($id, FALSE, TRUE);
					foreach ($avisos as $value) 
					{						
					}

					# Cancela los pedidos a proveedor
					$this->load->model('compras/m_pedidoproveedor');
					$this->load->model('compras/m_pedidoproveedorlinea');
					$pedidosproveedor = $this->reg->get_pedidosproveedor($id, FALSE, FALSE, TRUE);
					{
						foreach ($pedidosproveedor as $value) 
						{
							if ($value['nIdEstado'] == LINEA_PEDIDO_PROVEEDOR_STATUS_EN_PROCESO)
							{
								$msg = 'eliminar-pedido-proveedor-suscripcion';
								$res = $this->m_pedidoproveedorlinea->delete($value['nIdLinea']);
							}
							else
							{
								$mes= 'cancelacion-pedido-proveedor-suscripcion';
								$res = $this->m_pedidoproveedorlinea->cancelar($value['nIdLinea']);
							}
							if (!$res)
							{
								$this->db->trans_rollback();
								$this->out->error("[{$id}]: ". $this->m_pedidoproveedorlinea->error_message());
							}
							$link_l = format_enlace_cmd($id, site_url('suscripciones/suscripcion/index/' . $id));
							$message = sprintf($this->lang->line('cancelacion-pedido-proveedor-suscripcion'), $link_l);
							$this->_add_nota(null, $value['nIdPedido'], NOTA_INTERNA, $message, $this->m_pedidoproveedor->get_tablename());							
							$link_l = format_enlace_cmd($value['nIdPedido'], site_url('compras/pedidoproveedor/index/' . $value['nIdPedido']));
							$message = sprintf($this->lang->line('cancelacion-pedido-proveedor-suscripcion-history'), $link_l);
							$this->_add_nota(null, $id, NOTA_INTERNA, $message);
						}
					}

					# Crea la nota de cancelación
					$this->load->model('suscripciones/m_tiporeclamacion');
					$this->load->model('suscripciones/m_reclamacion');
					$sus = $this->reg->load($id, 'direccionenvio');
					$sus['cDireccionEnvio'] = format_address_print($sus['direccionenvio']);

					$rec = $this->m_reclamacion->create(TIPORECLAMACION_CANCELARSUSCRIPCION, 
						$sus['nIdCliente'], $sus['nIdProveedor'], $id, $sus);
					if (!$rec)
					{
						$this->db->trans_rollback();
						$this->out->error("[{$id}]: ". $this->m_reclamacion->error_message());
					}

					# Cancela la suscripción
					if (!$this->reg->cancelar($id))
					{
						$this->db->trans_rollback();
						$this->out->error("[{$id}]: ". $this->reg->error_message());
					}

					$this->_add_nota(null, $id, NOTA_INTERNA, 
						sprintf($this->lang->line('suscripcion-cancelado-history'), 
							$medio['cDescripcion'], $contacto, format_date($fecha)));
					++$count;				
				}
				$this->db->trans_commit();
			}
			$this->out->success(sprintf($this->lang->line('suscripciones-canceladas-ok'), implode(', ', $ids)));
		}
		elseif (is_numeric($id))
		{
			$data['id'] = $id;
			$data['url'] = site_url('suscripciones/suscripcion/cancelar');
			$data['title'] = $this->lang->line('Cancelar suscripción');
			$data['ref'] = FALSE;
			$data['icon'] = 'icon-cancel';
			$data['cmpid'] = $cmpid;
			$this->_show_js('upd', 'suscripciones/aceptarrenovar.js', $data);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Lanza una formulario de búsqueda de suscripciones
	 * @param int $id Id del cliente
	 * @return FORM
	 */
	function facturar($id = null)
	{
		$this->userauth->roleCheck($this->auth . '.get_list');
		$id = isset($id)?$id:$this->input->get_post('id');
		$data['id'] = !empty($id)?$id:'';
		$this->_show_js('get_list', 'suscripciones/facturarsus.js', $data);		
	}

	/**
	 * Resetea la condición de anticpiada
	 * @param int $id Id de la suscripción
	 * @return MSG
	 */
	function resetanticipada($id = null)
	{
		$this->userauth->roleCheck($this->auth . '.get_list');
		$id = isset($id)?$id:$this->input->get_post('id');
		if (is_numeric($id))
		{
			$this->reg->update($id, array('nEntradas' => 0, 'nFacturas' => 0));
			$this->out->success(sprintf($this->lang->line('suscripcion-resetanticipada-ok'), $id));			
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));		
	}
	
	/**
	 * Realiza las búsqueda de suscripiones según el filtro indicado
	 * @param int $revista Id de la revista
	 * @param int $cliente Id del cliente
	 * @param int $proveedor Id del proveedor
	 * @param bool $obras TRUE: Muestra solo las obras
	 * @param bool $activas TRUE: Muestra solo la activas
	 * @param string $volumen Referencia del volumen si es anticipado
	 * 
	 * @return HTML
	 */
	function get_facturas($id = null, $obras = null, $activas = null, $volumen = null)
	{
		$this->userauth->roleCheck($this->auth . '.get_list');
		$id = isset($id)?$id:$this->input->get_post('id');
		$obras = isset($obras)?$obras:$this->input->get_post('obras');
		$activas = isset($activas)?$activas:$this->input->get_post('activas');
		set_time_limit(0);
		$obras = format_tobool($obras);
		$activas = format_tobool($activas);
		$data = $this->reg->buscar(null, $id, null, $obras, $activas, TRUE);
		$this->load->model('catalogo/m_articulo');
		$this->load->model('proveedores/m_proveedor');
		$suscripciones = array();
		# Proveedor, Presupuesto, Albarán de entrada, Pedido a proveedor?
		foreach($data as $reg)
		{
			$sus = $this->reg->load($reg['nIdSuscripcion'], TRUE);
			# Proveedor
			$idp = $this->m_articulo->get_proveedor_habitual($sus['articulo']);
			if (isset($idp))
			{
				$sus['proveedor'] = $this->m_proveedor->load($idp);
			}
			# Tipo Suscripción
			$sus['cTipoSuscripcion'] = $reg['cTipoSuscripcion'];
			# Coste
			$sus['fCoste'] = $sus['fPrecioCompra'];
			$sus['fIVA'] = $sus['articulo']['fIVA'];
			$sus['nCantidad'] = $sus['nEjemplares'];
			$sus['tipo'] = $this->lang->line(($sus['revista']['nIdTipoSuscripcion'] == 5)?'OBRA':'PUBLICACION');
			# Albarán de entrada sin facturar?
			$alb = $this->reg->get_albaran_sin_facturar($reg['nIdSuscripcion']);
			if (isset($alb))
			{
				$sus = array_merge($sus, $alb);		
			}
			$sus['cTitulo'] = $sus['articulo']['cTitulo'];
			$sus['cCliente'] = format_name($sus['cliente']['cNombre'], $sus['cliente']['cApellido'], $sus['cliente']['cEmpresa']); 
			$sus['cProveedor'] = format_name($sus['proveedor']['cNombre'], $sus['proveedor']['cApellido'], $sus['proveedor']['cEmpresa']);
			unset($sus['articulo']);
			unset($sus['proveedor']);
			unset($sus['cliente']);
			unset($sus['direccionenvio']);
			unset($sus['direccionfactura']);
			unset($sus['revista']);
			$avisos = $this->reg->get_avisosrenovacion($reg['nIdSuscripcion'], TRUE);
			if (isset($avisos))
			{
				$sus['text_aviso'] = $avisos[0]['cCampana'] . ' - ' .
					$this->lang->line(isset($avisos[0]['nIdAvisoRenovacion']) ? (isset($avisos[0]['dGestionada']) ? ($avisos[0]['bAceptada'] ? 'ACEPTADA' : 'RECHAZADA') : 'SIN GESTIONAR') : 'NO HAY AVISO');
				$sus['id_aviso'] = isset($avisos[0]['nIdAvisoRenovacion']) ? (isset($avisos[0]['dGestionada']) ? ($avisos[0]['bAceptada'] ? 1 : -1) : 0) : 2;
			} 
			
			if (!isset($sus['fDescuento'])) $sus['fDescuento'] = 0;
			$totales = format_calculate_importes($sus);
			$sus['fPVP'] = $sus['fPVPAsignado'] = $totales['fTotal2']; 
			$sus['fMargen'] = format_margen($sus['fPrecio'] * (1 - $sus['fDescuento']/100), $sus['fCoste']);
			$suscripciones[] = $sus;
		}
		$this->out->data($suscripciones);
	}

	/**
	 * Crea una factura de las suscripciones
	 * @param int $id Id del albarán
	 * @param string $precios Precios a asignar a las suscripciones
	 * @param string $volumen Referencia de cada línea de factura
	 * @return MSG
	 */
	function crear_factura($id = null, $precios = null, $volumen = null)
	{
		$this->userauth->roleCheck($this->auth . '.upd');
		$id = isset($id) ? $id : $this->input->get_post('id');
		$precios = isset($precios) ? $precios : $this->input->get_post('precios');
		$volumen = isset($volumen) ? $volumen : $this->input->get_post('volumen');

		if (is_numeric($id) && !empty($precios))
		{
			# Limpiamos entrada
			$asig = preg_split('/;/', $precios);
			$precios = array();
			$ids = null;
			foreach ($asig as $k => $a)
			{
				if (trim($a) != '')
				{
					$a = preg_split('/\#\#/', $a);
					if (count($a) == 5)
					{
						$precios[$a[0]] = array(
							'fPVP' => $a[1],
							'nCantidad' => $a[4],
							'nIdAlbaran' => $a[3],
							'fDescuento' => $a[2],
							'nIdSuscripcion' => $a[0] 
						);
						$ids = $a[0];
					}
					else
					{
						$this->out->error($this->lang->line('mensaje_faltan_datos'));
					}
				}
			}
			# Creamos los albaranes anticipados
			$this->db->trans_begin();
			$new_albs = array();
			$this->load->model('suscripciones/m_suscripcion');
			$this->load->model('ventas/m_albaransalida');
			$this->load->model('ventas/m_factura');
			$seccion = $this->config->item('bp.suscripciones.seccion');
			
			#Crea una factura
			$sus = $this->m_suscripcion->load($ids);
			$datos = array(
				'nIdCliente' => $sus['nIdCliente'],
				'nIdDireccion' => $sus['nIdDireccionFactura'],
				'nIdSerie' => $this->config->item('oltp.suscripciones.serienormal'),
				'nIdCaja' => $this->config->item('bp.suscripciones.caja'),
			);
			$idf = $this->m_factura->insert($datos);
			if ($idf < 0)
			{
				$this->db->trans_rollback();
				$this->out->error($this->m_factura->error_message());
			}
			$count = 0;
			$anticipos = 0;
			foreach ($precios as $k => $v)
			{
				if (!is_numeric($v['nIdAlbaran']))
				{
					# Crea el albarán de salida anticipado
					$sus = $this->m_suscripcion->load($k, 'articulo');
					#var_dump($sus);	
					$salida = array(
						'nIdDireccion' => $sus['nIdDireccionEnvio'],
						'cRefCliente' => $sus['cRefCliente'],
						'cRefInterna' => $volumen,		
						'bNoFacturable' => FALSE,
						'nIdFactura' => $idf,	
						'nIdCliente' => $sus['nIdCliente'],
					);
					$salida['albaransalidasuscripcion'][] = array('nIdSuscripcion' => $k);
					$salida['lineas'][] = array(
						'nIdLibro' => $sus['nIdRevista'],
						'fPrecio' => format_quitar_iva($v['fPVP'], $sus['articulo']['fIVA']),
						'nIdSeccion' => $seccion,
						'nCantidad' => $v['nCantidad'],
						'cRefInterna' => $volumen,		
						'cRefCliente' => $sus['cRefCliente'],
						'fDescuento' => $v['fDescuento'],
						'fIVA' => $sus['articulo']['fIVA']
					);
					#var_dump($salida); 		
					$idsa = $this->m_albaransalida->insert($salida);
					if ($idsa < 0 )
					{
						$this->db->trans_rollback();
						$this->out->error($this->m_albaransalida->error_message());
					}
					# Lo cierra
					if (!$this->m_albaransalida->cerrar($idsa))
					{
						$this->db->trans_rollback();
						$this->out->error($this->m_albaransalida->error_message());			
					}

					# Abona el albarán de salida porque es anticipado
					$ab = $this->m_albaransalida->abonar($idsa); 
					if ($ab < 0)
					{
						$this->db->trans_rollback();
						$this->out->error($this->m_albaransalida->error_message());			
					}
					# No facturable
					if (!$this->m_albaransalida->update($ab, array('bNoFacturable' => TRUE)))
					{
						$this->db->trans_rollback();
						$this->out->error($this->m_albaransalida->error_message());			
					}
					# Cierra el abono
					if (!$this->m_albaransalida->cerrar($ab))
					{
						$this->db->trans_rollback();
						$this->out->error($this->m_albaransalida->error_message());			
					}
					++$anticipos;
				}
				else
				{
					if (!$this->m_albaransalida->update($v['nIdAlbaran'], array('nIdFactura' => $idf)))
					{
						$this->db->trans_rollback();
						$this->out->error($this->m_albaransalida->error_message());			
					}					 					
				}
				++$count;
			}			
			$this->db->trans_commit();
			$link = format_enlace_cmd($idf, site_url('ventas/factura/index/' . $idf));
			$this->out->dialog(TRUE, sprintf($this->lang->line('facturar-suscripciones-ok'), $link, $count, $anticipos));		
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Crea un nuevo pedido a proveedor de una suscripción
	 * @param int @Id Id de la suscripción
	 * @return DIALOG
	 */
	function crear_pedido($id = null)
	{
		$this->userauth->roleCheck($this->auth . '.upd');
		$id = isset($id)?$id:$this->input->get_post('id');
		if (is_numeric($id))
		{
			$id_n = $this->reg->crear_pedido($id);
			if ($id_n)
			{
				$link_pd = format_enlace_cmd($id_n, site_url('compras/pedidoproveedor/index/' . $id_n));
				$this->out->dialog(TRUE, sprintf($this->lang->line('suscripciones-nuevo-pedido-ok'), $link_pd));		
			}
			$this->out->error($this->reg->error_message());
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Muestra el histórico de precios
	 * @param int $id Id del suscripción
	 * @return HTML_FILE
	 */
	function historicoprecios($id = null) 
	{
		$this->userauth->roleCheck($this->auth . '.get_list');
		$id = isset($id) ? $id : $this->input->get_post('id');

		if ($id) 
		{
			$docs = $this->reg->get_precios($id);
			if (count($docs) > 0) 
			{
				$art = $this->reg->load($id, 'articulo');
				$art['precios'] = $docs;
				$art['nIdLibro'] = $id;
				$art['fIVA'] = $art['articulo']['fIVA'];
				$message = $this->load->view('catalogo/precios', $art, TRUE);
				$this->out->html_file($message, $this->lang->line('precios_articulo') . ' ' . $id, 'iconoReportTab');
			}
			$this->out->success($this->lang->line('no-precios_articulo'));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Muestra el histórico de clientes
	 * @param int $id Id del suscripción
	 * @return HTML_FILE
	 */
	function historicoclientes($id = null) 
	{
		$this->userauth->roleCheck($this->auth . '.get_list');
		$id = isset($id) ? $id : $this->input->get_post('id');

		if (is_numeric($id))
		{
			$docs = $this->reg->get_clientes($id);
			if (count($docs) > 0) 
			{
				$art = $this->reg->load($id, 'articulo');
				$art['clientes'] = $docs;
				$message = $this->load->view('suscripciones/clientes', $art, TRUE);
				$this->out->html_file($message, $this->lang->line('historico_clientes') . ' ' . $id, 'iconoReportTab');
			}
			$this->out->success($this->lang->line('no-historico_clientes'));
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

			$this->load->model('clientes/m_direccioncliente');
			$this->load->model('clientes/m_email');
			$this->load->model('clientes/m_telefono');
			$pd = $this->reg->load($id, 'cliente');
			$dir = $this->m_direccioncliente->load($pd['nIdDireccionEnvio']);
			$emails = $this->m_email->get_list($pd['nIdCliente']);
			$em = $this->utils->get_profile($emails, array(PERFIL_SUSCRIPCIONES, PERFIL_ENVIO));
			$tels = $this->m_telefono->get_list($pd['nIdCliente']);
			$tf = $this->utils->get_profile($tels, array(PERFIL_SUSCRIPCIONES, PERFIL_ENVIO));
		
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
	 * Recálculo de los costes de albarán
	 * @return [type] [description]
	 */
	function costes()
	{
		$res = $this->reg->costes();
	}
}

/* End of file Suscripcion.php */
/* Location: ./system/application/controllers/suscripciones/suscripcion.php */