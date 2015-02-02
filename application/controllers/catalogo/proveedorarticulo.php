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
 * Descuentos de un proveedor a un artículo
 * @author alexl
 *
 */
class Proveedorarticulo extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Descuentocliente
	 */
	function __construct()
	{
		parent::__construct('catalogo.proveedorarticulo', 'catalogo/M_proveedorarticulo', TRUE);
	}
}

/* End of file Descuento.php */
/* Location: ./system/application/controllers/catalogo/proveedorarticulo.php */