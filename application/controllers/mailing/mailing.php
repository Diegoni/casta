<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	mailing
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Mailing
 * @author alexl
 *
 */
class Mailing extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return App
	 */
	function __construct()
	{
		parent::__construct('mailing.mailing', 'mailing/M_mailing', TRUE, 'mailing/mailing.js', 'Mailing');
	}

	/**
	 * Resetea todos los emails que no se han podido enviar para que se puedan enviar de nuevo
	 * @param int $id Id del mailing
	 * @return JSON
	 */
	function reset($id = null)
	{
		$this->userauth->roleCheck(($this->auth .'.reset'));
		$id = isset($id)?$id:$this->input->get_post('id');
		if ($id)
		{
			$count = $this->reg->reset($id);
			$success = TRUE;
			$message = sprintf($this->lang->line('mailing-reset-ok'), $count);
		}
		else
		{
			$success = FALSE;
			$message = $this->lang->line('mensaje_faltan_datos');
		}

		// Respuesta
		echo $this->out->message($success, $message);
	}

	/**
	 * Resetea todos los emails que no se han podido enviar para que se puedan enviar de nuevo
	 * @param int $id Id del mailing
	 * @return JSON
	 */
	function del_emails($id = null)
	{
		$this->userauth->roleCheck(($this->auth .'.del'));
		$id = isset($id)?$id:$this->input->get_post('id');
		if ($id)
		{
			set_time_limit(0);
			$this->reg->del_emails($id);
			$success = TRUE;
			$message = $this->lang->line('mailing-delete-emails-ok');
		}
		else
		{
			$this->out->error($this->lang->line('mensaje_faltan_datos'));
		}

		// Respuesta
		$this->out->message($success, $message);
	}

	/**
	 * Elimina las referencias al email indicado.
	 * @param string $emails Emails separados por espacio o ;
	 * @return JSON
	 */
	function delete($emails = null)
	{
		$this->userauth->roleCheck(($this->auth .'.delete_emails'));
		$emails = isset($emails)?$emails:urldecode($this->input->get_post('emails'));

		if ($emails)
		{
			$count = 0;
			$email = preg_split('/[\;\s\n\r\;]/', $emails);
			foreach ($email as $e)
			{
				if (trim($e) != '')
				{
					$c = $this->reg->del_general($e);
					$count += $c;
				}
			}
			$this->out->success(sprintf($this->lang->line('mailing-delete-emails-general'), $count));
		}
		else
		{
			$this->_show_js('delete_emails', 'mailing/delete.js');
		}
	}

	/**
	 * Realiza el envío de un mailing
	 * @param int $id Id del mailing
	 * @return JSON
	 */
	function send($id = null, $time = null)
	{
		$this->userauth->roleCheck(($this->auth .'.send'));
		$id = isset($id)?$id:$this->input->get_post('id');
		$time = isset($time)?$time:$this->input->get_post('time');
		$time = ($time !== FALSE && $time != '')?format_str_to_date($time):null;
		if ($id)
		{
			$this->load->library('tasks');
			$cmd = site_url("mailing/mailing/send_task/{$id}");
			$this->tasks->add2(sprintf($this->lang->line('mailing-task-send'), $id), $cmd, null, $time);
		}
		else
		{
			$this->out->error($this->lang->line('mensaje_faltan_datos'));
		}
	}

	/**
	 * Envío de emails interno
	 * @param int $id Id del mailing
	 * @param array $emails Array de emails
	 * @param array $data Configuración
	 * @param bool $success TRUE: Correcto, FALSE: error
	 * @param string $message Mensaje de resultado
	 * @param bool $debug TRUE: slo funciona en modo de pruebas
	 */
	protected function _send_emails($id, $emails, $data, &$success, &$message, $debug = FALSE)
	{
		// Envía los emails
		if ($emails)
		{
			$this->load->plugin('swift');
			$this->load->helper('asset');
			$this->load->helper('extjs');
			$this->load->library('Logger');

			//Puede tardar
			set_time_limit(0);
			$this->logger->Log('Preparando Mailing ' . $id, 'mailing');

			// Si se han indicado la config en el mailing la usa, sino usa la que esté por defecto
			// TODO: Esto tendrá que adaptarse a los datos de cada usuario, sino al genérico
			if (isset($data['cSMTP']) && (trim($data['cSMTP'])!=''))
			{
				$config['Host']     = $data['cSMTP'];
				$config['SMTPAuth'] = $data['bAutenticacion'];
				$config['Password'] = $data['cPassword'];
				$config['Username'] = $data['cUser'];
			}
			else
			{
				$config['Host']     = $this->config->item('bp.mailing.host');
				$config['SMTPAuth'] = $this->config->item('bp.mailing.auth');
				$config['Password'] = $this->config->item('bp.mailing.pass');
				$config['Username'] = $this->config->item('bp.mailing.user');
			}

			// Se se indica email se usa como nombre
			if (isset($data['cEMailAddress']) && (trim($data['cEMailAddress'])!=''))
			{
				$r = preg_match_all('/(.*?)\<(.*?)>/', $data['cEMailAddress'], $match, PREG_SET_ORDER );
				if (isset($match[0]))
				{
					$config['From']  	= isset($match[2]) ? $match[2] : null;
					$config['FromName'] = isset($match[1]) ? $match[1] : null;
				}
			}
			$config['From']  	= (isset($config['From']))?$config['From']:$this->config->item('bp.mailing.from');
			$config['FromName'] = (isset($config['FromName']))?$config['From']:$this->config->item('bp.mailing.fromname');

			$config['Mailer']   = $this->config->item('bp.mailing.protocol');

			$ids = array();
			$list = array();
			$total_ok = 0;
			$total_nok = 0;
			foreach($emails as $em)
			{
				if (Mailer::valid_email($em['cEmail']))
				{
					$list[] = $em['cEmail'];
					if (isset($em['id']))
					{
						$ids[$em['cEmail']] = $em['id'];
					}
				}
				else
				{
					if (isset($em['id']))
					{
						$this->logger->Log("Mailing {$id} : {$em['cEmail']} " . $this->lang->line('mailing-error-email-formato') , 'mailing');
						$this->reg->error($em['id'], $this->lang->line('mailing-error-email-formato'));
					}
					$total_nok++;
				}
			}

			// Hoja de estilos CSS
			$css = $this->config->item('bp.mailing.css');
			if (trim($css) != '' && isset($css))
			{
				$css = css_asset_url($this->config->item('bp.mailing.css'));
				$css = file_get_contents($css);
			}

			// Envio
			$this->reg->process($id);
			#$res = $mail->send($list);
			# Versión en grupos
			$failures = array();
			$total_ok = 0;
			$total_nok = 0;
			$list2 = array_chunk($list, $this->config->item('bp.mailing.maxgroup'));
			foreach($list2 as $list)
			{
				// Email base
				$mail = new Mailer($config);
				$res = $mail->prepare($data['cAsunto'], $data['cBody'], $css, FALSE);
				$res = $mail->send((count($list)==1)?$list:null, null, (count($list)!=1)?$list:null);
				if ($res['error'] > 0)
				{
					foreach($res['failures'] as $e)
					{ 					
						if (isset($ids[$e]))
						{
							$ide = $ids[$e];
							if (isset($ide))
							{
								$this->logger->Log("Mailing {$id} : {$e} " . $this->lang->line('mailing-error-email') . ' ' .$mail->last_error , 'mailing');
								$this->reg->error($ide, $this->lang->line('mailing-error-email'));
								unset($ids[$e]);
							}
						}
					}
				}

				$total_ok += $res['count'] - $res['error'];
				$total_nok += $res['error'];
			}
			foreach ($ids as $e => $id2)
			{
				$this->logger->Log("Mailing {$id} : {$e} Enviado correctamente"  , 'mailing');
				if (isset($ids[$e]))
				{
					//$ide = $ids[$e];
					$this->reg->sended($id2);
				}
			}
			
			#Fin versión 1 a 1

			$success = TRUE;
			$text = format_enlace_cmd($data['cDescripcion'], site_url('mailing/mailing/index/' . $id));
			if ($total_nok > 0)
			{
				$message = sprintf($this->lang->line($debug?'mailing-enviados-debug':'mailing-enviados'), $text, $total_ok, $total_nok);
			}
			else
			{
				$message = sprintf($this->lang->line($debug?'mailing-enviados-debug-ok':'mailing-enviados-ok'), $text, $total_ok);
			}
			$this->logger->Log("Mailing {$id} : Finalizado Correctos: {$total_ok}, Erróneos: $total_nok", 'mailing');
		}
		else
		{
			$success = FALSE;
			$message = $this->lang->line('no-emails');
		}
	}

	/**
	 * Realiza el envío de un mailing
	 * @param int $id Id del mailing
	 * @return JSON
	 */
	function send_task($id = null)
	{
		$this->userauth->roleCheck(($this->auth .'.send'));

		$id = isset($id)?$id:$this->input->get_post('id');
		if ($id)
		{
			set_time_limit(0);
			$data = $this->reg->load($id);
			if (count($data)>0)
			{
				//Obtiene el listado de emails
				// En modo debug envia siempre a la lista de emails seleccionados
				$debug = $this->config->item('bp.mailing.debug');
				if ( $debug === TRUE )
				{
					$e = preg_split('/\;/', $this->config->item('bp.mailing.debugemails'));
					foreach($e as $em)
					{
						$emails[] = array('cEmail' => $em);
					}
				}
				else
				{
					$emails = $this->reg->get_emails($id);
				}

				// Envía los emails
				$this->_send_emails($id, $emails, $data, $success, $message, $debug);
			}
			else
			{
				$success = FALSE;
				$message = $this->lang->line('registro_no_encontrado');
			}
		}
		else
		{
			$success = FALSE;
			$message = $this->lang->line('mensaje_faltan_datos');

		}
		// Envía un mensaje
		$this->load->library('Mensajes');
		$this->mensajes->usuario($this->userauth->get_username(), $message);
		$this->out->dialog($success, $message);
	}

	/**
	 * Realiza el envío de un mailing a un email individual
	 * @param int $id Id del mailing
	 * @param string $email Emails separadados por ;
	 * @return JSON
	 */
	function send_uno($id = null, $email = null)
	{
		$this->userauth->roleCheck(($this->auth .'.send'));

		$id = isset($id)?$id:$this->input->get_post('id');
		$email = isset($email)?$email:urldecode($this->input->get_post('email'));
		if ($id && $email)
		{
			$data = $this->reg->load($id);
			if (count($data)>0)
			{
				set_time_limit(0);
				//Obtiene el listado de emails
				$email = preg_split('/[\;\s\n\r\;]/', $email);
				//print_r($email); die();
				foreach ($email as $e)
				{
					if (trim($e) != '')
					{
						$emails[] = array('cEmail' => $e);
					}
				}
				if (count($emails) == 0)
				{
					$this->out->error($this->lang->line('mensaje_faltan_datos'));
				}
				else
				{
					$this->_send_emails($id, $emails, $data, $success, $message);
					$this->out->message($success, $message);
				}
			}
			else
			{
				$this->out->error($this->lang->line('registro_no_encontrado'));
			}
		}
		else
		{
			$this->out->error($this->lang->line('mensaje_faltan_datos'));
		}
	}

	/**
	 * Añade varios emails al mailing
	 * @param int $id Id del mailing
	 * @param string $email Emails separadados por ;, espacios o saltos de línea
	 * @param
	 * @return JSON
	 */
	function add_emails($id = null, $email = null, $texto = null)
	{
		$this->userauth->roleCheck(($this->auth .'.upd'));

		$id = isset($id)?$id:$this->input->get_post('id');
		$email = isset($email)?$email:urldecode($this->input->get_post('email'));
		$texto = isset($texto)?$texto:urldecode($this->input->get_post('texto'));
		if ($id && $email)
		{
			set_time_limit(0);
			$this->load->model('mailing/m_mailingemail');
			//Obtiene el listado de emails
			$emails = preg_split('/[\;\s\n\r\;]/', $email);
			//print_r($email); die();
			if (count($emails) == 0)
			{
				$this->out->error($this->lang->line('mensaje_faltan_datos'));
			}
			else
			{
				$data['nIdMailing'] = $id;
				if(isset($texto)) $data['cOrigen'] = $texto;
				$count = 0;
				foreach($emails as $e)
				{
					if (trim($e) != '')
					{
						$data['cEmail'] = $e;
						$id2 = $this->m_mailingemail->insert($data);
						if ( $id2 > 0 ) $count++;
					}
				}
				$this->out->success(sprintf($this->lang->line('mailing-add-ok'), $count));
			}
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Genera un informe de todos los envíos realizados al email indicado
	 * @param string $email Email
	 * @return HTML
	 */
	function sended_emails($email = null)
	{
		$this->userauth->roleCheck(($this->auth .'.get_list'));

		$email = isset($email)?$email:urldecode($this->input->get_post('email'));
		if ($email)
		{
			$data['mailings'] = $this->reg->sended_email($email);
			$data['email'] = $email;
			$message = $this->load->view('mailing/sended', $data, TRUE);
			// Respuesta
			$this->out->html_file($message, $this->lang->line('mensajes_enviados'). " {$email}", 'iconoReportTab');
		}
		else
		{
			$this->out->error($this->lang->line('mensaje_faltan_datos'));
		}
	}

	/**
	 * Añade los emails de un tema al mailing
	 * @param int $id Id del mailing
	 * @param int $idtema Id del tema
	 * @return JSON
	 */
	function add_tema($id = null, $idtema = null)
	{
		$this->userauth->roleCheck(($this->auth .'.upd'));
		$id = isset($id)?$id:$this->input->get_post('id');
		$idtema = isset($idtema)?$idtema:$this->input->get_post('idtema');
		if ($id && $idtema)
		{
			set_time_limit(0);
			$count = $this->reg->add_tema($id, $idtema);
			$this->out->success(sprintf($this->lang->line('mailing-add-ok'), $count));
		}
		else
		{
			$this->out->error($this->lang->line('mensaje_faltan_datos'));
		}
	}

	/**
	 * Añade todos los emails del sistema
	 * @param int $id Id del mailing
	 * @return JSON
	 */
	function add_todos($id = null)
	{
		$this->userauth->roleCheck(($this->auth .'.upd'));
		$id = isset($id)?$id:$this->input->get_post('id');
		if ($id )
		{
			set_time_limit(0);
			$count = $this->reg->add_todos($id);
			$this->out->success(sprintf($this->lang->line('mailing-add-ok'), $count));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Promociona en la web los artículos de un boletín
	 * @param int $id Id del boletín
	 * @return MSG
	 */
	function publicar($id = null)
	{
		$this->userauth->roleCheck(($this->auth .'.publicar'));
		$id = isset($id)?$id:$this->input->get_post('id');
		if (is_numeric($id))
		{
			# Se logea en la web
			$this->load->library('Webshop');
			$server = $this->config->item('bp.webshop.server');
			$username = $this->config->item('bp.webshop.username');
			$pasword = $this->config->item('bp.webshop.password');
			$res = $this->webshop->login($server, $username, $pasword);

			if (!$res)
			{
				$this->out->error($this->lang->line('webshop-error-login'));
			}

			# Los datos
			$data = $this->reg->load($id, TRUE);

			# Crea el texto
			$filter = array(
				'title' => $data['cDescripcion'],
				'description' => $data['cAsunto'],
				'text' => $data['cBody']
			);

			# La llamada
			#$this->webshop->debug = TRUE;
			$res = $this->webshop->action('api/blog/text', $filter);
			#var_dump($res); die();

			if (!$res)
			{				
				$this->out->error($this->webshop->get_error());
			}
			if ($res['success'])
			{
				$url = "<a href='javascript:Ext.app.addTabJSONHTMLFILE({
								html_file : \"{$res['url']}\",
								icon : \"iconoWebTab\",
								title : \"{$data['cDescripcion']}\"
							});'>{$res['url']}</a>";
				$this->_add_nota(null, $id, NOTA_INTERNA, $this->lang->line('mailing-publicar-history'));
				$this->out->success(sprintf($this->lang->line('mailing-publicar-ok'), $url));
			}
			$this->out->error($res['message']);
		}
		else
		{
			$this->out->error($this->lang->line('mensaje_faltan_datos'));
		}	
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Controller#_pre_printer($id, $data, $css)
	 */
	protected function _pre_printer($id, &$data, &$css)
	{
		parent::_pre_printer($id, $data, $css);
		$css = $this->config->item('bp.mailing.css');
		return TRUE;
	}
}

/* End of file mailing.php */
/* Location: ./system/application/controllers/mailing/mailing.php */
