<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	clientes
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @filesource
 */

/**
 * Descuentos cliente
 *
 */
class M_descuento extends MY_Model
{
	/**
	 * Costructor 
	 * @return M_descuento
	 */
	function __construct()
	{
		$data_model = array(
			'cDescripcion'	=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE), 
			'fValor'		=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT), 
			'nIdCliente'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT), 
		);

		parent::__construct('Cli_Descuentos', 'nIdDescuento', 'cDescripcion', 'cDescripcion', $data_model, TRUE);	
		#$this->_cache = TRUE;
	}
}

/* End of file M_descuento.php */
/* Location: ./system/application/models/clientes/M_descuento.php */