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
 * Modos de pago
 *
 */
class M_modopago extends MY_Model
{
	/**
	 * Costructor
	 * @return M_modopago
	 */
	function __construct()
	{
		$data_model = array(
			'cDescripcion'		=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE), 
			'cDescripcionCorta'	=> array(), 
			'cAlias'			=> array(), 
			'cCuenta'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
			'bTarjeta' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN, DATA_MODEL_DEFAULT_VALUE => FALSE),
		);

		parent::__construct('Gen_ModosPago', 'nIdModoPago', 'cDescripcion', 'cDescripcion', $data_model, TRUE);
		$this->_cache = TRUE;
	}
}

/* End of file M_modopago.php */
/* Location: ./system/application/models/ventas/M_modopago.php */