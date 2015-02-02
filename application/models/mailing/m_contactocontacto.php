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

require_once(APPPATH . 'models' . DIRECTORY_SEPARATOR . 'm_perfilcontactomodel.php');

/**
 * Contactos contacto
 *
 */
class M_contactocontacto extends m_perfilcontactomodel
{
	/**
	 * Costructor
	 * @return M_contactocontacto
	 */
	function __construct()
	{
		parent::__construct('Mailing_ContactosContacto', 'nIdContactoContacto', 'nIdContactoMailing', 'nIdContacto');
	}

}

/* End of file M_contactocontacto.php */
/* Location: ./system/application/models/mailing/M_contactocontacto.php */