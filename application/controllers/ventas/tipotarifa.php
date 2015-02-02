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
 * Tipos de tarifas
 *
 */
class TipoTarifa extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return TipoTarifa
	 */
	function __construct()	
	{
		parent::__construct('ventas.tipotarifa', 'ventas/M_Tipotarifa', true, null, 'Tipos Tarifa');
	}
}

/* End of file tipotarifa.php */
/* Location: ./system/application/controllers/ventas/tipotarifa.php */