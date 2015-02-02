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
 * Modos de envío
 *
 */
class ModoEnvio extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return App
	 */
	function __construct()	
	{
		parent::__construct('ventas.modoenvio', 'ventas/m_modoenvio', true, null, 'Modos de Envío');
	}
}

/* End of file modocobro.php */
/* Location: ./system/application/controllers/ventas/modocobro.php */