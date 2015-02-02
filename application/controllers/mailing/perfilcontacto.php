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

require_once(APPPATH . 'controllers' . DIRECTORY_SEPARATOR . 'perfilescontroller.php');

/**
 * Perfiles de un contacto
 *
 */
class PerfilContacto extends PerfilController
{

	/**
	 * Constructor
	 *
	 * @return PerfilContacto
	 */
	function __construct()
	{
		parent::__construct('mailing.perfilcontacto');

		$this->_idref = 'nIdContacto';
		$this->_config['E'] = array('perfiles/M_email', 'mailing/M_contactoemail', 'nIdEmail');
		$this->_config['C'] = array('perfiles/M_contacto', 'mailing/M_contactocontacto', 'nIdContactoMailing', 'nIdContacto');
		$this->_config['T'] = array('perfiles/M_telefono', 'mailing/M_contactotelefono', 'nIdTelefono');
		$this->_config['D'] = array('perfiles/M_direccion', 'mailing/M_contactodireccion', 'nIdDireccion');
	}
}
/* End of file perfilcontacto.php */
/* Location: ./system/application/controllers/mailing/perfilcontacto.php */