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

require_once(APPPATH . 'models' . DIRECTORY_SEPARATOR . 'm_perfilemailmodel.php');

/**
 * Emails cliente
 *
 */
class M_clienteemail extends M_perfilemailmodel
{
	/**
	 * Costructor
	 * @return M_clienteemail
	 */
	function __construct()
	{
		parent::__construct('Cli_EMailsCliente', 'nIdEmailCliente', 'nIdEmail', 'nIdCliente');
	}
}

/* End of file M_clienteemail.php */
/* Location: ./system/application/models/clientes/M_clienteemail.php */