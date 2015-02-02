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
 * Información del estado de un pedido de proveedor
 *
 */
class Informacionproveedor extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Informacionproveedor
	 */
	function __construct()
	{
		parent::__construct('compras.informacionproveedor', 'compras/M_informacionproveedor', TRUE, null, 'Información proveedor');
	}
}

/* End of file Informacionproveedor.php */
/* Location: ./system/application/controllers/compras/Informacionproveedor.php */