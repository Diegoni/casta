<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	calendario
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Horas anuales trabajador
 *
 */
class Horasanuales extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Horasanuales
	 */
	function __construct()	
	{
		parent::__construct('calendario.horasanuales', 'calendario/M_Horasanuales', true, null, 'Horas Anuales');
	}
}

/* End of file horasanuales.php */
/* Location: ./system/application/controllers/calendario/horasanuales.php */