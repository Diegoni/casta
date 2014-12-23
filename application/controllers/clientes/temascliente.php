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

require_once(APPPATH . 'controllers' . DIRECTORY_SEPARATOR . 'temascontroller.php');
/**
 * Temas de cliente
 *
 */
class TemasCliente extends TemasController
{
	/**
	 * Constructor
	 *
	 * @return TemasCliente
	 */
	function __construct()
	{
		parent::__construct('clientes.temascliente', 'clientes/M_temascliente');
	}

}
/* End of file TemasCliente.php */
/* Location: ./system/application/controllers/clientes/TemasCliente.php */