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
 * Información del estado del pedido de un cliente
 *
 */
class Informacioncliente extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Informacioncliente
	 */
	function __construct()
	{
		parent::__construct('ventas.informacioncliente', 'ventas/M_informacioncliente', TRUE, null, 'Información cliente');
	}
}

/* End of file Informacioncliente.php */
/* Location: ./system/application/controllers/compras/Informacioncliente.php */