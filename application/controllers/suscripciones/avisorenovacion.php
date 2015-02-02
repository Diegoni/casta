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
 * Avisos de renovación
 *
 */
class AvisoRenovacion extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return AvisoRenovacion
	 */
	function __construct()
	{
		parent::__construct('suscripciones.avisorenovacion', 'suscripciones/M_avisorenovacion', TRUE, 'suscripciones/avisos.js', 'Avisos de renovación');
	}

	/**
	 * Crea una campaña y los avisos de renovación
	 * @param int $renovacion Fecha máxima de renovación
	 * @param string $descripcion Descripción de la campaña
	 * @return MSG
	 */
	function crear($renovacion = null, $descripcion = null)
	{
		$this->userauth->roleCheck($this->auth . '.add');
		$renovacion = isset($renovacion) ? $renovacion : $this->input->get_post('renovacion');
		$descripcion = isset($descripcion) ? $descripcion : $this->input->get_post('descripcion');
		if (!empty($renovacion) && !empty($descripcion))
		{
			#Crea la campaña
			$this->load->model('suscripciones/m_grupoaviso');
			$id = $this->m_grupoaviso->insert(array('cDescripcion' => $descripcion));
			if ($id < 0)
			{
				$this->out->error($this->m_grupoaviso->error_message());
			}
			return $this->crear_avisos($id, $renovacion);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Función de impresión del controlador
	 * @param int $id Id del registro a imprimir
	 * @param string $clientes Id de los clientes separados por ;
	 * @return MSG
	 */
	function del_avisos($id = null, $cliente = null)
	{
		$this->userauth->roleCheck(($this->auth .'.index'));

		$id 	= isset($id)?$id:$this->input->get_post('id');
		$cliente 	= isset($cliente)?$cliente:$this->input->get_post('cliente');

		if (is_numeric($id) && is_numeric($cliente))
		{
			$avisos = $this->reg->get_pendientes($id, $cliente, FALSE);
			$this->db->trans_begin();
			$count = 0;
			foreach ($avisos as $k2 => $sus)
			{
				if (!$this->reg->delete($sus['nIdAvisoRenovacion']))
				{
					$this->db->trans_rollback();
					$this->out->error($this->reg->error_message());
				}
				++$count;
			}
			$this->db->trans_commit();
			$this->out->success(sprintf($this->lang->line('sus-avisos-eliminados'), $count));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Ventana de avisos de renovación
	 * @return WINDOW
	 */
	function avisos()
	{
		$this->_show_form('index', 'suscripciones/avisos.js', $this->lang->line('Avisos de renovación'));
	}

	/**
	 * Avisos de renovación pendientes de enviar
	 * @param int $id Id de la campaña
	 */
	function pendientes($id = null)
	{
		$this->userauth->roleCheck($this->auth . '.get_list');
		$id = isset($id) ? $id : $this->input->get_post('id');
		if ($id)
		{

			$data = $this->reg->pendientes($id);
			foreach ($data as $k => $v)
			{
				$data[$k]['cCliente'] = format_name($v['cNombre'], $v['cApellido'], $v['cEmpresa']);
			}
			if (isset($data))
				sksort($data, 'cCliente');

			$this->out->data($data);
		}

		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Avisos de renovación pendientes de confirmar
	 * @param int $id Id de la campaña
	 */
	function por_confirmar($id = null)
	{
		$this->userauth->roleCheck($this->auth . '.get_list');

		$id = isset($id) ? $id : $this->input->get_post('id');

		if ($id)
		{

			$data = $this->reg->por_confirmar($id);
			foreach ($data as $k => $v)
			{
				$data[$k]['cCliente'] = format_name($v['cNombre'], $v['cApellido'], $v['cEmpresa']);
			}
			if (isset($data))
				sksort($data, 'cCliente');

			$this->out->data($data);
		}

		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Crea la tareas para enviar los avisos de renovación a los clientes
	 * @param int $id Id de la campaña
	 * @param string $clientes Id de los clientes separados por ;
	 * @return MSG
	 */
	function send_all($id = null, $clientes = null)
	{
		$this->userauth->roleCheck($this->auth . '.send');

		$id = isset($id) ? $id : $this->input->get_post('id');
		$clientes = isset($clientes) ? $clientes : $this->input->get_post('clientes');

		if ($id && $clientes)
		{
			$cmd = site_url("suscripciones/avisorenovacion/send/{$id}/{$clientes}");

			$this->load->library('tasks');
			$this->tasks->add2($this->lang->line('Avisos de renovación'), $cmd);

		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Tareas para enviar los avisos de renovación a los clientes
	 * @param int $id Id de la campaña
	 * @param string $clientes Id de los clientes separados por ;
	 * @return MSG
	 */
	function send($id = null, $clientes = null, $enviadas = null)
	{
		$this->userauth->roleCheck($this->auth . '.send');

		$id = isset($id) ? $id : $this->input->get_post('id');
		$clientes = isset($clientes) ? $clientes : $this->input->get_post('clientes');
		$enviadas 	= isset($enviadas)?$enviadas:$this->input->get_post('enviadas');

		if (is_numeric($id))
		{
			set_time_limit(0);
			$enviadas = empty($enviadas)?FALSE:format_tobool($enviadas);

			$inicio = time();
			// Modelos que se van a usar
			$this->load->model('clientes/m_cliente');
			$this->load->model('clientes/m_direccioncliente');
			$this->load->model('suscripciones/m_suscripcion');
			$this->load->model('suscripciones/m_grupoaviso');
			$this->load->model('catalogo/m_articulo');
			$this->load->model('perfiles/m_perfil');

			// Librerías
			$this->load->plugin('swift');
			$this->load->helper('asset');
			$this->load->library('Logger');
			$this->load->library('Comandos');

			// Datos SMTP
			$config['Host'] = $this->config->item('bp.suscripciones.host');
			$config['SMTPAuth'] = $this->config->item('bp.suscripciones.auth');
			$config['Password'] = $this->config->item('bp.suscripciones.pass');
			$config['Username'] = $this->config->item('bp.suscripciones.user');
			$config['From'] = $this->config->item('bp.suscripciones.from');
			$config['FromName'] = $this->config->item('bp.suscripciones.fromname');
			$config['Mailer'] = $this->config->item('bp.suscripciones.protocol');

			// Hoja de estilos CSS
			$css = $this->config->item('bp.suscripciones.css');
			if (trim($css) != '' && isset($css))
			{
				$css = css_asset_url($this->config->item('bp.suscripciones.css'));
				$css = file_get_contents($css);
			}
			$grupo = $this->m_grupoaviso->load($id);

			$this->logger->Log('Preparando avisos campaña Id ' .$grupo['cDescripcion'] . ' (' .$id . ') clientes ' . $clientes, 'avisosrenovacion');

			// Id de los clientes
			$clientes = preg_split('/[;\s,]/', $clientes);

			// Todos los clientes con avisos de renovación pendientes
			$data = $this->reg->pendientes($id, $enviadas);

			// Prepara un array con todos los grupos pendientes de los clientes indicados
			$enviar = array();
			foreach ($data as $k => $v)
			{
				if (in_array($v['nIdCliente'], $clientes))
					$enviar[] = $v;
			}
			if (count($enviar) == 0)
				$this->out->error($this->lang->line('avisos-no-hay'));

			$this->load->library('Messages');
			$this->messages->info(sprintf($this->lang->line('avisos-renovavion'), implode(',', $clientes)));
			// Obtiene de cada cliente los datos de las suscripciones pendientes
			$poremail = 0;
			$porcarta = 0;
			$sindatos = 0;
			$cartas = '';
			foreach ($enviar as $k => $envio)
			{
				// Datos del cliente para el envío
				$cliente = $this->m_cliente->load($envio['nIdCliente']);
				$cliente['email'] = $this->m_cliente->get_email($envio['nIdCliente'], PERFIL_SUSCRIPCIONES);
				$cliente['direccion'] = $this->m_cliente->get_direccion($envio['nIdCliente'], PERFIL_SUSCRIPCIONES);
				#echo '<pre>'; var_dump($cliente['direccion']); echo '</pre>';
				$enviar[$k]['cliente'] = $cliente;

				$clientelink = format_enlace_cmd(format_name($cliente['cNombre'], $cliente['cApellido'], $cliente['cEmpresa']), site_url('clientes/cliente/index/' . $envio['nIdCliente']));
				$this->messages->info(sprintf($this->lang->line('avisos-renovavion-suscripciones'), $envio['nIdCliente'], $clientelink));

				//Suscripciones
				$suscripciones = $this->reg->get_pendientes($id, $envio['nIdCliente'], $enviadas);
				foreach ($suscripciones as $k2 => $sus)
				{
					$aviso = $this->reg->load($sus['nIdAvisoRenovacion']);
					$suscripcion = $this->m_suscripcion->load($sus['nIdSuscripcion']);

					$revista = $this->m_articulo->load($suscripcion['nIdRevista']);
					$direnv = $this->m_direccioncliente->load($suscripcion['nIdDireccionEnvio']);
					$dirfac = $this->m_direccioncliente->load($suscripcion['nIdDireccionFactura']);

					$suscripciones[$k2]['aviso'] = $aviso;
					$suscripciones[$k2]['suscripcion'] = $suscripcion;
					$suscripciones[$k2]['revista'] = $revista;
					$suscripciones[$k2]['direnv'] = $direnv;
					$suscripciones[$k2]['dirfac'] = $dirfac;

					$suslink = format_enlace_cmd($sus['nIdSuscripcion'], site_url('suscripciones/suscripcion/index/' . $sus['nIdSuscripcion']));

					$this->messages->info(sprintf($this->lang->line('avisos-renovavion-suscripcion'), $suslink, $revista['cTitulo']), 1);
					$this->messages->info(sprintf($this->lang->line('avisos-renovavion-direccion-envio'), format_address_print($direnv)), 2);
					$this->messages->info(sprintf($this->lang->line('avisos-renovavion-direccion-factura'), format_address_print($dirfac)), 2);
				}
				$enviar[$k]['suscripciones'] = $suscripciones;

				// Envío del aviso
				if (isset($cliente['email']))
				{
					// Envío por email
					$message = $this->load->view('suscripciones/avisoemail', $enviar[$k], TRUE);
					$this->messages->info(sprintf($this->lang->line('avisos-renovavion-enviando-email'), $cliente['email']['cEMail']), 2);

					$mail = new Mailer($config);
					$res = $mail->prepare($this->lang->line('avisos-renovavion-asunto'), $message, $css);
					$e = ($this->config->item('bp.suscripciones.debug')) ? $this->config->item('bp.suscripciones.debugemails') : $cliente['email']['cEMail'];
					$this->logger->Log("Aviso renovación campaña {$id} cliente {$envio['nIdCliente']}: Enviando a email {$e}", 'avisosrenovacion');
					$res = $mail->send(array($e));
					if ($res['error'] > 0)
					{
						$this->messages->error(sprintf($this->lang->line('avisos-renovavion-enviando-email-error'), $e), 2);
						$this->logger->Log("Aviso renovación campaña {$id} cliente {$envio['nIdCliente']}: {$e} Error de envío", 'avisosrenovacion');
					}
					else
					{
						$this->messages->info(sprintf($this->lang->line('avisos-renovavion-enviando-email-ok'), $e), 2);
						$this->logger->Log("Aviso renovación campaña {$id} cliente {$envio['nIdCliente']}: {$e} Envío correcto", 'avisosrenovacion');
						// Se indica que los avisos están enviados
						foreach ($enviar[$k]['suscripciones'] as $sus)
						{
							//echo 'AVISO '; var_dump($sus['aviso']['nIdAvisoRenovacion']); echo '<br/>';
							$this->reg->update($sus['aviso']['nIdAvisoRenovacion'], array('dEnviada' => time()));
						}
					}
					++$poremail;
					$message = $this->load->view('suscripciones/avisoemail', $enviar[$k], TRUE);
					$modotexto =$this->lang->line('EMAIL') . "[{$e}]";
					//$cartas .= $message;
				}
				else
				{
					// Envío por carta
					if (isset($cliente['direccion']))
						++$porcarta;
					else
						++$sindatos;
					$message = $this->load->view('suscripciones/avisocarta', $enviar[$k], TRUE);
					$cartas .= $message;
					$this->messages->info(sprintf($this->lang->line('avisos-renovavion-enviando-carta'), format_address_print($cliente['direccion'])), 2);
					foreach ($enviar[$k]['suscripciones'] as $sus)
					{
						$this->reg->update($sus['aviso']['nIdAvisoRenovacion'], array('dEnviada' => time()));
					}
					$modotexto =$this->lang->line('CARTA');
				}
				foreach ($suscripciones as $sus)
				{
					$nota = sprintf($this->lang->line('suscripciones-nota-avisoenviado'), $grupo['cDescripcion'], $modotexto);
					$this->_add_nota(null, $sus['nIdSuscripcion'], NOTA_INTERNA, $nota, $this->m_suscripcion->get_tablename());
				}
				
			}
			$final = time();
			$tiempo = ($final - $inicio);
			$this->messages->info(sprintf($this->lang->line('avisos-renovavion-enviados'), $poremail, $porcarta, $sindatos, $tiempo));
			$body = $this->messages->out($this->lang->line('Avisos de renovación'));
			//$body .= $cartas;
			//Enviamos las cartas como un comando
			if ($porcarta + $sindatos > 0)
			{
				$result = $this->out->html_file($cartas, $this->lang->line('Avisos de renovación'), 'iconoReportTab', $this->config->item('bp.suscripciones.css'), FALSE, FALSE);
				$this->comandos->add($this->userauth->get_username(), $result);
			}

			$this->out->html_file($body, $this->lang->line('Avisos de renovación'), 'iconoReportTab');
			return;
		}

		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Lista los avisos de renovación pendientes de crear
	 * @param int $id Id de la campaña
	 * @param date $renovacion Fecha máxima de renovación
	 * @return HTML_FILE
	 */
	function por_crear($id = null, $renovacion = null)
	{
		$this->userauth->roleCheck($this->auth . '.get_list');

		$id = isset($id) ? $id : $this->input->get_post('id');
		$renovacion = isset($renovacion) ? $renovacion : $this->input->get_post('renovacion');

		if ($id && $renovacion)
		{
			$renovacion = to_date($renovacion);
			$datos['suscripciones'] = $this->reg->por_crear($id, $renovacion);

			$message = $this->load->view('suscripciones/avisosporcrear', $datos, TRUE);

			$this->out->html_file($message, $this->lang->line('Avisos por crear') . ' ' . $id, 'iconoReportTab');
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Crea los avisos de renovación
	 * @param int $id Id de la campaña
	 * @param date $renovacion Fecha máxima de renovación
	 * @return MSG
	 */
	function crear_avisos($id = null, $renovacion = null)
	{
		$this->userauth->roleCheck($this->auth . '.add');

		$id = isset($id) ? $id : $this->input->get_post('id');
		$renovacion = isset($renovacion) ? $renovacion : $this->input->get_post('renovacion');

		if ($id && $renovacion)
		{
			set_time_limit(0);
			$renovacion = to_date($renovacion);
			$count = $this->reg->crear_avisos($id, $renovacion);

			if ($count > 0)
				$this->out->success(sprintf($this->lang->line('avisos-renovacion-generados'), $count));
			$this->out->success($this->lang->line('avisos-renovacion-no-hay'));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Estado de una campaña de renovación
	 * @param int $id ID de la campaña
	 * @return HTML_FILE
	 */
	function estado($id = null)
	{
		$this->userauth->roleCheck($this->auth . '.get_list');

		$id = isset($id) ? $id : $this->input->get_post('id');

		if (is_numeric($id))
		{
			$datos = $this->reg->estado($id);
			$this->load->model('suscripciones/m_grupoaviso');
			$datos['campana'] = $this->m_grupoaviso->load($id);
			$message = $this->load->view('suscripciones/estado', $datos, TRUE);

			$this->out->html_file($message, $this->lang->line('Estado de la campaña') . ' ' . $datos['campana']['cDescripcion'], 'iconoReportTab');
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Devuelve los avisos de renovación pendientes de un cliente
	 * @param int $idcliente ID del cliente
	 * @return DATA
	 */
	function avisos_cliente($id = null)
	{
		$this->userauth->roleCheck($this->auth . '.get_list');

		$id = isset($id) ? $id : $this->input->get_post('id');

		if (is_numeric($id))
		{
			$this->load->model('suscripciones/m_grupoaviso');
			$grupo = $this->m_grupoaviso->get_last_grupo();
			$data = $this->reg->avisos_cliente($id, $grupo);
			$this->load->model('clientes/m_direccioncliente');
			foreach ($data as $k => $v)
			{
				$data[$k]['direccionenvio'] = $this->m_direccioncliente->load($v['nIdDireccionEnvio']);
			}

			$this->out->data($data);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));

	}

	/**
	 * Obtiene los avisos de renovación de un cliente y una campaña
	 * @param  int $id      Id de la campaña
	 * @param  int $cliente Id del cliente
	 * @return DATA
	 */
	function get_avisos($id = null, $cliente = null)
	{
		$id = isset($id) ? $id : $this->input->get_post('id');
		$cliente = isset($cliente) ? $cliente : $this->input->get_post('cliente');
		if (is_numeric($id) && !empty($cliente))
		{			
			$this->out->data($this->reg->get_pendientes($id, $cliente, TRUE));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Gestiona los avisos de renovación
	 * @param  int $id      Id de la campaña
	 * @param  int $cliente Id del cliente
	 * @param  string $cmpid   Id del componente a refrescar
	 * @param  bool $renovar TRUE: Renovar, FALSE: No renovar
	 * @return FORM
	 */
	function gestionar($id = null, $cliente = null, $cmpid = null, $renovar = null)
	{
		$this->userauth->roleCheck($this->auth . '.upd');

		$id = isset($id) ? $id : $this->input->get_post('id');
		$cliente = isset($cliente) ? $cliente : $this->input->get_post('cliente');
		$cmpid = isset($cmpid) ? $cmpid : $this->input->get_post('cmpid');
		$renovar = isset($renovar) ? $renovar : $this->input->get_post('renovar');

		if (is_numeric($id) && !empty($cliente))
		{			
			$renovar = format_tobool($renovar);
			$data['id'] = $id;
			$data['cliente'] = $cliente;
			$data['url'] = site_url('suscripciones/avisorenovacion/' . ($renovar?'Aceptar':'cancelar'));
			$data['title'] = $this->lang->line($renovar?'Renovar suscripción':'Cancelar suscripción');
			$data['ref'] = $renovar;
			$data['cmpid'] = $cmpid;
			$data['icon'] = ($renovar?'icon-accept':'icon-cancel');
			$this->_show_js('upd', 'suscripciones/gestionar.js', $data);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Acepta un aviso de renovación
	 * @param int $id Id de la suscripción
	 * @param string $contacto Persona que realiza la cancelación
	 * @param string $ref Referencia de la suscripción
	 * @param int $id Id del modo en el que ha dado la orden de cancelación
	 * @param date $fecha Fecha en la que se ha recibido la cancelación
	 * @param string $cmpid Id del componente que realiza la tarea
	 * @param string $avisos Grupo de suscripciones/Ref para aceptar juntas
	 * @return bool
	 */
	function aceptar($id = null, $contacto = null, $ref = null, $modo = null, $fecha = null, $cmpid = null, $avisos = null)
	{
		$this->userauth->roleCheck($this->auth . '.upd');

		$id = isset($id) ? $id : $this->input->get_post('id');
		$contacto = isset($contacto) ? $contacto : $this->input->get_post('contacto');
		$ref = isset($ref) ? $ref : $this->input->get_post('ref');
		$modo = isset($modo) ? $modo : $this->input->get_post('modo');
		$fecha = isset($fecha) ? $fecha : $this->input->get_post('fecha');
		$cmpid = isset($cmpid) ? $cmpid : $this->input->get_post('cmpid');
		$avisos = isset($avisos) ? $avisos : $this->input->get_post('avisos');

		if ((is_numeric($id) || !empty($avisos)) && !empty($contacto) && is_numeric($modo))
		{			
			set_time_limit(0);
			$fecha = (empty($fecha))?time():to_date($fecha);
			
			# Soporte
			$this->load->model('suscripciones/m_grupoaviso');
			$this->load->model('suscripciones/m_suscripcion');
			$this->load->model('suscripciones/m_mediorenovacion');
			$this->load->library('Emails');
			if (!empty($avisos))
			{
				$temp = explode(';', $avisos);
				$avisos = array();
				foreach ($temp as $value) 
				{
					$a = explode('##', $value);
					if (count($a) == 2)
					{
						if ($a[1] == 'null') $a[1] = null;
						if (!empty($a[0]))
							$avisos[] = $a;
					}
				}
			}
			else
			{
				$avisos[] = array($id, $ref);
			}
			#var_dump($avisos); die();
			$this->db->trans_begin();
			$notas = array();
			foreach ($avisos as $value) 
			{
				$id = $value[0];
				$ref = $value[1];
				if ($ref == '') $ref = null;
				#var_dump($ref); die();

				# Aviso?
				$aviso = $this->reg->load($id);
				if (!isset($aviso['nIdSuscripcion']))
					$this->out->error($this->lang->line('avisorenovacion-erroneo'));
				$ids = $aviso['nIdSuscripcion'];				
				
				# Renueva la suscripcion	
				if (!$this->m_suscripcion->renovar($ids, $ref))
				{
					$this->db->trans_rollback();
					$this->out->error($this->m_suscripcion->error_message());				
				}

				# Acepta el aviso
				if (!$this->reg->aceptar($id, $contacto, $modo, $fecha))
				{
					$this->db->trans_rollback();
					$this->out->error($this->reg->error_message());
				}

				# Crea un pedido de renovación al proveedor
				if (!$this->m_suscripcion->crear_pedido($ids))
				{
					$this->db->trans_rollback();
					$this->out->error($this->m_suscripcion->error_message());
				}
				
				# Añade una nota a la suscripción
				$modotexto = $this->m_mediorenovacion->load($modo);
				$modotexto = $modotexto['cDescripcion'];
				$idgrupo = $this->m_grupoaviso->get_last_grupo();
				$grupo = $this->m_grupoaviso->load($idgrupo);
				$nota = sprintf($this->lang->line('suscripciones-nota-aceptacion'), $grupo['cDescripcion'], $ids, $contacto, $ref, $modotexto);
				$this->_add_nota(null, $ids, NOTA_INTERNA, $nota, $this->m_suscripcion->get_tablename());

				# Envía un mensaje de email de aviso
				$text = $this->load->view('main/email', array('texto_email' => $nota), TRUE);
				$emails = preg_split('/;/' , $this->config->item('bp.suscripciones.avisos'));
				$this->emails->send($this->lang->line('suscripciones-email-aceptacion-subject'), $text, $emails, null, null, null, $this->config->item('bp.documentos.css'));
				$notas[] = $nota;
			}
				
			#$this->db->trans_rollback();
			$this->db->trans_commit();
			$this->out->success(implode('<br/>', $notas));
		}
		elseif (is_numeric($id) && empty($avisos))
		{
			$data['id'] = $id;
			$data['url'] = site_url('suscripciones/avisorenovacion/aceptar');
			$data['title'] = $this->lang->line('Renovar suscripción');
			$data['ref'] = TRUE;
			$data['cmpid'] = $cmpid;
			$data['icon'] = 'icon-accept';
			$this->_show_js('upd', 'suscripciones/aceptarrenovar.js', $data);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Cancela un aviso de renovación
	 * @param int $id Id de la suscripción
	 * @param string $contacto Persona que realiza la cancelación
	 * @param int $id Id del modo en el que ha dado la orden de cancelación
	 * @param date $fecha Fecha en la que se ha recibido la cancelación
	 * @param string $cmpid Id del componente que realiza la tarea
	 * @param string $avisos Grupo de suscripciones/Ref para aceptar juntas
	 * @return bool
	 */
	function cancelar($id = null, $contacto = null, $modo = null, $fecha = null, $cmpid = null, $avisos = null)
	{
		$this->userauth->roleCheck($this->auth . '.upd');

		$id = isset($id) ? $id : $this->input->get_post('id');
		$contacto = isset($contacto) ? $contacto : $this->input->get_post('contacto');
		$modo = isset($modo) ? $modo : $this->input->get_post('modo');
		$fecha = isset($fecha) ? $fecha : $this->input->get_post('fecha');
		$cmpid = isset($cmpid) ? $cmpid : $this->input->get_post('cmpid');
		$avisos = isset($avisos) ? $avisos : $this->input->get_post('avisos');

		if ((is_numeric($id) || !empty($avisos)) && !empty($contacto) && is_numeric($modo))
		{
			set_time_limit(0);
			$fecha = (empty($fecha))?time():to_date($fecha);
			
			$this->load->model('suscripciones/m_grupoaviso');
			$this->load->model('suscripciones/m_suscripcion');
			$this->load->model('suscripciones/m_mediorenovacion');
			$this->load->model('suscripciones/m_reclamacion');
			$this->load->model('suscripciones/m_tiporeclamacion');
			$this->load->library('Emails');
						
			if (!empty($avisos))
			{
				$temp = explode(';', $avisos);
				$avisos = array();
				foreach ($temp as $value) 
				{
					$a = explode('##', $value);
					if (count($a) == 2)
					{
						if ($a[1] == 'null') $a[1] = null;
						if (!empty($a[0]))
							$avisos[] = $a;
					}
				}
			}
			else
			{
				$avisos[] = array($id, $ref);
			}
			#var_dump($avisos); die();
			$this->db->trans_begin();
			$notas = array();
			foreach ($avisos as $value) 
			{
				$id = $value[0];
				$ref = $value[1];
				if ($ref == '') $ref = null;
				# Aviso?
				$aviso = $this->reg->load($id);
				if (!isset($aviso['nIdSuscripcion']))
					$this->out->error($this->lang->line('avisorenovacion-erroneo'));
				$ids = $aviso['nIdSuscripcion'];

				# Cancela la suscripcion	
				if (!$this->m_suscripcion->cancelar($ids))
				{
					$this->db->trans_rollback();
					$this->out->error($this->m_suscripcion->error_message());				
				}
				
				# Cancela la renovacion
				if (!$this->reg->cancelar($id, $contacto, $modo, $fecha))
				{
					$this->db->trans_rollback();
					$this->out->error($this->reg->error_message());
				}

				# Añade una nota a la suscripción
				$modotexto = $this->m_mediorenovacion->load($modo);
				$modotexto = $modotexto['cDescripcion'];
				$idgrupo = $this->m_grupoaviso->get_last_grupo();
				$grupo = $this->m_grupoaviso->load($idgrupo);
				$nota = sprintf($this->lang->line('suscripciones-nota-cancelacion'), $grupo['cDescripcion'], $ids, $contacto, $modotexto);
				$this->_add_nota(null, $ids, NOTA_INTERNA, $nota, $this->m_suscripcion->get_tablename());

				#Nota de cancelación al proveedor
				$datos = $this->m_suscripcion->load($ids, 'direccionenvio');
				$datos['cDireccionEnvio'] = format_address_print($datos['direccionenvio']);
				$reclamacion = $this->m_reclamacion->create(TIPORECLAMACION_CANCELARSUSCRIPCION, 
					$datos['nIdCliente'],
					isset($datos['nIdProveedor'])?$datos['nIdProveedor']:null, 
					$ids, 
					$datos);			
				if ($reclamacion < 0)
				{
					$this->db->trans_rollback();
					$this->out->error($this->m_reclamacion->error_message());
				}

				# Envía un mensaje de email de aviso
				$text = $this->load->view('main/email', array('texto_email' => $nota), TRUE);
				$emails = preg_split('/;/' , $this->config->item('bp.suscripciones.avisos'));			
				$this->emails->send($this->lang->line('suscripciones-email-cancelacion-subject'), $text, $emails, null, null, null, $this->config->item('bp.documentos.css'));
			#$this->db->trans_rollback();
				$notas[] = $nota;
			}
			$this->db->trans_commit();
			$this->out->success(implode('<br/>', $notas));
		}
		elseif (is_numeric($id) && empty($avisos))
		{
			$data['id'] = $id;
			$data['url'] = site_url('suscripciones/avisorenovacion/cancelar');
			$data['title'] = $this->lang->line('Cancelar suscripción');
			$data['icon'] = 'icon-cancel';
			$data['ref'] = FALSE;
			$data['cmpid'] = $cmpid;
			$this->_show_js('upd', 'suscripciones/aceptarrenovar.js', $data);
		}

		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Controller#_pre_printer($id, $data, $css)
	 */
	protected function _pre_printer($id, &$data, &$css)
	{
		parent::_pre_printer($id, $data, $css);
		$css = $this->config->item('bp.suscripciones.css');
		// Datos del cliente para el envío
		$this->load->model('clientes/m_cliente');
		$this->load->model('clientes/m_direccioncliente');
		$this->load->model('suscripciones/m_suscripcion');
		$this->load->model('suscripciones/m_grupoaviso');
		$this->load->model('catalogo/m_articulo');
		$cliente = $this->m_cliente->load($data['nIdCliente']);
		$cliente['email'] = $this->m_cliente->get_email($data['nIdCliente'], PERFIL_SUSCRIPCIONES);
		$cliente['direccion'] = $this->m_cliente->get_direccion($data['nIdCliente'], PERFIL_SUSCRIPCIONES);
		$data['cliente'] = $cliente;

		//Suscripciones
		$suscripciones = $this->reg->get_pendientes($id, $data['nIdCliente'], $data['bEnviadas']);
		foreach ($suscripciones as $k2 => $sus)
		{
			$aviso = $this->reg->load($sus['nIdAvisoRenovacion']);
			$suscripcion = $this->m_suscripcion->load($sus['nIdSuscripcion']);

			$revista = $this->m_articulo->load($suscripcion['nIdRevista']);
			$direnv = $this->m_direccioncliente->load($suscripcion['nIdDireccionEnvio']);
			$dirfac = $this->m_direccioncliente->load($suscripcion['nIdDireccionFactura']);

			$suscripciones[$k2]['aviso'] = $aviso;
			$suscripciones[$k2]['suscripcion'] = $suscripcion;
			$suscripciones[$k2]['revista'] = $revista;
			$suscripciones[$k2]['direnv'] = $direnv;
			$suscripciones[$k2]['dirfac'] = $dirfac;
		}
		$data['suscripciones'] = $suscripciones;

		$data['campana'] = $this->m_grupoaviso->load($id);

		return TRUE;
	}

	/**
	 * Función de impresión del controlador
	 * @param int $id Id del registro a imprimir
	 * @param string $clientes Id de los clientes separados por ;
	 * @param bool $enviadas 1: Los avisos enviados, 0: todos
	 * @param string $report El report a utilizar
	 * @return JSON
	 */
	function printer($id = null, $cliente = null, $enviadas = null, $report = null)
	{
		$this->userauth->roleCheck(($this->auth .'.index'));

		$id 		= isset($id)?$id:$this->input->get_post('id');
		$report 	= urldecode(isset($report)?$report:$this->input->get_post('report'));
		$enviadas 	= isset($enviadas)?$enviadas:$this->input->get_post('enviadas');
		$cliente 	= isset($cliente)?$cliente:$this->input->get_post('cliente');
		
		$title = $this->title . ' - ' . $id;

		$lang = $this->config->item('reports.language');
		$lang = preg_split('/;/', $lang);
		$lang = $lang[0];

		$out = TRUE;
		$list = FALSE;
		$preview = TRUE;

		$print 	= FALSE;
		$enviadas= (bool)format_tobool($enviadas);
		#$preview= (bool)format_tobool($print);

		// Se pide el listado?
		if ($list)
		{
			$reports = $this->_get_reports();
			if (!is_array($reports))
			{
				$this->out->success();
			}
			$this->out->data($reports);
		}
		if ($id)
		{
			//Hay un report por defecto?
			if (!$report)
			{
				$report = $this->_get_report_default();
			}
			$data['nIdCliente'] = $cliente;
			$data['bEnviadas'] = $enviadas;
			$css = null;
			$this->_pre_printer($id, $data, $css);
			$text = $this->show_report($title, $data, $report, $css, $out, $lang, $preview);
			if ($print===TRUE) echo $text;
			return $text;
		}
		else
		{
			$this->out->message(FALSE, $this->lang->line('mensaje_faltan_datos'));
		}

		$this->out->message($this->lang->line('Imprimir'), $this->lang->line('no-soportado'));
	}

}

/* End of file AvisoRenovacion.php */
/* Location: ./system/application/controllers/suscripciones/AvisoRenovacion.php */
