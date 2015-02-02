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
 * Estados de una devolución
 *
 */
class Estadodevolucion extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Estadodevolucion
	 */
	function __construct()
	{
		parent::__construct('compras.estadodevolucion', 'compras/M_estadodevolucion', TRUE, null, 'Estados devolución');
	}
}

/* End of file Estadodevolucion.php */
/* Location: ./system/application/controllers/compras/Estadodevolucion.php */