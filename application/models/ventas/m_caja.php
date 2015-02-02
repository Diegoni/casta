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
 * Cajas
 *
 */
class M_Caja extends MY_Model
{
	/**
	 * Constructor
	 *
	 * @return M_Caja
	 */
	function __construct()
	{
		$data_model = array(
			'cDescripcion'	=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE),
			'cCorto'		=> array(),
			'cResponsable'	=> array(),
			'nIdSerie'		=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'ventas/serie/search')),
		);
		
		parent::__construct('Gen_Cajas', 'nIdCaja', 'cDescripcion', 'cDescripcion', $data_model, true);
		$this->_cache = TRUE;
	}
}

/* End of file M_caja.php */
/* Location: ./system/application/models/ventas/M_caja.php */