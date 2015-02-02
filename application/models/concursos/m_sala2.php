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
 * Salas  Concurso
 *
 */
class M_sala2 extends MY_Model
{
	/**
	 * Prefijo de la base de datos
	 * @var string
	 */
	var $prefix = '';
	
	/**
	 * Costructor 
	 * @return M_sala2
	 */
	function __construct()
	{
		$data_model = array(
			'cSala'		=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE), 
		);
		
		$obj =& get_instance();
		$this->prefix = $obj->config->item('bp.concursos.database');
		
		parent::__construct($this->prefix . 'Diba_Salas', 'nIdSala', 'cSala', 'cSala', $data_model);	
	}
}

/* End of file M_sala2.php */
/* Location: ./system/application/models/concursos/M_sala2.php */