<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	compras
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Estados de albarán de entrada
 *
 */
class Estadoalbaranentradalinea extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Estadoalbaranentradalinea
	 */
	function __construct()
	{
		parent::__construct('compras.estadoalbaranentradalinea', 'compras/M_estadoalbaranentradalinea', TRUE, null, 'Estados líneas de albarán de entrada');
	}
}

/* End of file estadoalbaranentradalinea.php */
/* Location: ./system/application/controllers/compras/estadoalbaranentradalinea.php */