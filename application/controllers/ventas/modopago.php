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
 * Modos de pago
 *
 */
class Modopago extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Modopago
	 */
	function __construct()	
	{
		parent::__construct('ventas.modopago', 'ventas/M_modopago', true, null, 'Modos de Pago');
	}
}

/* End of file modopago.php */
/* Location: ./system/application/controllers/ventas/modopago.php */