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

require_once(APPPATH . 'models' . DIRECTORY_SEPARATOR . 'm_perfiltelefonomodel.php');

/**
 * Telefono cliente
 *
 */
class M_clientetelefono extends M_perfiltelefonomodel
{
	/**
	 * Constructor
	 * @return M_clientetelefono
	 */
	function __construct()
	{
		parent::__construct('Cli_TelefonosCliente', 'nIdTelefonoCliente', 'nIdTelefono', 'nIdCliente');
	}
}

/* End of file M_clientetelefono.php */
/* Location: ./system/application/models/clientes/M_clientetelefono.php */