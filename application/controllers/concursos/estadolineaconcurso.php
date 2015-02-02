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
 * Estado Linea Concurso
 *
 */
class EstadoLineaConcurso extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return EstadoLineaConcurso
	 */
	function __construct()
	{
		parent::__construct('concursos.estadolineaconcurso', 'concursos/M_estadolineaconcurso', TRUE, null, 'Estados línea concurso');
	}
}

/* End of file Biblioteca.php */
/* Location: ./system/application/controllers/concursos/Biblioteca.php */
