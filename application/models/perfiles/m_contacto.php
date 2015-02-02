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
 * Contactos
 *
 */
class M_contacto extends MY_Model
{
	/**
	 * Costructor 
	 * @return M_tema
	 */
	function __construct()
	{
		$data_model = array(
			'cDescripcion'	=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE), 
			'cNombre'		=> array(DATA_MODEL_REQUIRED => TRUE), 
			'cApellido'		=> array(), 
			'nIdTipo'		=> array(DATA_MODEL_DEFAULT_VALUE => 1, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'perfiles/tipoperfil/search')), 
		);

		parent::__construct('Gen_Contactos', 'nIdContacto', 'cDescripcion', 'cDescripcion', $data_model);	
			$this->_cache = TRUE;
	}
}

/* End of file M_contacto.php */
/* Location: ./system/application/models/perfiles/M_contacto.php */