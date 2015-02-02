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
 * Tipos de autor
 *
 */
class M_tipoautor extends MY_Model
{
	/**
	 * Costructor 
	 * @return M_tipoautor
	 */
	function __construct()
	{
		$data_model = array(
			'cDescripcion'	=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE), 
			'bProtegido'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),
		);

		parent::__construct('Cat_TiposAutor', 'nIdTipoAutor', 'cDescripcion', 'cDescripcion', $data_model);	
		$this->_cache = TRUE;
	}
}

/* End of file M_tipoautor.php */
/* Location: ./system/application/models/catalogo/M_tipoautor.php */