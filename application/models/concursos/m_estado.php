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
 * Estados  Concurso
 *
 */
class M_estado extends MY_Model
{
	/**
	 * Prefijo de la base de datos
	 * @var string
	 */
	var $prefix = '';
	
	/**
	 * Costructor 
	 * @return M_estado
	 */
	function __construct()
	{
		$data_model = array(
			'cEstado'		=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE),
			'nIdGrupo' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'concursos/grupoestado/search')),
		);
		
		$obj =& get_instance();
		$this->prefix = $obj->config->item('bp.concursos.database');
		
		parent::__construct($this->prefix . 'Diba_Estados', 'nIdEstado', 'cEstado', 'cEstado', $data_model);	
	}
}

/* End of file M_estado.php */
/* Location: ./system/application/models/concursos/M_estado.php */