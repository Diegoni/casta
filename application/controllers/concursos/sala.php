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
class Sala extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Sala
	 */
	function __construct()
	{
		parent::__construct('concursos.sala', 'concursos/M_sala', TRUE, null, 'Salas');
	}
}

/* End of file sala.php */
/* Location: ./system/application/controllers/concursos/sala.php */
