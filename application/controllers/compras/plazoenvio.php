<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	compras
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Plazos de envío
 *
 */
class Plazoenvio extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Plazoenvio
	 */
	function __construct()
	{
		parent::__construct('compras.plazoenvio', 'compras/M_plazoenvio', TRUE, null, 'Plazos de Envío');
	}
}

/* End of file Plazoenvio.php */
/* Location: ./system/application/controllers/compras/Plazoenvio.php */