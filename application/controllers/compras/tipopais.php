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
 * Tipos de IVA por tipo de artículo y país
 *
 */
class Tipopais extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Tipopais
	 */
	function __construct()	
	{
		parent::__construct('compras.tipopais', 'compras/M_tipopais', true, null, 'Tipos de IVA países');
	}
}

/* End of file tipopais.php */
/* Location: ./system/application/controllers/compras/tipopais.php */