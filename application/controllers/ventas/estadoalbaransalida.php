<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	ventas
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Estados de un albarán de salida
 *
 */
class Estadoalbaransalida extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Estadoalbaransalida
	 */
	function __construct()
	{
		parent::__construct('ventas.estadoalbaransalida', 'ventas/M_estadoalbaransalida', TRUE, null, 'Estados albarán de salida');
	}
}

/* End of file Estadoalbaransalida.php */
/* Location: ./system/application/controllers/ventas/Estadoalbaransalida.php */