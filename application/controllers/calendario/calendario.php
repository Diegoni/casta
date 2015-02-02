<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	calendario
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Calendario
 *
 */
class Calendario extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Calendario
	 */
	function __construct()
	{
		parent::__construct(
			'calendario.calendario', 
			'calendario/M_Calendario', 
			true, 
			null, 
			'Calendario');
	}

	/**
	 * Muestra los datos de los trabajadores de un día
	 * @return HTML
	 */
	function personal_dia($fecha1 = null, $fecha2 = null)
	{
		$this->userauth->roleCheck(($this->auth.'.personal_dia'));

		$fecha1	= isset($fecha1)?$fecha1:$this->input->get_post('fecha1', null);
		$fecha2	= isset($fecha2)?$fecha2:$this->input->get_post('fecha2', null);

		if ($fecha1 && $fecha2)
		{
			$fecha1 = to_date($fecha1);
			$fecha2 = to_date($fecha2);

			set_time_limit(0);

			$data['fecha1'] = format_date($fecha1);
			$data['fecha2'] = format_date($fecha2);
			$dias = $this->reg->personal_dia($fecha1, $fecha2);
			//Procesa los datos
			$valores = array();
			$grupos = array();
			foreach($dias as $d)
			{
				if ($d['fHoras'] >= $this->config->item('calendario.jornada'))
				{
					$valores[$d['dDia2']][$d['cGrupo']]['manana'][] = $d;
					$valores[$d['dDia2']][$d['cGrupo']]['tarde'][] = $d;
				}
				else
				{
					if ($d['bTarde'])
					{
						$valores[$d['dDia2']][$d['cGrupo']]['tarde'][] = $d;
					}
					else
					{
						$valores[$d['dDia2']][$d['cGrupo']]['manana'][] = $d;
					}
					if (!isset($valores[$d['dDia2']][$d['cGrupo']]['manana']))
					{
						$valores[$d['dDia2']][$d['cGrupo']]['manana'][] = array();
					}
					if (!isset($valores[$d['dDia2']][$d['cGrupo']]['tarde']))
					{
						$valores[$d['dDia2']][$d['cGrupo']]['tarde'][] = array();
					}
				}
				$valores[$d['dDia2']][$d['cGrupo']][] = $d;

				$grupos[$d['cGrupo']] = $d['cGrupo'];
			}
			$data['valores'] = $valores;
			$data['grupos'] = $grupos;
			$this->load->helper('asset');
			$body = $this->load->view('calendario/personaldia', $data, true);

			$this->out->html_file($body, $this->lang->line('Personal-Dia'), 'iconoCalendarioTurnosTab', 'informes.css');
		}
		else
		{
			$this->_show_js('personal_dia', 'calendario/verdia.js');
		}
	}

	/**
	 * Calendario en un periodo
	 * @return EXTJS
	 */
	function calendario_index()
	{
		$this->_show_form('calendario_trabajador', 'calendario/calendario.js', $this->lang->line('Calendario'));
	}

	/**
	 * Muestra los datos de los trabajadores de un día
	 * @return HTML
	 */
	function personal_dia2($fecha1 = null, $fecha2 = null, $grupo = null, $sort = null, $dir = null)
	{
		$this->userauth->roleCheck(($this->auth.'.personal_dia'));

		$fecha1	= isset($fecha1)?$fecha1:$this->input->get_post('fecha1', null);
		$fecha2	= isset($fecha2)?$fecha2:$this->input->get_post('fecha2', null);
		$sort	= isset($sort)?$sort:$this->input->get_post('sort', null);
		$dir	= isset($dir)?$dir:$this->input->get_post('dir', null);
		$grupo	= isset($grupo)?$grupo:$this->input->get_post('grupo', null);

		$fecha1 = to_date($fecha1);
		$fecha2 = to_date($fecha2);

		set_time_limit(0);

		$data['fecha1'] = format_date($fecha1);
		$data['fecha2'] = format_date($fecha2);

		$dias = $this->reg->calendario(null, $fecha1, $fecha2, $grupo, $sort, $dir);
		foreach ($dias as $k => $d)
		{
			if (!empty($d['nIdVacaciones']))
			{
				$d['cDescripcion'] = $this->lang->line('Vacaciones') . $d['cDescripcion'];
				$d['fHoras'] = 0;
				$dias[$k] = $d;
			}
			elseif (!empty($d['nIdFestivo']))
			{
				#$d['fHoras'] = 0;
				$dias[$k] = $d;
			}
		}

		$this->out->data($dias);
	}

	/**
	 * Calendario de un trabajador
	 * @param int $id
	 * @param date $fecha1
	 * @param date $fecha2
	 * @return JSON
	 */
	function calendario_trabajador($id = null, $fecha1 = null, $fecha2 = null)
	{
		$this->userauth->roleCheck(($this->auth.'.calendario_trabajador'));

		$fecha1	= isset($fecha1)?$fecha1:$this->input->get_post('fecha1', null);
		$fecha2	= isset($fecha2)?$fecha2:$this->input->get_post('fecha2', null);
		$id	= isset($id)?$id:$this->input->get_post('id', null);

		#echo '<pre>1#'  .$fecha1 . "\n" . $fecha2 . '</pre>';

		$fecha1 = (!isset($fecha1) || ($fecha1==''))?time():to_date($fecha1);
		$fecha2 = (!isset($fecha2) || ($fecha2==''))?mktime(0,0,0,12,31,date('Y', $fecha1)):to_date($fecha2);

		#echo '<pre>2#' .$fecha1 . "\n" . $fecha2 . '</pre>';

		if ($id && $fecha1 && $fecha2)
		{
			set_time_limit(0);

			$data = $this->reg->calendario($id, $fecha1, $fecha2);
			foreach ($data as $k => $d)
			{
				if (!empty($d['nIdVacaciones']))
				{
					$d['cDescripcion'] = $this->lang->line('Vacaciones') . ' ' . $d['cDescripcion'];
					$d['fHoras'] = 0;
					$data[$k] = $d;
				}
				/*elseif (!empty($d['nIdFestivo']))
				{
					$d['fHoras'] = 0;
					$data[$k] = $d;
				}*/
			}

			$this->out->data($data);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Controller#upd()
	 */
	function upd($id =null, $horas = null, $comentario = null, $tarde = null)
	{
		$id = isset($id)?$id:$this->input->get_post('id');
		parent::_add($id);
	}

	/**
	 * Estado de las horas de todos los trabajadores en un año
	 * @param int $year Año
	 * @return JSON
	 */
	function estado_horas($year = null)
	{
		$this->userauth->roleCheck(($this->auth . '.estado_horas'));

		$year 	= isset($year)?$year:$this->input->get_post('year');

		if ($year)
		{
			$data = $this->reg->estado_horas($year);
			$this->load->model('calendario/m_trabajador');
			$this->load->model('calendario/m_horaextratrabajador');
			$tr = $this->m_trabajador->get(null, null, 'cNombre', null, 'bActivo=1');
			foreach ($tr as $key => $value) 
			{
				$tr[$key]['Total'] = $this->m_trabajador->trabajadas($value['nIdTrabajador'], $year);
				$horas = $this->m_horaextratrabajador->get(null, null, null, null, "nIdTrabajador={$value['nIdTrabajador']} AND nYear={$year}");
				$extras = 0;
				foreach($horas as $h)
				{
					$extras += $h['fHoras'];
				}

				$this->load->model('calendario/m_horasanuales');
				$totales = $this->m_horasanuales->get(null, null, null, null, "nIdTrabajador={$value['nIdTrabajador']} AND nAnno={$year}");
				$tr[$key]['fHoras'] = isset($totales[0])?$totales[0]['fHoras']:0;
				$tr[$key]['Extras'] = $extras;
				$tr[$key]['nAnno'] = $year;
				$tr[$key]['Diferencia'] = $tr[$key]['fHoras'] - $tr[$key]['Total'] + $extras;
				$tr[$key]['AFavor'] = ($tr[$key]['Diferencia'] < 0)? -$tr[$key]['Diferencia']:0;
				$tr[$key]['EnContra'] = ($tr[$key]['Diferencia'] > 0)? $tr[$key]['Diferencia']:0;
			}
			#var_dump($tr); die();
			$this->load->helper('asset');
			$message = $this->load->view('calendario/estado_horas', array('horas' => $tr, 'year' => $year), TRUE);
			#echo $message; die();
			// Respuesta
			$this->out->html_file($message, $this->lang->line('estado-horas') . ' ' . $year, 'iconoReportTab');
		}
		$this->_show_js('estado_horas', 'calendario/estado_horas.js');
	}

	/**
	 * Rsumen del Calendario de un trabajador
	 * @param int $id Id del trabajador
	 * @param date $desde Fecha inicial
	 * @param date $year Año a mostrar
	 * @return JSON
	 */
	function resumen($id = null, $desde = null, $year = null)
	{
		$this->userauth->roleCheck(($this->auth.'.calendario_trabajador'));

		$desde	= isset($desde)?$desde:$this->input->get_post('desde');
		$year	= isset($year)?$year:$this->input->get_post('year');
		$id	= isset($id)?$id:$this->input->get_post('id');

		if ($id && $desde && $year)
		{
			set_time_limit(0);
				
			$fecha1 = to_date($desde);
			$fecha2 = mktime(0, 0, 0, 12, 31, $year);
			$data = $this->reg->calendario($id, $fecha1, $fecha2);
			foreach ($data as $k => $d)
			{
				if (!empty($d['nIdVacaciones']))
				{
					$d['cDescripcion'] = $this->lang->line('Vacaciones') . ' ' . $d['cDescripcion'];
					$d['fHoras'] = 0;
					$data[$k] = $d;
				}
				elseif (!empty($d['nIdFestivo']))
				{
					$d['fHoras'] = 0;
					$data[$k] = $d;
				}
			}
			$this->load->model('calendario/m_trabajador');
			$tr = $this->m_trabajador->load($id);
			
			$data['calendario'] = $data;
			$data['trabajador'] = $tr;
			$data['fecha1'] = format_date($fecha1);
			$data['fecha2'] = format_date($fecha2);
			$message = $this->load->view('calendario/calendario', $data, TRUE);
			
			// Respuesta
			$this->out->html_file($message, $this->lang->line('Calendario'). " {$data['fecha1']} - {$data['fecha2']}", 'iconoReportTab');				
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Copia el calendario de un trabajador al otro. Incluye vacaciones
	 * @param  int $origen  Id trabajador origen
	 * @param  string $destino Ids separados por comas (,) de los trabajadores destino
	 * @param  int $year    Año a copiar
	 * @return MSG
	 */
	function copiar($origen = null, $destino = null, $year = null)
	{
		$this->userauth->roleCheck(($this->auth.'.calendario_trabajador'));
		$origen	= isset($origen)?$origen:$this->input->get_post('origen');
		$year	= isset($year)?$year:$this->input->get_post('year');
		$destino	= isset($destino)?$destino:$this->input->get_post('destino');
		if (!empty($destino) && is_numeric($origen) && is_numeric($year))
		{
			$destino = explode(',', $destino);
			$this->db->trans_begin();
			$count = 0;
			foreach ($destino as $id)
			{
				if (is_numeric($id) && $id != $origen)
				{
					if (!$this->reg->copiar($origen, $id, $year))
					{
						$this->db->trans_rollback();
						$this->out->error($this->reg->error_message());						
					}
					++$count;
				}
			}
			$this->db->trans_commit();
			$this->out->success(sprintf($this->lang->line('copiar-calendario-ok'), $count));
		}
		$this->_show_js('calendario_trabajador', 'calendario/copiarcalendario.js');
	}
}

/* End of file calendario.php */
/* Location: ./system/application/controllers/calendario.php */