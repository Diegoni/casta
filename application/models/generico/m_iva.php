<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	generico
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Iva
 *
 */
class M_iva extends MY_Model
{
	/**
	 * Constructor
	 *
	 * @return M_iva
	 */
	function __construct()
	{
		$data_model = array(
			'fValor'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT, DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE),
			'fRecargo'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT, DATA_MODEL_REQUIRED => TRUE),
			'nIdCuenta' 	=> array(DATA_MODEL_REQUIRED => TRUE),
			'nIdCuentaREC' 	=> array(DATA_MODEL_REQUIRED => TRUE),
		);
		
		parent::__construct('Gen_IVAS', 'nIdIVA', 'nIdIVA', 'nIdIVA', $data_model);
		
		$this->_cache = TRUE;		
	}
}

/* End of file M_iva.php */
/* Location: ./system/application/models/generico/M_iva.php */