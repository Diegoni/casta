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
class MedioRenovacion extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return MedioRenovacion
	 */
	function __construct()
	{
		parent::__construct('suscripciones.mediorenovacion', 'suscripciones/m_mediorenovacion', TRUE, null, 'Medios de renovación');
	}
}

/* End of file MedioRenovacion.php */
/* Location: ./system/application/controllers/suscripciones/MedioRenovacion.php */