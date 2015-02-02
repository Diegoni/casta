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
 * Estados de una línea de pedido a proveedor
 *
 */
class Estadopedidoproveedorlinea extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Estadopedidoproveedorlinea
	 */
	function __construct()
	{
		parent::__construct('compras.estadopedidoproveedorlinea', 'compras/M_estadopedidoproveedorlinea', TRUE, null, 'Estados Línea Pedido Proveedor');
	}
}

/* End of file Estadopedidoproveedorlinea.php */
/* Location: ./system/application/controllers/compras/Estadopedidoproveedorlinea.php */