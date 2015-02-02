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
class Tiposuscripcionrevista extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Tiposuscripcionrevista
	 */
	function __construct()
	{
		parent::__construct('catalogo.tiposuscripcionrevista', 'catalogo/M_tiposuscripcionrevista', TRUE, null, 'Tipos de suscripción revista');
	}
}

/* End of file Tiposuscripcionrevista.php */
/* Location: ./system/application/controllers/catalogo/Tiposuscripcionrevista.php */
