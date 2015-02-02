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
 * Paises
 *
 */
class M_pais extends MY_Model
{
	/**
	 * Costructor 
	 * @return M_tema
	 */
	function __construct()
	{
		$data_model = array(
			'cNombre'		=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE), 
			'cCodISO'		=> array(), 
			'bImpuestos' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),
			'nIdGrupoIva' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'generico/grupoiva/search')),
			'nIdZona'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'perfiles/zona/search')), 
		);

		parent::__construct('Gen_Paises', 'nIdPais', 'cNombre', 'cNombre', $data_model);	
		$this->_cache = TRUE;
	}
}

/* End of file M_pais.php */
/* Location: ./system/application/models/perfiles/M_pais.php */