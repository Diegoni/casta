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
 * Series-Áreas de negocio
 *
 */
class Areaserie extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Areaserie
	 */
	function __construct()
	{
		parent::__construct('ventas.areaserie', 'ventas/m_areaserie', true, null, 'Áreas-Series');
	}
}

/* End of file Areaserie.php */
/* Location: ./system/application/controllers/ventas/Areaserie.php */