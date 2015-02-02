<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	generico
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Satelites
 *
 */
class Iva extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Iva
	 */
	function __construct()
	{
		parent::__construct('generico.iva', 'generico/M_iva', true, null, 'IVAS');
	}
	
}
/* End of file grupoiva.php */
/* Location: ./system/application/controllers/generico/grupoiva.php */