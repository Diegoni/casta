<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
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
 * Envio de documentos por FAX o Email
 * @author alexl
 *
 */
class Sender
{

	/**
	 * Instancia de CI
	 * @var CI
	 */
	private $obj;

	/**
	 * Constructor
	 * @return Sender
	 */
	function __construct()
	{
		$this->obj = &get_instance();
		$this->obj->load->model('perfiles/m_perfil');

		log_message('debug', 'Sender Class Initialised via ' . get_class($this->obj));
	}

	/**
	 * Envía un documento por un medio telemático
	 * @param int $id Id del documento
	 * @param int $profile Datos para el envío
	 * @param bool $email TRUE: Usar email
	 * @param bool $fax TRUE: usar FAX
	 * @param bool $sinli TRUE: usar SINLI
	 * @return array 'success' => TRUE, ha ido bien
	 * 	'media' => modo de envio
	 *  'dest' => Destino
	 */
	function send($id, $profile, $email = TRUE, $fax = TRUE, $sinli = TRUE)
	{
		// SINLI, Emails, Faxes?
		if ($sinli)
		{
			if (isset($profile['sinli']) && isset($profile['sinliemail']) && isset($profile['sinlitipo']))
			{
				$this->obj->load->library('SinliLib');
				$res = $this->obj->sinlilib->send($profile['sinlitipo'], $profile['data'], $profile['sinli'], $profile['sinliemail']);
				if ($res === TRUE)
				{
					#$this->reg->update($id, array('bEnviadoSINLI' => time()));
					return array(
							'success' => TRUE,
							'media' => $this->obj->lang->line('SINLI'),
							'dest' => $profile['sinli']
					);
				}
				else
				{
					return array(
							'success' => FALSE,
							'media' => $this->obj->lang->line('SINLI'),
							'message' => $this->obj->sinlilib->get_error()
					);
				}
			}
		}
		if ($email)
		{
			$this->obj->load->library('Configurator');

			$emails = $profile['emails']->get_list($profile['id']);
			$em = $this->obj->utils->get_profile($emails, $profile['perfil']);
			if (isset($em))
			{
				$cc = ($this->obj->config->item('sender.cc') === TRUE) ? array($this->obj->configurator->user('bp.email.from')) : null;
				$debug = $this->obj->config->item('sender.debug');
				if ($debug != FALSE)
				{
					$e = preg_split('/\;/', $debug);
					foreach ($e as $em)
					{
						$to[] = trim($em);
					}
				}
				else
				{
					$to[] = trim($em['text']);
				}

				// Texto del email
				$html = $profile['controller']->show_report($profile['subject'], $profile['data'], $profile['report_email'], null, FALSE, $profile['report_lang'], FALSE, FALSE);
				// Pedido
				$this->obj->load->library('HtmlFile');

				#var_dump($profile['report_lang']); die();
				$filename = $profile['controller']->printer($id, $profile['report_normal'], $profile['subject'], FALSE, null, null, null, TRUE, $profile['report_lang']);
				$this->obj->load->library('PdfLib');
				$pdf = $this->obj->pdflib->create($this->obj->htmlfile->pathfile($filename), null, null, null, FALSE, FALSE);

				$this->obj->load->library('Emails');
				set_time_limit(0);
				$res = $this->obj->emails->send($profile['subject'], $html, $to, $cc, null, $this->obj->htmlfile->pathfile($pdf), $profile['css']);
				if ($res === TRUE)
				{
					return array(
							'success' => TRUE,
							'media' => $this->obj->lang->line('EMAIL-PDF'),
							'dest' => $to[0]
					);
				}
				else
				{
					return array(
							'success' => FALSE,
							'media' => $this->obj->lang->line('EMAIL-PDF'),
							'message' => $res
					);
				}
			}
		}
		return array(
				'success' => FALSE,
				'media' => $this->obj->lang->line('NINGUNA'),
				'message' => $this->obj->lang->line('sender-no-media-imprimir')
		);

		if ($fax)
		{
			$faxes = $profile['faxes']->get_list($profile['id']);
			foreach ($faxes as $k => $fax)
			{
				if (!$fax['bFax'])
					unset($faxes[$k]);
			}
		}

	}

	/**
	 * Indica si un número es de un teléfono móvil
	 * @param string $num Númerp
	 * @return bool
	 */
	function is_mobile($num)
	{
		$num = trim($num);
		return (substr($num, 0, 1) == '6');
	}

	/**
	 * Busca el teléfono móvil del perfil del tipo indicado. Si no encuentra el tipo, devuelve un
	 * general, y si no hay general
	 * el primero de ellos
	 * @param array $profiles Perfiles
	 * @param mixed $type int: Id del perfil, array: perfiles posibles por orden de
	 * preferencia
	 * @return array, registro de perfil
	 */
	function get_mobile($profiles, $types = PERFIL_GENERAL)
	{
		$general = null;
		if (!is_array($types))
			$types = array($types);
		if (count($profiles) > 0)
		{
			foreach ($types as $type)
			{
				foreach ($profiles as $perfil)
				{
					if ($perfil['id_perfil'] == $type && $this->is_mobile($perfil['text']))
						return $perfil;
					if (!isset($general) && $perfil['id_perfil'] == PERFIL_GENERAL && $this->is_mobile($perfil['text']))
						$general = $perfil;
				}
			}
			if (isset($general))
				return $general;
			foreach ($profiles as $perfil)
			{
				if ($this->is_mobile($perfil['text']))
				{
					return $perfil;
				}
			}
		}
		return null;
	}

}

/* End of file sender.php */
/* Location: ./system/libraries/sender.php */
