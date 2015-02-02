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
 * Turnos de los trabajadores
 *
 */
class GruposTrabajador extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return App
	 */
	function __construct()
	{
		parent::__construct('calendario.grupostrabajador', 'calendario/M_Grupostrabajador', true, null, 'Grupos Trabajador');
	}
}

/* End of file grupostrabajador.php */
/* Location: ./system/application/controllers/calendario/grupostrabajador.php */