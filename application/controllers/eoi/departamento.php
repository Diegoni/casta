<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	pedidos cliente
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @filesource
 */

/**
 * EOI - Departamentos
 *
 */
class Departamento extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Departamento
	 */
	function __construct()
	{
		parent::__construct('eoi.departamento', 'eoi/M_Departamento', true, null, /*'eoi/departamento.js'*/ 'Departamentos');
	}
}

/* End of file departamento.php */
/* Location: ./system/application/controllers/eoi/departamento.php */