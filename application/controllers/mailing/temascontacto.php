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
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

require_once(APPPATH . 'controllers' . DIRECTORY_SEPARATOR . 'temascontroller.php');

/**
 * Temas de un contacto
 *
 */
class TemasContacto extends TemasController
{

	/**
	 * Constructor
	 *
	 * @return TemasContacto
	 */
	function __construct()
	{
		parent::__construct('mailing.temascontacto', 'mailing/M_temascontacto');
	}
}

/* End of file TemasContacto.php */
/* Location: ./system/application/controllers/mailing/TemasContacto.php */