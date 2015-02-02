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
 * Promociones
 *
 */
class Promocion extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Promocion
	 */
	function __construct()
	{
		parent::__construct('catalogo.promocion', 'catalogo/M_promocion', TRUE, null, 'Promociones');
	}
}

/* End of file Promocion.php */
/* Location: ./system/application/controllers/catalogo/Promocion.php */
