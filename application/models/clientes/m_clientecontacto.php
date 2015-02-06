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

require_once(APPPATH . 'models' . DIRECTORY_SEPARATOR . 'm_perfilcontactomodel.php');

/**
 * Contactos clientes
 *
 */
class M_clientecontacto extends m_perfilcontactomodel
{
	/**
	 * Costructor
	 * @return M_clientecontacto
	 */
	function __construct()
	{
		parent::__construct('Cli_ContactosCliente', 'nIdContactoCliente', 'nIdContacto', 'nIdCliente');
	}

}

/* End of file M_clientecontacto.php */
/* Location: ./system/application/models/clientes/M_clientecontacto.php */