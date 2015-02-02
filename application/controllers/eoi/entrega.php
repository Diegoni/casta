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
 * EOI - Puntos de entrega
 *
 */
class Entrega extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Entrega
	 */
	function __construct()
	{
		parent::__construct('eoi.entrega', 'eoi/M_entrega', TRUE, null, 'Entregas');
	}	
}

/* End of file entrega.php */
/* Location: ./system/application/controllers/eoi/entrega.php */
