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
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Líneas de albarán de salida de deposito
 *
 */
class Albaransalidalineadeposito extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return albaransalidalineadeposito
	 */
	function __construct()
	{
		parent::__construct('ventas.albaransalidalineadeposito', 'ventas/m_albaransalidalineadeposito', TRUE, null, 'Líneas albarán de salida deposito');
	}
}

/* End of file albaransalidalineadeposito.php */
/* Location: ./system/application/controllers/ventas/albaransalidalineadeposito.php */