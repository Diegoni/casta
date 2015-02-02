<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	calendario
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Turnos de Trabajador
 *
 */
class M_Turno extends MY_Model
{
	/**
	 * Prefijo de la base de datos
	 * @var string
	 */
	var $prefix = '';

	/**
	 * Cosntructor
	 *
	 * @return M_Turno
	 */
	function __construct()
	{
		$data_model = array(
			'cDescripcion'	=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_DEFAULT => TRUE),
			'cSabados'		=> array(DATA_MODEL_REQUIRED => TRUE)		
		);
		//CI
		$obj =& get_instance();
		$this->prefix = $obj->config->item('bp.calendario.database');
		parent::__construct($this->prefix . 'Turnos', 'nIdTurno', 'cDescripcion', 'cDescripcion', $data_model);
		$this->_cache = TRUE;
	}
}

/* End of file M_turno.php */
/* Location: ./system/application/models/calendario/M_turno.php */