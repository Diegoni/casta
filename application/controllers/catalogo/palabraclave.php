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
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Palabras clave 
 *
 */
class Palabraclave extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Palabraclave
	 */
	function __construct()	
	{
		parent::__construct('catalogo.palabraclave', 'catalogo/M_palabraclave', true, null, 'Palabras clave');
	}
}

/* End of file Articuloautor.php */
/* Location: ./system/application/controllers/catalogo/Articuloautor.php */