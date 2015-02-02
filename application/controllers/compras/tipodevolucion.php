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
 * Tipos de devolución
 *
 */
class TipoDevolucion extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return TipoDevolucion
	 */
	function __construct()	
	{
		parent::__construct('compras.tipodevolucion', 'compras/M_Tipodevolucion', true, null, 'Tipos de Devolución');
	}
}

/* End of file tipodevolucion.php */
/* Location: ./system/application/controllers/compras/tipodevolucion.php */