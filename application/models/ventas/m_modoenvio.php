<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	ventas
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Modos de Cobro
 *
 */
class M_modoenvio extends MY_Model
{
	/**
	 * Costructor 
	 * @return MY_Model
	 */
	function __construct()
	{
		$data_model = array(
			'cNombre'		=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE),
			'fMinimo'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DOUBLE, DATA_MODEL_DEFAULT_VALUE => 0),
			'nOrden' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT), 
			'bWeb' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN, DATA_MODEL_DEFAULT_VALUE => TRUE),
			'cModosPago'	=> array(), 
		);

		parent::__construct('Web_TiposEnvio', 'nIdTipo', 'cNombre', 'cNombre', $data_model);	
		$this->_cache = TRUE;
	}
}

/* End of file M_modoenvio.php */
/* Location: ./system/application/models/ventas/M_modoenvio.php */