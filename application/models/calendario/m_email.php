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
 * Emails de Trabajador
 *
 */
class M_Email extends MY_Model
{
	/**
	 * Prefijo de la base de datos
	 * @var string
	 */
	var $prefix = '';

	/**
	 * Cosntructor
	 *
	 * @return M_Email
	 */
	function __construct()
	{
		$data_model = array(
			'cEmail'		=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE),
			'cLogin'		=> array(DATA_MODEL_REQUIRED => TRUE),	
			'cPassword'		=> array(DATA_MODEL_REQUIRED => TRUE)		
		);

		//CI
		$obj =& get_instance();
		$this->prefix = $obj->config->item('bp.calendario.database');
		parent::__construct($this->prefix . 'Emails', 'nIdEmail', 'cEmail', 'cEmail', $data_model);
		$this->_cache = TRUE;
	}
}

/* End of file M_email.php */
/* Location: ./system/application/models/calendario/M_email.php */