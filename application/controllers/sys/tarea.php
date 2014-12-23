<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	app
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Tareas del scheduler
 *
 */
class Tarea extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Tarea
	 */
	function __construct()
	{
		parent::__construct('sys.tarea', 'sys/m_tarea', TRUE, 'sys/tareas.js');
	}

	/**
	 * Muestra un listado de tareas
	 */
	/*function index()
	{
		$this->userauth->roleCheck(($this->auth . '.index'));
		$data['tpv'] = TRUE;
		$this->_show_form('index', 'sys/tareas.js', $this->lang->line('Tareas'), null, null, $open_id, $data);
		
		$this->load->library('Tasks');
		$data = $this->tasks->get_list();
		$message = $this->load->view('sys/tareas', array('tareas' => $data), TRUE);
		$this->out->html_file($message, $this->lang->line('Cola de tareas'), 'iconoTasksTab');		
	}*/	
	
	/**
	 * Ejecuta la tarea indicada
	 * @param int $id Id de la tarea
	 */
	function runtask($id)
	{
		$this->userauth->roleCheck(($this->auth . '.runtask'));
		$id = isset($id)?$id:$this->input->get_post('id');
		if ($id)
		{
			$this->load->library('Tasks');
			$cmd = $this->tasks->reset($id);
			$this->out->success($this->lang->line('tarea_ejecutada'));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));		
	}
}

/* End of file tarea.php */
/* Location: ./system/application/controllers/sys/tares.php */