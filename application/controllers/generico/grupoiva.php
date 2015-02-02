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
class Grupoiva extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Grupoiva
	 */
	function __construct()
	{
		parent::__construct('generico.grupoiva', 'generico/M_Grupoiva', true, null, 'Grupos IVA');
	}
	
}
/* End of file grupoiva.php */
/* Location: ./system/application/controllers/generico/grupoiva.php */