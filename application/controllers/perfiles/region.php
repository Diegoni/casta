<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	perfiles
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Regiones
 *
 */
class Region extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return App
	 */
	function __construct()
	{
		parent::__construct('perfiles.region', 'perfiles/M_Region', TRUE, null, 'Regiones');
	}
}

/* End of file region.php */
/* Location: ./system/application/controllers/perfiles/region.php */