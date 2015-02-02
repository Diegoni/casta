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
 * Tarifas envío gramos
 *
 */
class Tarifasenviogramos extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Tarifasenviogramos
	 */
	function __construct()
	{
		parent::__construct('ventas.tarifasenviogramos', 'ventas/m_tarifasenviogramos', true, null, 'Tarifas de envío por gramos');
	}
}

/* End of file tarifasenviogramos.php */
/* Location: ./system/application/controllers/ventas/tarifasenviogramos.php */