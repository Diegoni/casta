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
 * salas
 *
 */
class Sala2 extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Sala2
	 */
	function __construct()
	{
		parent::__construct('concursos.sala2', 'concursos/M_sala2', TRUE, null, 'Salas (antiguo)');
	}
}

/* End of file sala2.php */
/* Location: ./system/application/controllers/concursos/sala2.php */
