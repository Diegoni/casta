<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	mailing
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

require_once(APPPATH . 'models' . DIRECTORY_SEPARATOR . 'm_perfilemailmodel.php');

/**
 * Emails contacto
 *
 */
class M_contactoemail extends M_perfilemailmodel
{
	/**
	 * Costructor
	 * @return M_contactodireccion
	 */
	function __construct()
	{
		parent::__construct('Mailing_EMailsContacto', 'nIdEmailContacto', 'nIdEmail', 'nIdContacto');
	}
}

/* End of file M_contactoemail.php */
/* Location: ./system/application/models/mailing/M_contactoemail.php */