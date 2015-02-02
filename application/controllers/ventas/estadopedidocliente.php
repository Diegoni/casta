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
 * Estados de un pedido de cliente 
 *
 */
class Estadopedidocliente extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Estadopedidocliente
	 */
	function __construct()
	{
		parent::__construct('ventas.estadopedidocliente', 'ventas/M_estadopedidocliente', TRUE, null, 'Estados pedido cliente');
	}
}

/* End of file Estadopedidocliente.php */
/* Location: ./system/application/controllers/ventas/Estadopedidocliente.php */