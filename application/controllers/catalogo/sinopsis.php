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
 * Sinopsis de libro
 *
 */
class Sinopsis extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Sinopsis
	 */
	function __construct()
	{
		parent::__construct('catalogo.sinopsis', 'catalogo/M_sinopsis', TRUE, null, 'Sinopsis Artículo');
	}

}

/* End of file sinopsis.php */
/* Location: ./system/application/controllers/catalogo/sinopsis.php */
