<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	libraries
 * @category	app
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */


/**
 * Gestor de tareas
 * @author alexl
 *
 */
class Tasks {

	/**
	 * Instancia de CI
	 * @var CI
	 */
	var $obj;

	/**
	 * Constructor
	 * @return Task
	 */
	function __construct()
	{
		$this->obj =& get_instance();
		$this->obj->load->model('sys/m_tarea');
		log_message('debug', 'Tasks Class Initialised via '.get_class($this->obj));
	}

	/**
	 * Añade una tarea al sistema
	 * @param string $descripcion Descripción
	 * @param string $cmd Comando
	 * @param date $time Programación de la tarea
	 * @return int Id de la tarea
	 */
	function add($descripcion, $cmd, $time = null)
	{
		$alias = $this->obj->config->item('bp.runner.alias');
		if (isset($alias))
		{
			foreach($alias as $k => $v)
			{
				$cmd = str_replace($k, $v, $cmd);
			}
		}
		$data = array (
			'cDescripcion'	=> $descripcion,
			'cComando'		=> $cmd,
			'dInicio'		=> $time
		);
		return $this->obj->m_tarea->insert($data);
	}

	/**
	 * Devuelve la siguiente tarea lista para ejecutarse
	 */
	function next()
	{
		return $this->obj->m_tarea->get_first();
	}

	/**
	 * Indica que una tarea se ha bloqueado
	 * @param int $id Id de la tarea
	 */
	function blocked($id)
	{
		$data = array (
			'nIdEstado'		=> TASK_STATE_BLOCKED
		);

		return $this->obj->m_tarea->update($id, $data);
	}

	/**
	 * Indica que una tarea se ha cancelado
	 * @param int $id Id de la tarea
	 */
	function cancel($id)
	{
		$data = array (
			'nIdEstado'		=> TASK_STATE_CANCEL
		);

		return $this->obj->m_tarea->update($id, $data);
	}

	/**
	 * Indica que una tarea está en ejecución
	 * @param int $id Id de la tarea
	 */
	function running($id)
	{
		$data = array (
			'nIdEstado'		=> TASK_STATE_RUNNING
		);

		return $this->obj->m_tarea->update($id, $data);
	}

	/**
	 * Se indica que una tarea ya está finalizada
	 * @param int $id Id de la tarea
	 * @param string $result Resultado devuelto por la tarea
	 */
	function finish($id, $result = null)
	{
		$data = array (
			'nIdEstado'		=> TASK_STATE_FINISH,
			'cResultado'	=> $result
		);

		return $this->obj->m_tarea->update($id, $data);
	}

	/**
	 * Añade una tarea al sistema y devuelve el mensaje de OK
	 * @param string $descripcion Descripción
	 * @param string $cmd Comando
	 * @param date $time Programación de la tarea
	 * @param bool $out Envía un mensaje cuando crea la tarea
	 * @return JSON
	 */
	function add2($descripcion, $cmd, $params = null, $time = null, $out = TRUE)
	{
		if (count($params) > 0)
		{
			foreach ($params as $k => $v)
			{
				$params[$k] = urlencode($v);
				if ($params[$k] == '') $params[$k] = 'null';
			}
			#$runner = $this->obj->userauth->get_username();
			#$params[] = $runner;

			$cmd .= '/' . implode('/', $params);
		}
		$id_task = $this->add($descripcion, $cmd, $time);
		
		$message = (isset($time))?sprintf($this->obj->lang->line('mailing-task-cola-time'), format_datetime($time), $id_task):sprintf($this->obj->lang->line('mailing-task-cola'), $id_task);
		if ($out) $this->obj->out->success($message);
		return $message;
	}

	/**
	 * Resetea una terea y la vuelve a ejecutar
	 * @param int $id Id de la tarea
	 */
	function reset($id)
	{
		$data = array (
			'nIdEstado'		=> TASK_STATE_NORMAL,
			'cResultado'	=> null
		);

		return $this->obj->m_tarea->update($id, $data);
	}

	/**
	 * Devuelve la lista de tareas
	 * @param int $limit Número de elementos a mostrar
	 *
	 */
	function get_list($limit = 20)
	{
		return $this->obj->m_tarea->get(0, $limit, 'nIdTarea', 'DESC');
	}
}

/* End of file Tasks.php */
/* Location: ./system/libraries/Tasks.php */