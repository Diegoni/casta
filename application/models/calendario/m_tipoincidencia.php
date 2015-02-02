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
 * Grupos de Trabajador
 *
 */
class M_TipoIncidencia extends MY_Model
{
	/**
	 * Prefijo de la base de datos
	 * @var string
	 */
	var $prefix = '';

	/**
	 * Cosntructor
	 *
	 * @return M_TipoIncidencia
	 */
	function __construct()
	{
		$data_model = array(
			'cDescripcion'	=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_DEFAULT => TRUE)
		);
		//CI
		$obj =& get_instance();
		$this->prefix = $obj->config->item('bp.calendario.database');
		parent::__construct($this->prefix . 'TiposIncidencia', 'nIdTipoIncidencia', 'cDescripcion', 'cDescripcion', $data_model);
		$this->_cache = TRUE;
	}
}

/* End of file M_grupostrabajador.php */
/* Location: ./system/application/models/calendario/M_grupostrabajador.php */