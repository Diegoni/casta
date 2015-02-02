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
 * Autores de un artículo 
 *
 */
class ArticuloAutor extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Articuloautor
	 */
	function __construct()	
	{
		parent::__construct('catalogo.articuloautor', 'catalogo/M_articuloautor', true, null, 'Artículo-Autor');
	}
}

/* End of file Articuloautor.php */
/* Location: ./system/application/controllers/catalogo/Articuloautor.php */