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
class Teixell extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Teixell
	 */
	function __construct()
	{
		parent::__construct('concursos.teixell', 'concursos/M_teixell', TRUE, null, 'Todos los Teixells');
	}
}

/* End of file Estado.php */
/* Location: ./system/application/controllers/concursos/Estado.php */
