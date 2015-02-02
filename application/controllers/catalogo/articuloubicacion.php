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
class ArticuloUbicacion extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return ArticuloUbicacion
	 */
	function __construct()	
	{
		parent::__construct('catalogo.articuloubicacion', 'catalogo/M_articuloubicacion', true, null, 'Ubicaciones artículo');
	}
}

/* End of file Articuloubicacion.php */
/* Location: ./system/application/controllers/catalogo/Articuloubicacion.php */