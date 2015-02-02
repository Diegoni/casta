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
 * Tipos de mercancía
 *
 */
class Tipomercancia extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Tipomercancia
	 */
	function __construct()	
	{
		parent::__construct('compras.tipomercancia', 'compras/M_tipomercancia', true, null, 'Tipos de mercancía');
	}
}

/* End of file tipomercancia.php */
/* Location: ./system/application/controllers/compras/tipomercancia.php */