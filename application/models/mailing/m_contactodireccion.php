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

require_once(APPPATH . 'models' . DIRECTORY_SEPARATOR . 'm_perfildireccionmodel.php');

/**
 * Direcciones contacto
 *
 */
class M_contactodireccion extends M_perfildireccionmodel
{
	/**
	 * Costructor
	 * @return M_contactodireccion
	 */
	function __construct()
	{
		parent::__construct('Mailing_DireccionesContacto', 'nIdDireccionContacto', 'nIdDireccion', 'nIdContacto');
	}
}

/* End of file M_contactodireccion.php */
/* Location: ./system/application/models/mailing/M_contactodireccion.php */