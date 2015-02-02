<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	perfiles
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @filesource
 */

/**
 * Zonas
 *
 */
class M_zona extends MY_Model
{
	/**
	 * Costructor 
	 * @return M_tema
	 */
	function __construct()
	{
		$data_model = array(
			'cNombre'		=> array(DATA_MODEL_REQUIRED => TRUE), 
			'cDescripcion'	=> array(DATA_MODEL_DEFAULT => TRUE), 
		);

		parent::__construct('Web_Zonas', 'nIdZona', 'cNombre', 'cNombre', $data_model);	
		$this->_cache = TRUE;
	}
}

/* End of file M_zona.php */
/* Location: ./system/application/models/perfiles/M_zona.php */