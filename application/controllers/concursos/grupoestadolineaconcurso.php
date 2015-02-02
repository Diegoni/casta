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
 * Grupos Estado Linea Concurso
 *
 */
class GrupoEstadoLineaConcurso extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return GrupoEstadoLineaConcurso
	 */
	function __construct()
	{
		parent::__construct('concursos.grupoestadolineaconcurso', 'concursos/M_grupoestadolineaconcurso', TRUE, null, 'Grupos de Estados');
	}
}

/* End of file Biblioteca.php */
/* Location: ./system/application/controllers/concursos/Biblioteca.php */
