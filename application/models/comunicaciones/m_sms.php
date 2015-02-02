<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	comunicaciones
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Mensajes SMS
 *
 */
class M_Sms extends MY_Model
{
	function __construct()
	{
		$data_model = array(
			'cTo'			=> array(DATA_MODEL_REQUIRED => TRUE),
			'cMensaje'		=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE),
			'bEnviado'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),
			'cIdServidor' 	=> array(),
			'cEstado'		=> array(),		
			'dEnviado'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATE),
			'bDone'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN, DATA_MODEL_DEFAULT_VALUE => FALSE),
		);

		parent::__construct('Ext_SMS', 'nIdSMS', 'cTo', 'dCreacion', $data_model, TRUE);
		//$this->_cache = TRUE;
	}	
}

/* End of file M_sms.php */
/* Location: ./system/application/models/comunicaciones/M_sms.php */
