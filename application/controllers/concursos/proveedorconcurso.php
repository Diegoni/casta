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
 * Proveedores Concurso
 *
 */
class ProveedorConcurso extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return ProveedorConcurso
	 */
	function __construct()
	{
		parent::__construct('concursos.proveedorconcurso', 'concursos/M_proveedorconcurso', TRUE, null, 'Proveedores Concurso');
	}
}

/* End of file ProveedorConcurso.php */
/* Location: ./system/application/controllers/concursos/ProveedorConcurso.php */
