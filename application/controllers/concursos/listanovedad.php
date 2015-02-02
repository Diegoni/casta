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
 * Lista de novedades
 * @author alexl
 *
 */
class Listanovedad extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Listanovedad
	 */
	function __construct()
	{
		parent::__construct('concursos.listanovedad', 'concursos/M_listanovedad', TRUE, 'concursos/listanovedad.js', 'Servicio de Novedades');
	}
}

/* End of file listanovedad.php */
/* Location: ./system/application/controllers/mailing/listanovedad.php */