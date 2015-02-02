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
	 * @return App
	 */
	function __construct()	
	{
		parent::__construct('mailing.grupocliente', 'mailing/M_Grupocliente', true, null, 'Grupos Contacto');
	}
}

/* End of file grupocliente.php */
/* Location: ./system/application/controllers/mailing/grupocliente.php */