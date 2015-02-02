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
class M_area extends MY_Model
{
	/**
	 * Constructor
	 *
	 * @return M_area
	 */
	function __construct()
	{
		$data_model = array(
			'cNombre'	=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE),
		);
		
		parent::__construct('Gen_Areas', 'nIdArea', 'cNombre', 'cNombre', $data_model);
		$this->_cache = TRUE;
	}
}

/* End of file M_area.php */
/* Location: ./system/application/models/ventas/M_area.php */