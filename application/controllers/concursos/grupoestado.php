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
 * GrupoEstados
 *
 */
class GrupoEstado extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return GrupoEstado
	 */
	function __construct()
	{
		parent::__construct('concursos.grupoestado', 'concursos/M_grupoestado', TRUE, null, 'Grupos de Estados');
	}
}

/* End of file GrupoEstado.php */
/* Location: ./system/application/controllers/concursos/GrupoEstado.php */
