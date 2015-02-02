<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	pedidos cliente
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @filesource
 */

/**
 * EOI - Títulos de venta por internet
 *
 */
class Titulo extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Titulo
	 */
	function __construct()
	{
		parent::__construct('eoi.titulo', 'eoi/M_titulo', TRUE, null, 'Títulos');
	}
	
}

/* End of file titulo.php */
/* Location: ./system/application/controllers/eoi/titulo.php */
