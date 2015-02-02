<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	app
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Tarea pendiente de ejecutar
 * @var int
 */
define('TASK_STATE_NORMAL',		1);
/**
 * Tarea bloqueda
 * @var int
 */
define('TASK_STATE_BLOQUED', 	2);
/**
 * Tarea en ejecución
 * @var int
 */
define('TASK_STATE_RUNNING', 	3);
/**
 * Tarea cancelada
 * @var int
 */
define('TASK_STATE_CANCEL', 	4);
/**
 * Tarea finalizada
 * @var int
 */
define('TASK_STATE_FINISH', 	5);

/**
 * Tareas
 *
 */
class M_Tarea extends MY_Model
{
	/**
	 * Constructoir
	 * @return unknown_type
	 */
	function __construct()
	{
		$data_model = array(
			'cDescripcion'		=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE),
			'cComando'			=> array(DATA_MODEL_REQUIRED => TRUE),
			'nIdEstado'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_DEFAULT_VALUE => TASK_STATE_NORMAL),
			'dInicio' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATETIME),
			'cResultado'	 	=> array(),
		);

		parent::__construct('Ext_Tareas', 'nIdTarea', 'dCreacion DESC', 'cDescripcion', $data_model, TRUE);
		#$this->_cache = TRUE;
	}

	/**
	 * Devuelve la primera tarea que se debe ejecutar
	 * @return array
	 */
	function get_first()
	{
		$time = format_mssql_datetime(time());
		
		$data = $this->get(0, 1, 'dCreacion', 'DESC', "nIdEstado = 1 AND (dInicio <= {$time} OR dInicio IS NULL)");
		if (isset($data[0]))
		{
			return $data[0];
		}
		return null;		
	}	
}

/* End of file M_tarea.php */
/* Location: ./system/application/models/sys/M_tarea.php */
