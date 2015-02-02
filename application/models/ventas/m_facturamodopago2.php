<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	ventas
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @filesource
 */

require_once(APPPATH . 'models' . DIRECTORY_SEPARATOR . 'ventas' . DIRECTORY_SEPARATOR . 'm_facturamodopago.php');

/**
 * Modos de pago de factura temporales
 *
 */
class M_facturamodopago2 extends M_facturamodopago
{
	/**
	 * Costructor
	 * @return M_facturamodopago2
	 */
	function __construct()
	{
		parent::__construct('Doc_FacturasModosPago2');
	}
}

/* End of file M_facturamodopago2.php */
/* Location: ./system/application/models/ventas/M_facturamodopago2.php */