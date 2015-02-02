<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	stocks
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Tipos de Stock
 *
 */
class Tipostock extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Tipostock
	 */
	function __construct()
	{
		parent::__construct('stocks.tipostock', 'stocks/M_tipostock', TRUE, null, 'Tipos stock');
	}
}

/* End of file tipostock.php */
/* Location: ./system/application/controllers/stocks/tipostock.php */