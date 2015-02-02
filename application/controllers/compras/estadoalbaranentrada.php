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
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Estados de un albarán de entrada
 *
 */
class Estadoalbaranentrada extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Estadoalbaranentrada
	 */
	function __construct()
	{
		parent::__construct('compras.estadoalbaranentrada', 'compras/M_estadoalbaranentrada', TRUE, null, 'Estados albarán de entrada');
	}
}

/* End of file estadoalbaranentrada.php */
/* Location: ./system/application/controllers/compras/estadoalbaranentrada.php */