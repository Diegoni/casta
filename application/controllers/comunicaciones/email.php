<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	comunicaciones
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Controlador de Envíos de email
 *
 */
class Email extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Email
	 */
	function __construct()
	{
		parent::__construct('comunicaciones.email', null, TRUE, 'comunicaciones/email.js', 'Email');
	}

	/**
	 * Ventana de envío de un email
	 * @param string $to Direcciones TO separadas por , o ;
	 * @param string $cc Direcciones CC separadas por , o ;
	 * @param string $cco Direcciones CCO separadas por , o ;
	 * @param string $subjet Asunto
	 * @param string $msg Mensaje a enviar
	 * @param string $file Fichro adjunto
	 */
	function index($to = null, $cc = null, $cco = null, $subjet = null, $msg = null, $file = null)
	{
		$this->userauth->roleCheck($this->auth . '.index');

		$to 		= isset($to)?$to:urldecode($this->input->get_post('to'));
		$cc 		= isset($cc)?$cc:urldecode($this->input->get_post('cc'));
		$cco 		= isset($cco)?$cco:urldecode($this->input->get_post('cco'));
		$subject 	= isset($subject)?$subject:urldecode($this->input->get_post('subject'));
		$msg 		= isset($msg)?$msg:urldecode($this->input->get_post('msg'));
		$file 		= isset($file)?$file:urldecode($this->input->get_post('file'));

		$this->load->helper('asset');
		$data['to'] = $to;
		$data['cc'] = $cc;
		$data['cco'] = $cco;
		$data['css'] = css_asset_url($this->config->item('bp.documentos.css'));
		$data['subject'] = $subject;
		$text = $this->load->view('main/email', array('texto_email' => $msg), TRUE);
		$data['msg'] = $text;
		$data['file'] = $file;

		$this->_show_form('index', 'comunicaciones/email.js', $this->lang->line('Email'), null, null, null, $data);
	}

	/**
	 * Envia el email
	 * @param string $to Direcciones TO separadas por , o ;
	 * @param string $cc Direcciones CC separadas por , o ;
	 * @param string $cco Direcciones CCO separadas por , o ;
	 * @param string $subjet Asunto
	 * @param string $msg Mensaje a enviar
	 */
	function send($to = null, $cc = null, $cco = null, $subjet = null, $msg = null)
	{
		$this->userauth->roleCheck($this->auth . '.send');

		$to 		= isset($to)?$to:$this->input->get_post('to');
		$cc 		= isset($cc)?$cc:$this->input->get_post('cc');
		$cco 		= isset($cco)?$cco:$this->input->get_post('cco');
		$subject 	= isset($subject)?$subject:$this->input->get_post('subject');
		$msg 		= isset($msg)?$msg:$this->input->get_post('msg');
		$file 		= isset($file)?$file:$this->input->get_post('file');

		if (($to || $cc || $cco) && $msg && $subject)
		{
			$to = isset($to)?preg_split('/[;|,|\s]/', $to):null;
			$cc = isset($cc)?preg_split('/[;|,|\s]/', $cc):null;
			$cco = isset($cco)?preg_split('/[;|,|\s]/', $cco):null;
			$file = isset($file)?preg_split('/;/', $file):null;
			$filepath = DIR_TEMP_PATH;
			if (isset($file))
			{
				$this->load->library('PdfLib');
				$this->load->library('HtmlFile');
				foreach($file as $k => $v)
				{
					if (trim($v) != '')
					{
						$name = pathinfo($v);
						$ext = strtolower($name['extension']);
						if ($ext == 'html')
						{
							$name = $name['filename'] . '.pdf';
							$fout = $this->htmlfile->pathfile($name);
							if (!file_exists($fout)) $this->pdflib->create($this->htmlfile->pathfile($v), $fout, null, null, FALSE, FALSE);
							$file[$k] = $fout;
						}
						else
						{
							$file[$k] = $filepath . $v;
						}
					}
					else
					{
						unset($file[$k]);
					}
				}
			}
			if ($this->config->item('sender.cc') === TRUE)
			{
				$this->load->library('Configurator');
				$cc[] = $this->configurator->user('bp.email.from');
			}
			$this->load->library('Emails');
			set_time_limit(0);
			$res = $this->emails->send($subject, $msg, $to, $cc, $cco, $file, $this->config->item('bp.documentos.css'));
			if ($res === TRUE)
			{
				$dir = array();
				$dir = (count($to) > 0)? array_merge($dir, $to): $dir;
				$dir = (count($cc) > 0)? array_merge($dir, $cc): $dir;
				$dir = (count($cco) > 0)? array_merge($dir, $cco): $dir;
				$this->out->success(sprintf($this->lang->line('email-email-enviado'), $subject, implode(', ', $dir)));
			}
			else
			{
				$this->out->error($res);
			}
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}
}

/* End of file sms.php */
/* Location: ./system/application/controllers/comunicaciones/sms.php */
