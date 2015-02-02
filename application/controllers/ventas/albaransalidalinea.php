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
 * Líneas de albarán de salida
 *
 */
class Albaransalidalinea extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Albaransalidalinea
	 */
	function __construct()
	{
		parent::__construct('ventas.albaransalidalinea', 'ventas/m_albaransalidalinea', TRUE, null, 'Líneas albarán de salida');
	}
}

/* End of file Albaransalidalinea.php */
/* Location: ./system/application/controllers/ventas/Albaransalidalinea.php */