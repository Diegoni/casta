<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	clientes
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

require_once(APPPATH . 'models' . DIRECTORY_SEPARATOR . 'm_perfildireccionmodel.php');

/**
 * Direcciones cliente
 *
 */
class M_clientedireccion extends M_perfildireccionmodel
{
	/**
	 * Costructor
	 * @return M_clientedireccion
	 */
	function __construct()
	{
		parent::__construct('Cli_DireccionesCliente', 'nIdDireccionCliente', 'nIdDireccion', 'nIdCliente');
	}
}

/* End of file M_clientedireccion.php */
/* Location: ./system/application/models/clientes/M_clientedireccion.php */