<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	catalogo
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Tipos de libro
 *
 */
class Tipolibro extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return App
	 */
	function __construct()
	{
		parent::__construct('catalogo.tipolibro', 'catalogo/M_tipolibro', TRUE, null, 'Tipos Libro');
	}

}

/* End of file Tipolibro.php */
/* Location: ./system/application/controllers/catalogo/Tipolibro.php */
