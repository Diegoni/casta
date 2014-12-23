<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	clientes
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Tarifas de cliente
 *
 */
class ClienteTarifa extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return ClienteTarifa
	 */
	function __construct()	
	{
		parent::__construct('clientes.clientetarifa', 'clientes/M_clientetarifa', true, null, 'Tarifas Cliente');
	}
}

/* End of file ClienteTarifa.php */
/* Location: ./system/application/controllers/clientes/ClienteTarifa.php */