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
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

require_once(APPPATH . 'models' . DIRECTORY_SEPARATOR . 'm_temasmodel.php');

/**
 * Temas de un cliente
 *
 */
class M_temascliente extends M_temasmodel
{
	/**
	 * Costructor
	 * @return M_temascliente
	 */
	function __construct()
	{
		parent::__construct('Sus_Clientes_Temas', 'nIdClientesTemas', 'nIdCliente');
	}
}

/* End of file M_temascliente.php */
/* Location: ./system/application/models/clientes/M_temascliente.php */