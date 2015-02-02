<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	web
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Newsletter
 *
 */
class Newsletter extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return App
	 */
	function __construct()
	{
		parent::__construct('web.newsletter', 'web/M_newsletter', TRUE, null, 'Boletín de noticias');
	}

	/**
	 * Consulta los temas a los que está suscrito un email
	 * @param string $email Email a consultar
	 * @return JSON
	 */
	function temas_email($email = null)
	{
		$email = isset($emai) ? $email : $this->input->get_post('email');
		if ($email)
		{
			$data = $this->reg->temas_email($email);
			$this->out->data($data);
		}
		else
		{
			$this->out->error($this->lang->line('mensaje_faltan_datos'));
		}
	}

	/**
	 * Consulta los temas a los que está suscrito un email
	 * @param string $email Email a consultar
	 * @return JSON
	 */
	function add_temas($email = null, $temas = null)
	{
		$email = isset($emai) ? $email : $this->input->get_post('email');
		$temas = isset($temas) ? $temas : $this->input->get_post('Temas');
		if ($email)
		{
			// Borra todas las suscripciones anteriores
			$this->reg->del_general($email);
			// Añade las nuevas
			$ids = preg_split('/;/', $temas);
			$data['cEmail'] = $email;
			foreach ($ids as $id)
			{
				$data['nIdTema'] = $id;
				$this->reg->insert($data);
			}
			$this->out->success();
		}
		else
		{
			$this->out->error($this->lang->line('mensaje_faltan_datos'));
		}
	}

}

/* End of file newsletter.php */
/* Location: ./system/application/controllers/web/newsletter.php */
