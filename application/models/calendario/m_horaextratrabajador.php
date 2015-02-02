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
 * Horas extra trabajador
 *
 */
class M_Horaextratrabajador extends MY_Model
{
	/**
	 * Prefijo de la base de datos
	 * @var string
	 */
	var $prefix = '';

	/**
	 * Cosntructor
	 *
	 * @return M_Horaextratrabajador
	 */
	function __construct()
	{
		$data_model = array(
			'nIdTrabajador'		=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'calendario/trabajador/search')),		
			'nYear'				=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_DEFAULT => TRUE),		
			'cDescripcion'		=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_DEFAULT => TRUE),
			'fHoras'			=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_DOUBLE)
		);
		//CI
		$obj =& get_instance();
		$this->prefix = $obj->config->item('bp.calendario.database');

		parent::__construct($this->prefix . 'HorasExtraTrabajador', 'nIdExtra', 'nIdTrabajador', 'cDescripcion', $data_model);
		$this->_cache = TRUE;
	}
}

/* End of file M_horaextratrabajador.php */
/* Location: ./system/application/models/calendario/M_horaextratrabajador.php */