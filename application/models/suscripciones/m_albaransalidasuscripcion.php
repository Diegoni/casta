<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	suscripciones
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Albaranes de salida de una suscripción 
 *
 */
class M_albaransalidasuscripcion extends MY_Model
{
	/**
	 * Costructor 
	 * @return M_albaransalidasuscripcion
	 */
	function __construct()
	{
		$data_model = array(
			'nIdSuscripcion'	=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
			'nIdAlbaran'		=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
		);

		parent::__construct('Sus_SuscripcionesAlbaranes', 'nIdSuscripcionAlbaran', 'nIdSuscripcionAlbaran', 'nIdPedidoSuscripcion', $data_model);	
		$this->_cache = TRUE;
	}
}

/* End of file M_albaransalidasuscripcion.php */
/* Location: ./system/application/models/suscripciones/M_albaransalidasuscripcion.php */