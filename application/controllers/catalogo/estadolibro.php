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
 * Estados de libro
 *
 */
class Estadolibro extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Estadolibro
	 */
	function __construct()
	{
		parent::__construct('catalogo.estadolibro', 'catalogo/M_estadolibro', TRUE, null, 'Estados Libro');
	}

}

/* End of file Estadolibro.php */
/* Location: ./system/application/controllers/catalogo/Estadolibro.php */
