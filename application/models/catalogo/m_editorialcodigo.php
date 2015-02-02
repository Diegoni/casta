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
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Códigos de una editorial
 *
 */
class M_editorialcodigo extends MY_Model
{
	/**
	 * Constructor
	 * @return M_editorialcodigo
	 */
	function __construct()
	{
		$obj = get_instance();
		$data_model = array(
			'nIdEditorial'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'catalogo/editorial/search')),
			'cCodigo'		=> array(DATA_MODEL_REQUIRED => TRUE),		
		);

		parent::__construct('Cat_Codigos', 'nIdCodigo', 'cCodigo', 'cCodigo', $data_model, TRUE);
	}		
}

/* End of file M_editorialcodigo.php */
/* Location: ./system/application/models/catalogo/M_editorialcodigo.php */
