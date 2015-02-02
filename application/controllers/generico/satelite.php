<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	generico
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Satelites
 *
 */
class Satelite extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Satelite
	 */
	function __construct()
	{
		parent::__construct('generico.satelite', 'generico/M_Satelite', true, null, 'Sucursales');
	}

}
/* End of file satelite.php */
/* Location: ./system/application/controllers/generico/satelite.php */