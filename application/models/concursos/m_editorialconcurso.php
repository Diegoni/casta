<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	concursos
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Editoriales Concurso
 *
 */
class M_editorialconcurso extends MY_Model
{
	/**
	 * Prefijo de la base de datos
	 * @var string
	 */
	var $prefix = '';

	/**
	 * Constructor
	 * @return M_Pedido
	 */
	function __construct()
	{
		$data_model = array(
			'cEditorial'		=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE),
			'nIdProveedor'		=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'concursos/proveedorconcurso/search')),
		);

		//CI
		$obj =& get_instance();
		$this->prefix = $obj->config->item('bp.concursos.database');
		parent::__construct($this->prefix . 'Diba_Editoriales', 'nIdEditorial', 'cEditorial', 'cEditorial', $data_model);
	}
}

/* End of file M_editorialconcurso.php */
/* Location: ./system/application/models/concursos/M_editorialconcurso.php */