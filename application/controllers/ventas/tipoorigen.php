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
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Tipos de origen pedidos
 *
 */
class Tipoorigen extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Tipoorigen
	 */
	function __construct()	
	{
		parent::__construct('ventas.tipoorigen', 'ventas/M_Tipoorigen', true, null, 'Tipos Origen Pedido');
	}
}

/* End of file tipoorigen.php */
/* Location: ./system/application/controllers/ventas/tipotarifa.php */