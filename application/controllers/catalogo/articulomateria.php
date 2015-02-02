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
 * Materias de un artículo 
 *
 */
class ArticuloMateria extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return ArticuloMateria
	 */
	function __construct()	
	{
		parent::__construct('catalogo.articulomateria', 'catalogo/M_articulomateria', true, null, 'Materias artículo');
	}
}

/* End of file ArticuloMateria.php */
/* Location: ./system/application/controllers/catalogo/ArticuloMateria.php */