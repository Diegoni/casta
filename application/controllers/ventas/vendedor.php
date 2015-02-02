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
 * Vendedores
 *
 */
class Vendedor extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Vendedor
	 */
	function __construct()
	{
		parent::__construct('ventas.vendedor', 'ventas/m_vendedor', true, null, 'Vendedores');
	}
}

/* End of file Vendedor.php */
/* Location: ./system/application/controllers/ventas/Vendedor.php */