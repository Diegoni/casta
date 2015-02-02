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
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

require_once(APPPATH . 'models' . DIRECTORY_SEPARATOR . 'm_temasmodel.php');

/**
 * Temas de un contacto
 *
 */
class M_temascontacto extends M_temasmodel
{
	/**
	 * Costructor
	 * @return M_temascontacto
	 */
	function __construct()
	{
		parent::__construct('Mailing_TemasContacto', 'nIdTemasContacto', 'nIdContacto');
		$this->_cache = TRUE;
	}
}

/* End of file M_temascontacto.php */
/* Location: ./system/application/models/mailing/M_temascontacto.php */