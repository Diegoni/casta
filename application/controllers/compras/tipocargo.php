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
 * Tipos de cargos
 *
 */
class TipoCargo extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return TipoCargo
	 */
	function __construct()	
	{
		parent::__construct('compras.tipocargo', 'compras/M_Tipocargo', true, null, 'Tipos de Cargo');
	}
}

/* End of file tipocargo.php */
/* Location: ./system/application/controllers/compras/tipocargo.php */