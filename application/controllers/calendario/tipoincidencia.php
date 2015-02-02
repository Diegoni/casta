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
 * Tipos de incidencias
 *
 */
class TipoIncidencia extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return App
	 */
	function __construct()	
	{
		parent::__construct('calendario.tipoincidencia', 'calendario/M_Tipoincidencia', true, null, 'Tipos Incidencia');
	}
}

/* End of file tipoincidencia.php */
/* Location: ./system/application/controllers/calendario/tipoincidencia.php */