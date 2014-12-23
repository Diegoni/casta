<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	libraries
 * @category	core
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Gestor de Logs de la aplicación
 * @author alexl
 *
 */
class Emails {

	/**
	 * Instancia de CI
	 * @var CI
	 */
	private $obj;

	/**
	 * Constructor
	 * @return Email
	 */
	function __construct()
	{
		$this->obj =& get_instance();

		log_message('debug', 'Email Class Initialised via '.get_class($this->obj));
	}

	protected function _clean(&$to)
	{
		if (count($to) == 0) return TRUE;
		foreach($to as $k => $e)
		{
			if (trim($e) != '')
			{
				if (!Mailer::valid_email($e))
				{
					return $e;
				}
			}
			else
			{
				unset($to[$k]);
			}
		}
		return TRUE;
	}

	/**
	 * Envía un email
	 * @param string $subject Asunto
	 * @param string $body Cuerpo del mensaje
	 * @param array $to To
	 * @param array $cc CC
	 * @param array $cco CCO
	 * @param array $files Ficheros adjuntos
	 */
	function send($subject, $body, $to = null, $cc = null, $cco = null, $files = null, $css = null, $from = null, $fromname = null)
	{
		$this->obj->load->plugin('swift');
		$this->obj->load->helper('asset');
		$this->obj->load->helper('extjs');
		$this->obj->load->library('Logger');
		$this->obj->load->library('Configurator');

		$config['Host']     = $this->obj->configurator->user('bp.email.host');
		$config['SMTPAuth'] = $this->obj->configurator->user('bp.email.auth');
		$config['Password'] = $this->obj->configurator->user('bp.email.pass');
		$config['Username'] = $this->obj->configurator->user('bp.email.user');
		$config['From']  	= isset($from)?$from:$this->obj->configurator->user('bp.email.from');
		$config['FromName'] = isset($fromname)?$fromname:$this->obj->configurator->user('bp.email.fromname');

		$config['Mailer']   = $this->obj->config->item('bp.email.protocol');

		// Hoja de estilos CSS
		if (!isset($css))
		{
			$css = $this->obj->config->item('bp.mailing.css');
		}
		if (isset($css))
		{
			$css = css_asset_path($css);
			$css = file_get_contents($css);
		}

		// Email base
		$mail = new Mailer($config);

		$mail->prepare($subject, $body, $css);

		if (isset($files)) $mail->files($files);

		if (($res = $this->_clean($to)) !== TRUE)
		{
			return sprintf($this->obj->lang->line('email-email-erroneo'), $res);
		}
		if (($res = $this->_clean($cc)) !== TRUE)
		{
			return sprintf($this->obj->lang->line('email-email-erroneo'), $res);
		}
		if (($res = $this->_clean($cco)) !== TRUE)
		{
			return sprintf($this->obj->lang->line('email-email-erroneo'), $res);
		}
		$emails = array_merge((count($to) > 0)?$to:array(), (count($cc) > 0)?$cc:array(), (count($cco) > 0)?$cco:array());
		$this->obj->logger->Log("Email: " . sprintf($this->obj->lang->line('email-enviando-emails'), implode(', ', $emails)) , 'email');
		$res = $mail->send($to, $cc, $cco);
		#var_dump($res);
		if ($res['error'] > 0)
		{
			$res = sprintf($this->obj->lang->line('email-envio-erroneos'), implode(', ', $res['failures']));
			$this->obj->logger->Log("Email: " . $res , 'email');
		}
		else
		{
			$res = TRUE;
		}
		return $res;
	}

}


/* End of file email.php */
/* Location: ./system/libraries/email.php */