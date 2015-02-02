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
 * Webmail
 *
 */
class Webmail extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Webmail
	 */
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Abrel el cliente de email externo
	 */
	function index()
	{
		$this->obj->load->library('Configurator');
		$url 		= $this->obj->configurator->user('bp.webmail.url');
		$password 	= $this->obj->configurator->user('bp.webmail.pass');
		$username 	= $this->obj->configurator->user('bp.webmail.user');
		if ($url && $password && $username)
		{
			$url = str_replace(array('%username%', '%password%'), array($username, $password), $url);
			$this->out->url($url, $this->lang->line('Webmail'), 'iconoWebmailTab');
		}
		$this->out->error($this->lang->line('webmail-no-configurado'));
	}
}

/* End of file webmail.php */
/* Location: ./system/application/controllers/comunicaciones/webmail.php */
