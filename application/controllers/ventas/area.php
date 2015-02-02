<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	ventas
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Áreas de negocio
 *
 */
class Area extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Area
	 */
	function __construct()
	{
		parent::__construct('ventas.area', 'ventas/m_area', true, null, 'Áreas');
	}
}

/* End of file area.php */
/* Location: ./system/application/controllers/ventas/area.php */