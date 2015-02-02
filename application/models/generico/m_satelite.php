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
 * Series
 *
 */
class M_Satelite extends MY_Model
{
	/**
	 * Constructor
	 *
	 * @return M_Satelite
	 */
	function __construct()
	{
		$data_model = array(
			'cDescripcion'	=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE),
		);
		
		parent::__construct('Gen_Satelites', 'nIdSatelite', 'cDescripcion', 'cDescripcion', $data_model, true);
		$this->_cache = TRUE;
	}
}

/* End of file M_satelite.php */
/* Location: ./system/application/models/generico/M_satelite.php */