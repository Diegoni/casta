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

require_once(APPPATH . 'models' . DIRECTORY_SEPARATOR . 'm_perfiltelefonomodel.php');

/**
 * Telefono contacto
 *
 */
class M_contactotelefono extends M_perfiltelefonomodel
{
	/**
	 * Constructor
	 * @return M_contactotelefono
	 */
	function __construct()
	{
		parent::__construct('Mailing_TelefonosContacto', 'nIdTelefonoContacto', 'nIdTelefono', 'nIdContacto');
	}
}

/* End of file M_contactotelefono.php */
/* Location: ./system/application/models/mailing/M_contactotelefono.php */