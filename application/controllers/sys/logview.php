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
 * Visor de LOGS
 *
 */
class LogView extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Log
	 */
	function __construct()
	{
		parent::__construct('sys.log', null, TRUE, 'sys/log.js');
	}

	/**
	 * Devuelve la lista de ficheros de logs en forma de árbol compatible con ExtJS
	 * @return JSON
	 */
	function get_list()
	{
		$this->load->library('Logger');
		$groups = $this->logger->get_groups();
		$nodes = array();
		$nodo = $this->get_logs();
		if (isset($nodo)) $nodes[] = $nodo;
		foreach($groups as $group)
		{
			$nodo = $this->get_logs($group);
			if (isset($nodo)) $nodes[] = $nodo;
		}
		$this->out->send($nodes);
	}

	/**
	 * Devuelve el fichero de log indicado
	 * @param string $id nombre del fichero de log con formato <grupo>/<fichero>
	 * @return JSON
	 */
	function get_log($id = null)
	{
		$id = isset($id)?$id:$this->input->get_post('id');

		if ($id)
		{
			$data = preg_split('/\//', $id);
			$group = $data[0];
			if ($group == $this->lang->line('log-group-general')) $group = null;
			$file = $data[1];
			$this->load->library('Logger');
			$text = '<pre>' . $this->logger->output($file, $group) . '</pre>';
				
			$this->out->success($text);
		}
		$this->out->error();
	}

	/**
	 * Lee el listado de ficheros de logs de un grupo determinado
	 * @param string $group Nomnbre del grupo
	 */
	private function get_logs($group = null)
	{
		$logs = $this->logger->get_list($group);
		if (count($logs) > 0)
		{
			$name = isset($group)?$group:$this->lang->line('log-group-general');
			$n['text'] = $name;
			$n['iconCls'] = 'icon-log-group';
			$n['qtip'] = $name;
			$n['leaf'] = false;
			foreach ($logs as $log)
			{
				$c = array();
				$c['text'] = $log[1];
				$c['iconCls'] = 'icon-log-file';
				$c['id'] = $name . '/' . $log[1];
				$c['qtip'] = $log[1];
				$c['leaf'] = true;
				$n['children'][] = $c;
			}

			return $n;
		}
		return null;
	}
}

/* End of file log.php */
/* Location: ./system/application/controllers/sys/log.php */