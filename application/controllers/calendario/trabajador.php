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
 * Trabajadores
 *
 */
class Trabajador extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Trabajador
	 */
	function __construct()
	{
		parent::__construct('calendario.trabajador', 'calendario/M_Trabajador', true, 'calendario/trabajador.js', 'Trabajadores');
	}

	/**
	 * Consulta el calendario del usuario
	 * @return FORM
	 */
	function consultar($open_id = null)
	{
		// Usuario - Trabajador
		$open_id		= isset($open_id)?$open_id:$this->input->get_post('open_id');
		if (!is_numeric($open_id))
		{
			$username = $this->userauth->get_username();
			$data = $this->reg->get(0, 1, null, null, array('cUsername' => $username));
			if (count($data) > 0)
			{
				$open_id = $data[0]['nIdTrabajador'];
			}
			else
			{
				$this->out->error($this->obj->lang->line('trabajador-no-user-defined'));
			}
		}
		if (is_numeric($open_id)) $this->_show_js('index', 'calendario/trabajadoruno.js', array('open_id' => $open_id));
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Crea un calendario de un trabajador
	 * @param int $year Año del calendario
	 * @param int $id Id del trabajador
	 * @param date $desde Fecha desde donde aplicar el calendario
	 * @return JSON
	 */
	function crear_calendario($id = null, $desde = null, $hasta = null)
	{
		$this->userauth->roleCheck(($this->auth . '.create'));

		$id		= isset($id)?$id:$this->input->get_post('id');
		$desde	= isset($desde)?$desde:$this->input->get_post('desde');
		$hasta	= isset($hasta)?$hasta:$this->input->get_post('hasta');
		#var_dump($desde, $hasta);
		$desde 	= to_date($desde);
		$hasta 	= to_date($hasta);

		$dias[0] = isset($d1)?$d1:urldecode($this->input->get_post('d1'));
		$dias[1] = isset($d2)?$d2:urldecode($this->input->get_post('d2'));
		$dias[2] = isset($d3)?$d3:urldecode($this->input->get_post('d3'));
		$dias[3] = isset($d4)?$d4:urldecode($this->input->get_post('d4'));
		$dias[4] = isset($d5)?$d5:urldecode($this->input->get_post('d5'));
		$dias[5] = isset($d6)?$d6:urldecode($this->input->get_post('d6'));
		$dias[6] = isset($d7)?$d7:urldecode($this->input->get_post('d7'));


		$turnos[0] = isset($t1)?$t1:$this->input->get_post('t1');
		$turnos[1] = isset($t2)?$t2:$this->input->get_post('t2');
		$turnos[2] = isset($t3)?$t3:$this->input->get_post('t3');
		$turnos[3] = isset($t4)?$t4:$this->input->get_post('t4');
		$turnos[4] = isset($t5)?$t5:$this->input->get_post('t5');
		$turnos[5] = isset($t6)?$t6:$this->input->get_post('t6');
		$turnos[6] = isset($t7)?$t7:$this->input->get_post('t7');
			
		if ($id && $desde && $hasta)
		{
			#var_dump($desde, format_date($desde), $hasta, format_date($hasta)); die();
			$success = $this->reg->crear_calendario($id, $desde, $hasta, $dias, $turnos);
			if ($success)
			{
				$this->out->success($this->lang->line('trabajador_calendario_creado'));
			}
			else
			{
				$this->out->error($this->lang->line('mensaje_accion_error'));
			}
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Cre las vacaciones de un trabajador
	 * @param int $id Id del trabajador
	 * @param date $desde Fecha de inicio de las vacaciones
	 * @param date $hasta Fecha final de las vacaciones
	 * @return JSON
	 */
	function crear_vacaciones($id = null, $desde = null, $hasta = null)
	{
		// Seguridad
		$this->userauth->roleCheck(($this->auth . '.create'));

		// Parámetros
		$id		= isset($id)?$id:$this->input->get_post('id');
		$desde	= isset($desde)?$desde:$this->input->get_post('desde');
		$hasta	= isset($hasta)?$hasta:$this->input->get_post('hasta');

		$desde 	= to_date($desde);
		$hasta	= to_date($hasta);

		// Datos
		if ($id && $desde && $hasta)
		{
			$success = $this->reg->crear_vacaciones($id, $desde, $hasta);
			// Final
			if ($success > 0)
			{
				$this->out->success(sprintf($this->lang->line('trabajador_vacaciones_creado'), $success));
			}
			else
			{
				$this->out->error($this->lang->line('mensaje_accion_error'));
			}
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Devuelve un resumen de las horas
	 * @param int $id Id del trabajador
	 * @param int $year Año a mostrar resumen
	 * @return JSON
	 */
	function resumen($id = null, $year = null)
	{
		$this->userauth->roleCheck(($this->auth . '.resumen'));

		$year 	= isset($year)?$year:$this->input->get_post('year');
		$id		= isset($id)?$id:$this->input->get_post('id');

		if (is_numeric($id) && is_numeric($year))
		{
			$data = $this->_resumen($id, $year);
			$data['trabajador'] = $this->reg->load($id);
			$message = $this->load->view('calendario/resumen', $data, TRUE);
			#echo $message; return;
			# Respuesta
			$this->out->html_file($message, $this->lang->line('resumen'). " {$year}", 'iconoReportTab');
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Devuelve un resumen de las horas
	 * @param int $id Id del trabajador
	 * @param int $year Año a mostrar resumen
	 * @return array(year, trabajador, extras, cals)
	 */
	function _resumen($id, $year)
	{
		static $y = null;
		if (!isset($y))
		{
			$this->load->model('calendario/m_dia');
			$y = $this->m_dia->years();
			sksort($y, 'id');
		}
		$this->load->model('calendario/m_horaextratrabajador');
		$horas = $this->m_horaextratrabajador->get(null, null, null, null, "nIdTrabajador={$id} AND nYear<={$year}");
		$horas2 = array();
		#var_dump($horas);
		foreach($horas as $h)
		{
			$r = preg_match('/\((.*)\)/', $h['cDescripcion'], $match);
			if (isset($match[1])) $h['nYear2'] = $match[1]; 
			$h['fHoras'] = -$h['fHoras'];
			$horas2[$h['nYear']][] = $h;
		}
		#var_dump($horas2);
		$horas = $this->m_horaextratrabajador->get(null, null, null, null, "nIdTrabajador={$id} AND nYear={$year}");
		$extras =0;
		foreach($horas as $h)
		{
			$extras += $h['fHoras'];
		}			
		#var_dump($horas, $extras);

		$this->load->model('calendario/m_horasanuales');
		$totales = $this->m_horasanuales->get(null, null, null, null, "nIdTrabajador={$id} AND nAnno={$year}");

		$cals[$year] = array(
				'Total' => $this->reg->trabajadas($id, $year),
				'fHoras' => isset($totales[0])?$totales[0]['fHoras']:0,
				'Extras' => $extras,
			);
		$cals[$year]['Diferencia'] = $cals[$year]['fHoras'] - $cals[$year]['Total'] + $extras;
		#var_dump($cals[$year]['Diferencia'], $cals[$year]['fHoras'], $cals[$year]['Total'], $extras);

		$data['year'] = $year;
		#$data['horas'] = $horas2;
		$data['extras'] = $horas;
		$data['cals'] = $cals;
		#echo '<pre>'; print_r($data); echo '</pre>'; 
		return $data;
	}

	/**
	 * Genera los sábados de un trabajador
	 *
	 * @param int $id Id del trabajador
	 * @param int $year Año
	 * @param dete $desde Fecha de inicio
	 *
	 * @return JSON
	 */
	function crear_sabados($id = null, $desde = null, $hasta = null, $horas = null)
	{
		$this->userauth->roleCheck(($this->auth . '.resumen'));

		$id		= isset($id)?$id:$this->input->get_post('id');
		$desde	= isset($desde)?$desde:$this->input->get_post('desde');
		$hasta	= isset($hasta)?$hasta:$this->input->get_post('hasta');
		$horas	= isset($horas)?$horas:$this->input->get_post('horas');

		if ($desde && $hasta && $horas)
		{
			$desde 	= to_date($desde);
			$hasta 	= to_date($hasta);

			if ((isset($id) && $id != ''))
			{
				$this->reg->crear_sabados($id, $desde, $hasta, $horas);
			}
			else
			{
				$data = $this->reg->get(null, null, null, null, 'bActivo = 1');
				foreach($data as $r)
				{
					$this->reg->crear_sabados($r['nIdTrabajador'], $desde, $hasta, $horas);
				}
			}

			$this->out->success($this->lang->line('crear_sabados_ok'));
		}
		$this->_show_js('estado_horas', 'calendario/asignar_sabados.js');
	}

	/**
	 * Elimina el calendario del trabajador
	 * @param int $id Id del trabajador
	 * @param int $year Año a mostrar resumen
	 * @return JSON
	 */
	function eliminar_calendario($id = null, $desde = null, $hasta = null)
	{
		$this->userauth->roleCheck(($this->auth . '.eliminar_calendario'));

		$id		= isset($id)?$id:$this->input->get_post('id');
		$desde	= isset($desde)?$desde:$this->input->get_post('desde');
		$hasta	= isset($hasta)?$hasta:$this->input->get_post('hasta');

		$desde 	= to_date($desde);
		$hasta 	= to_date($hasta);

		if ($id && $desde && $hasta)
		{
			$data = $this->reg->eliminar_calendario($id, $desde, $hasta);

			$this->out->success($this->lang->line('elm-calendario-ok'));
		}
		else
		{
			$this->out->error($this->lang->line('mensaje_faltan_datos'));
		}
	}

	/**
	 * Estado de las horas de todos los trabajadores en un año
	 * @param int $year Año
	 * @param  string $msg     [description]
	 * @param  string $destino Ids separados por comas (,) de los trabajadores destino
	 * @return MSG
	 */
	function liquidar_horas($year = null, $msg = null, $destino = null)
	{
		$this->userauth->roleCheck(($this->auth . '.upd'));

		$year 	= isset($year)?$year:$this->input->get_post('year');
		$msg	= isset($msg)?$msg:$this->input->get_post('msg');
		$destino	= isset($destino)?$destino:$this->input->get_post('destino');
			#var_dump($year, $msg, $destino); die();

		if (is_numeric($year) && !empty($destino))
		{
			$this->load->model('calendario/m_horaextratrabajador');
			$destino = explode(',', $destino);
			$this->db->trans_begin();
			$count = 0;
			foreach ($destino as $id)
			{
				$data = $this->_resumen($id, $year);
				$horas = $data['cals'][$year]['Diferencia'];
				if ($horas != 0)
				{
					// Quita de uno
					$h = array (
						'nIdTrabajador' => $id,
						'nYear'			=> $year,
						'cDescripcion'	=> sprintf($this->lang->line('liquidar-horas-format'), $year, $msg),
						'fHoras'		=> -$horas
					);
					$this->m_horaextratrabajador->insert($h);

					// Añade al otro
					$h['fHoras'] = -$h['fHoras'];
					$h['nYear'] = $h['nYear'] + 1;
					$this->m_horaextratrabajador->insert($h);
					++$count;
				}
			}
			$this->db->trans_commit();
			$this->out->success(sprintf($this->lang->line('liquidar-horas-ok'), $year, $count));		
		}
		$this->_show_js('upd', 'calendario/liquidar_horas.js');
	}
}

/* End of file trabajador.php */
/* Location: ./system/application/controllers/calendario/trabajador.php */