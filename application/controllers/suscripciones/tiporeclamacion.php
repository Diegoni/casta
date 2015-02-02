<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	suscripciones
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Medios de renovación 
 *
 */
class Tiporeclamacion extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Tiporeclamacion
	 */
	function __construct()
	{
		parent::__construct('suscripciones.tiporeclamacion', 'suscripciones/m_tiporeclamacion', TRUE, 'suscripciones/tiposreclamacion.js', 'Tipos de reclamación');
	}
}

/* End of file Tiporeclamacion.php */
/* Location: ./system/application/controllers/suscripciones/Tiporeclamacion.php */