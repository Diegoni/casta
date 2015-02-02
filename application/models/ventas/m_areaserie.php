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
 * Áreas-Series
 *
 */
class M_areaserie extends MY_Model
{
	/**
	 * Constructor
	 *
	 * @return M_areaserie
	 */
	function __construct()
	{
		$data_model = array(
			'nIdArea'	=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'ventas/area/search')),
			'nIdSerie'	=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'ventas/serie/search')),
		);
		
		parent::__construct('Gen_AreasSerie', 'nIdAreaSerie', 'nIdAreaSerie', 'nIdAreaSerie', $data_model);
		$this->_cache = TRUE;
	}
}

/* End of file M_areaserie.php */
/* Location: ./system/application/models/ventas/M_areaserie.php */