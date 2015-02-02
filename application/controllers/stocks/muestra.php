<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	stocks
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Muestras de stock
 *
 */
class Muestra extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Muestra
	 */
	function __construct()
	{
		parent::__construct('stocks.muestra', 'stocks/M_muestra', TRUE);
	}

		/**
	 * Muestra de stocks ordenadas por cantidades
	 * @param int $unidades Número de unidades
	 * @param int $task 0: Directo, 1: Como tareas
	 */
	function _muestra($method, $title, $unidades, $task)
	{
		$this->userauth->roleCheck($this->auth . '.' . $method);
		$unidades = isset($unidades)?$unidades:$this->input->get_post('unidades');
		$task = isset($task)?$task:$this->input->get_post('task');

		if ($task === FALSE) $task = 1;
		if (!is_numeric($unidades)) $unidades = $this->config->item('bp.oltp.unidades_muestra');

		if ($task == 1)
		{
			$this->load->library('tasks');
			$cmd = site_url("stocks/muestra/{$method}/{$unidades}/0");
			$this->tasks->add2($title, $cmd);
		}
		else
		{
			set_time_limit(0);

			$this->load->model('generico/m_seccion');
			$this->load->model('catalogo/m_articulo');
			$secciones = $this->m_seccion->get(null, null, 'cNombre', 'ASC');
			$data = array();
			foreach($secciones as $sec)
			{
				$lineas = $this->reg->$method($sec['nIdSeccion'], $unidades);
				if (count($lineas) > 0)
				{
					foreach ($lineas as $k => $l)
					{
						$d = $this->m_articulo->load($l['nIdLibro'], 'ubicaciones');
						$lineas[$k]['ubicacion'] = $d['ubicaciones'];
						$lineas[$k]['PVP'] = $d['fPVP'];
					}

					$data[] = array('seccion' => $sec, 'lineas' => $lineas);
				}
			}
			$data['titulo'] = $title;
			$data['secciones'] = $data;
			$message = $this->load->view('stocks/listado', $data, TRUE);
			$this->out->html_file($message, $title, 'iconoReportTab');
		}
		return;
	}
	
	/**
	 * Muestra de stocks ordenadas por cantidades
	 * @param int $unidades Número de unidades
	 * @param int $task 0: Directo, 1: Como tareas
	 * @return HTML_FILE
	 */
	function cantidades($unidades = null, $task = null)
	{
		return $this->_muestra('cantidades', $this->lang->line('stock-cantidades'), $unidades, $task);
	}

	/**
	 * Muestra de stocks ordenadas por precios
	 * @param int $unidades Número de unidades
	 * @param int $task 0: Directo, 1: Como tareas
	 * @return HTML_FILE
	 */
	function precio($unidades = null, $task = null)
	{
		return $this->_muestra('precio', $this->lang->line('stock-precio'), $unidades, $task);
	}
}

/* End of file Muestra.php */
/* Location: ./system/application/controllers/stocks/Muestra.php */