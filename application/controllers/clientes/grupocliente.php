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
 * Grupos de cliente
 *
 */
class GrupoCliente extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return GrupoCliente
	 */
	function __construct()	
	{
		parent::__construct('clientes.grupocliente', 'clientes/M_Grupocliente', true, null, 'Grupos Cliente');
	}
}

/* End of file grupocliente.php */
/* Location: ./system/application/controllers/clientes/grupocliente.php */