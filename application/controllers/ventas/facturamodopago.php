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
 * @copyright	Copyright (c) 2008-20100, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Modos de pago de Factura
 *
 */
class FacturaModoPago extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return FacturaModoPago
	 */
	function __construct()
	{
		parent::__construct('ventas.facturamodopago', 'ventas/M_facturamodopago', TRUE, null, 'Cobros Factura');
	}

}

/* End of file facturamodopago.php */
/* Location: ./system/application/controllers/ventas/facturamodopago.php */