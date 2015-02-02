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
 * Series
 *
 */
class Serie extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return App
	 */
	function __construct()
	{
		parent::__construct('ventas.serie', 'ventas/m_serie', true, null, 'Series');
	}
}

/* End of file serie.php */
/* Location: ./system/application/controllers/ventas/serie.php */