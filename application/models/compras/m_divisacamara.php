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
 * Divisas cámara
 *
 */
class M_divisacamara extends MY_Model
{
	/**
	 * Costructor 
	 * @return M_divisacamara
	 */
	function __construct()
	{
		$data_model = array(
			'cDescripcion'	=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE), 
			'cSimbolo'		=> array(), 
			'fCompra'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT), 
			'fVenta'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT), 
			'nDecimales'	=> array(), 
			'cSimboloIzq'	=> array(), 
			'cSimboloDer'	=> array(), 
			'cSimboloMil'	=> array(), 
			'cSimboloComa'	=> array(), 
		);

		parent::__construct('Gen_DivisasAlmacen', 'nIdDivisa', 'cDescripcion', 'cDescripcion', $data_model, TRUE);
		
		$this->_cache = TRUE;		
	}
}

/* End of file M_divisacamara.php */
/* Location: ./system/application/models/generico/M_divisacamara.php */