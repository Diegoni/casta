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
 * Bibliotecas
 *
 */
class Biblioteca2 extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Biblioteca2
	 */
	function __construct()
	{
		parent::__construct('concursos.biblioteca2', 'concursos/M_biblioteca2', TRUE, null, 'Bibliotecas (antiguo)');
	}
}

/* End of file Biblioteca2.php */
/* Location: ./system/application/controllers/concursos/Biblioteca2.php */
