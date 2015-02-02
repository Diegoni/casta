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
 * Editoriales Concurso
 *
 */
class EditorialConcurso extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return EditorialConcurso
	 */
	function __construct()
	{
		parent::__construct('concursos.editorialconcurso', 'concursos/M_editorialconcurso', TRUE, null, 'Editoriales Concurso');
	}
}

/* End of file EditorialConcurso.php */
/* Location: ./system/application/controllers/concursos/EditorialConcurso.php */
