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
 * Estados de una línea de pedido de cliente 
 *
 */
class Estadopedidoclientelinea extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Estadopedidoclientelinea
	 */
	function __construct()
	{
		parent::__construct('ventas.estadopedidoclientelinea', 'ventas/M_estadopedidoclientelinea', TRUE, null, 'Estados líneas de pedido cliente');
	}
}

/* End of file Estadopedidoclientelinea.php */
/* Location: ./system/application/controllers/ventas/Estadopedidoclientelinea.php */