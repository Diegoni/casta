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
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Tratamientos de cliente
 *
 */
class M_Tratamiento extends MY_Model
{
	/**
	 * Costructor 
	 * @return M_Tratamiento
	 */
	function __construct()
	{
		$data_model = array(
			'cDescripcion'		=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE), 
		);

		parent::__construct('Gen_Tratamientos', 'nIdTratamiento', 'cDescripcion', 'cDescripcion', $data_model);	
		$this->_cache = TRUE;
	}
}

/* End of file M_Tratamiento.php */
/* Location: ./system/application/models/clientes/M_Tratamiento.php */