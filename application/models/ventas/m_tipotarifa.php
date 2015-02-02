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
 * Tipos de tarifas
 *
 */
class M_Tipotarifa extends MY_Model
{
	/**
	 * Costructor 
	 * @return M_Tipotarifa
	 */
	function __construct()
	{
		$data_model = array(
			'cDescripcion'			=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE), 
			'fMargen'				=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT, DATA_MODEL_REQUIRED => TRUE),
			'bPortes'				=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),
			'bDescuentoProveedor'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN)
		);

		parent::__construct('Cat_TiposTarifa', 'nIdTipoTarifa', 'cDescripcion', 'cDescripcion', $data_model, TRUE);	
		$this->_cache = TRUE;
	}
}

/* End of file M_tipotarifa.php */
/* Location: ./system/application/models/ventas/M_tipotarifa.php */