<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	concursos
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Configuraciones
 *
 */
class Configuracion extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Configuracion
	 */
	function __construct()
	{
		parent::__construct('concursos.configuracion', 'concursos/M_configuracion', TRUE, null, 'Configuración');
	}

}

/* End of file configuracion.php */
/* Location: ./system/application/controllers/concursos/configuracion.php */
