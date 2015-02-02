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
class Teixell2 extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Teixell2
	 */
	function __construct()
	{
		parent::__construct('concursos.teixell2', 'concursos/M_teixell2', TRUE, null, 'Todos los Teixells Directos', 'concursos/submenuteixells2.js');
	}
}

/* End of file Estado.php */
/* Location: ./system/application/controllers/concursos/Estado.php */
