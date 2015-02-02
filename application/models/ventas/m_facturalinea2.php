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

require_once(APPPATH . 'models' . DS . 'ventas' .DS . 'm_facturalinea.php');

/**
 * Líneas de factura temporales
 *
 */
class M_facturalinea2 extends M_facturalinea
{
	/**
	 * Constructor
	 * @return M_facturalinea2
	 */
	function __construct()
	{
		parent::__construct('Doc_AlbaranesSalida2', 'Doc_LineasAlbaranesSalida2', 'ventas/m_factura2', 'ventas/m_albaransalida2', 'albaransalida2');
	}
}

/* End of file M_facturalinea2.php */
/* Location: ./system/application/models/compras/M_facturalinea2.php */
