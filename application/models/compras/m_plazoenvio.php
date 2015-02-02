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
 * Plazos de envío
 *
 */
class M_plazoenvio extends MY_Model
{
	/**
	 * Costructor 
	 * @return M_plazoenvio
	 */
	function __construct()
	{
		$data_model = array(
			'cDescripcion'	=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE), 
			'nDiasMinimos'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
			'nDiasMaximos'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
		);

		parent::__construct('Cat_PlazoEnvio', 'nIdPlazoEnvio', 'cDescripcion', 'cDescripcion', $data_model);	
		$this->_cache = TRUE;
	}
}

/* End of file M_plazoenvio.php */
/* Location: ./system/application/models/compras/M_plazoenvio.php */