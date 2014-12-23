<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	clientes
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Descuentos de un cliente
 * @author alexl
 *
 */
class Descuento extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Descuentocliente
	 */
	function __construct()
	{
		parent::__construct('clientes.descuento', 'clientes/M_descuento', TRUE);
	}
}

/* End of file Descuento.php */
/* Location: ./system/application/controllers/clientes/Descuento.php */