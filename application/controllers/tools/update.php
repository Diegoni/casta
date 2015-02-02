<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	tools
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Procesos relacionados con la migracion
 * @author alexl
 *
 */
class Update extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Update
	 */
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Crea una devolución con el contenido de otra rechazado
	 * @param int $id Id de la devolución
	 * @return JSON
	 */
	function reasignar()
	{
        set_time_limit(0);
		$this->load->model('compras/m_devolucion');
		$count = $this->m_devolucion->reasignar();
		echo '<pre>';		
		echo "Se han asignado {$count} albaranes\n";
		echo '</pre>';
	}
}

/* End of file Update.php*/
/* Location: ./system/application/controllers/tools/Update.php */
