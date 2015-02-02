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
 * Horas extra trabajador
 *
 */
class HoraExtraTrabajador extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return App
	 */
	function __construct()	
	{
		parent::__construct('calendario.horaextratrabajador', 'calendario/M_Horaextratrabajador', true, null, 'Horas Extra Trabajador');
	}
}

/* End of file horaextratrabajador.php */
/* Location: ./system/application/controllers/calendario/horaextratrabajador.php */