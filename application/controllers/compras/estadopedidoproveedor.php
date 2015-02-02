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
 * Estados de un pedido a proveedor
 *
 */
class Estadopedidoproveedor extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Estadopedidoproveedor
	 */
	function __construct()
	{
		parent::__construct('compras.estadopedidoproveedor', 'compras/M_estadopedidoproveedor', TRUE, null, 'Estados Pedido Proveedor');
	}
}

/* End of file Estadopedidoproveedor.php */
/* Location: ./system/application/controllers/compras/Estadopedidoproveedor.php */