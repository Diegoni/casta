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
 * Tipos de envío 
 *
 */
class TipoEnvio extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return TipoEnvio
	 */
	function __construct()
	{
		parent::__construct('suscripciones.tipoenvio', 'suscripciones/M_tipoenvio', TRUE, null, 'Tipos de envío');
	}
}

/* End of file TipoEnvio.php */
/* Location: ./system/application/controllers/suscripciones/TipoEnvio.php */