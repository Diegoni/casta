<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	calendario
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Incidencias
 *
 */
class Incidencia extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return App
	 */
	function __construct()	
	{
		parent::__construct('calendario.incidencia', 'calendario/M_Incidencia', true, null, 'Incidencias');
	}
}

/* End of file incidencia.php */
/* Location: ./system/application/controllers/calendario/calendario/incidencia.php */