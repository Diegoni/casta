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
 * Estados de una factura 
 *
 */
class Estadofactura extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return estadofactura
	 */
	function __construct()
	{
		parent::__construct('ventas.estadofactura', 'ventas/m_estadofactura', TRUE, null, 'Estados factura');
	}
}

/* End of file estadofactura.php */
/* Location: ./system/application/controllers/ventas/estadofactura.php */