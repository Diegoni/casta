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
 * Destinos de reclamación
 *
 */
class DestinoReclamacion extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return DestinoReclamacion
	 */
	function __construct()
	{
		parent::__construct('suscripciones.destinoreclamacion', 'suscripciones/m_destinoreclamacion', TRUE, null, 'Destinos reclamación');
	}
}

/* End of file DestinoReclamacion.php */
/* Location: ./system/application/controllers/suscripciones/DestinoReclamacion.php */