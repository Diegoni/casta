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
 * @version		$Rev: 435 $
 * @filesource
 */

require_once(APPPATH . 'models' . DIRECTORY_SEPARATOR . 'ventas' . DIRECTORY_SEPARATOR . 'm_albaransalidalinea.php');
/**
 * Líneas de albarán de salida temporales
 *
 */
class M_albaransalidalinea2 extends M_albaransalidalinea
{
	/**
	 * Constructor
	 * @return M_albaransalidalinea
	 */
	function __construct()
	{
		parent::__construct('Doc_LineasAlbaranesSalida2', 'albaransalida2', 'ventas/m_albaransalida2');
	}

}

/* End of file M_albaransalidalinea.php */
/* Location: ./system/application/models/compras/M_albaransalidalinea.php */
