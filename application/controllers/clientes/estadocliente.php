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
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Estados de cliente
 *
 */
class Estadocliente extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Estadocliente
	 */
	function __construct()	
	{
		parent::__construct('clientes.estadocliente', 'clientes/M_estadocliente', true, null, 'Estados Cliente');
	}
}

/* End of file Estadocliente.php */
/* Location: ./system/application/controllers/clientes/Estadocliente.php */