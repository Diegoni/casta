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
 * Portadas de los artículos
 *
 */
class Portada extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return portada
	 */
	function __construct()
	{
		parent::__construct('catalogo.portada', 'catalogo/M_portada', TRUE, null, 'Portadas');
	}
}

/* End of file portada.php */
/* Location: ./system/application/controllers/catalogo/portada.php */
