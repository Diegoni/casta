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
 * Secciones de un artículo
 *
 */
class Articulocodigo extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Articulocodigo
	 */
	function __construct()
	{
		parent::__construct('catalogo.articulocodigo', 'catalogo/M_articulocodigo', true, null, 'Códigos artículo');
	}
}

/* End of file Articulocodigo.php */
/* Location: ./system/application/controllers/catalogo/Articulocodigo.php */