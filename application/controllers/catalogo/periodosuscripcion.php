<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	catalogo
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Periodos de suscripción de una revista
 *
 */
class Periodosuscripcion extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Periodosuscripcion
	 */
	function __construct()
	{
		parent::__construct('catalogo.periodosuscripcion', 'catalogo/M_periodosuscripcion', TRUE, null, 'Periodos de suscripción revista');
	}
}

/* End of file Periodosuscripcion.php */
/* Location: ./system/application/controllers/catalogo/Periodosuscripcion.php */
