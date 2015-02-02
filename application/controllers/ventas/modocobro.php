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
 * Modos de cobro
 *
 */
class ModoCobro extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return ModoCobro
	 */
	function __construct()	
	{
		parent::__construct('ventas.modocobro', 'ventas/M_Modocobro', true, null, 'Modos Cobro');
	}
}

/* End of file modocobro.php */
/* Location: ./system/application/controllers/ventas/modocobro.php */