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
 * Tipos de promociones
 *
 */
class Tipopromocion extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Tipopromocion
	 */
	function __construct()
	{
		parent::__construct('catalogo.tipopromocion', 'catalogo/M_tipopromocion', TRUE, null, 'Tipos Promoción');
	}

}

/* End of file Tipopromocion.php */
/* Location: ./system/application/controllers/catalogo/Tipopromocion.php */
