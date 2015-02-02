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
 * Estados
 *
 */
class Estado extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Estado
	 */
	function __construct()
	{
		parent::__construct('concursos.estado', 'concursos/M_estado', TRUE, null, 'Estados');
	}
}

/* End of file Estado.php */
/* Location: ./system/application/controllers/concursos/Estado.php */
