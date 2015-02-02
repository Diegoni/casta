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
 * Tipos de cliente
 *
 */
class TipoCliente extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return App
	 */
	function __construct()	
	{
		parent::__construct('mailing.tipocliente', 'mailing/M_Tipocliente', true, null, 'Tipos Contacto');
	}
}

/* End of file tipocliente.php */
/* Location: ./system/application/controllers/mailing/tipocliente.php */