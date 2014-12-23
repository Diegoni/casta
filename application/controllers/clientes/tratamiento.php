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
 * Tratamientos de cliente
 *
 */
class Tratamiento extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return App
	 */
	function __construct()	
	{
		parent::__construct('clientes.tratamiento', 'clientes/M_Tratamiento', true, null, 'Tratamientos Cliente');
	}
}

/* End of file tipocliente.php */
/* Location: ./system/application/controllers/clientes/tipocliente.php */