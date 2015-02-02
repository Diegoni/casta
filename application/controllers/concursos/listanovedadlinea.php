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
 * Libros de un boletín
 * @author alexl
 *
 */
class Listanovedadlinea extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Listanovedadlinea
	 */
	function __construct()
	{
		parent::__construct('concursos.listanovedadlinea', 'concursos/M_listanovedadlinea', TRUE);
	}
}

/* End of file Listanovedadlinea.php */
/* Location: ./system/application/controllers/mailing/Listanovedadlinea.php */