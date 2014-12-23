<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	app
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Grupos de Mensajes del sistema
 *
 */
class Mensajegrupo extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Mensaje
	 */
	function __construct()
	{
		parent::__construct('sys.mensajegrupo', 'sys/m_mensajegrupo', TRUE, null, 'Grupos de mensajes');
	}

}

/* End of file mensajegrupo.php */
/* Location: ./system/application/controllers/sys/mensajegrupo.php */