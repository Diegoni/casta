<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	compras
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Líneas de documento de la cámara del libro
 *
 */
class M_liquidaciondepositoslinea extends MY_Model
{
	/**
	 * Costructor 
	 * @return M_liquidaciondepositoslinea
	 */
	function __construct()
	{
		$data_model = array(
			'nIdDocumento'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'compras/liquidaciondepositos/search')),
			'nIdLineaEntrada'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE), 
			'nIdLineaSalida'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE), 
			'nCantidad' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE), 
			'fPrecio' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT), 
			'fIVA' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT), 
			'fRecargo' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT), 
			'fDescuento' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT), 
		);

		parent::__construct('Doc_LineasLiquidacionDeposito', 'nIdLinea', 'nIdLinea', 'nIdLinea', $data_model, TRUE);
	}
}

/* End of file M_liquidaciondepositoslinea.php */
/* Location: ./system/application/models/compras/M_liquidaciondepositoslinea.php */