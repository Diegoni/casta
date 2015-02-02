<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	catalogo
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Portadas de los artículos
 *
 */
class M_portada extends MY_Model
{
	/**
	 * Costructor 
	 * @return M_portada
	 */
	function __construct()
	{
		$data_model = array(
			'nIdTabla'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT), 
			'nIdRegistro'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE), 
			'cDescripcion'	=> array(DATA_MODEL_DEFAULT => TRUE), 
			'nFotoSize'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE), 
			'cExtension'	=> array(), 
		);

		parent::__construct('Fotos', 'nIdFoto', 'nIdFoto', 'nIdFoto', $data_model, TRUE);	
		$this->_cache = TRUE;
	}
}

/* End of file M_portada.php */
/* Location: ./system/application/models/catalogo/M_portada.php */