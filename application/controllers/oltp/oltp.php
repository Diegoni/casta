<?php
/* Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	pedidos cliente
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @filesource
 */

/**
 * Controlador de las tarifas de envío
 *
 */
class Oltp extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return TarifasEnvio
	 */
	function __construct()
	{
		parent::__construct('oltp', 'oltp/m_oltp', TRUE);
	}

	/**
	 * Ventas por series
	 * @param int $year Año a mostrar
	 */
	function ventas_series($year = null)
	{
		$this->userauth->roleCheck('oltp.ventas_series');
		$year = isset($year)?$year:$this->input->get_post('year', null);

		if ($year)
		{
			$data['year'] = $year;
			$data['valores'] = $this->reg->ventas_series($year);
			$body = $this->load->view('oltp/ventasseries', $data, TRUE);
			$this->out->html_file($body, $this->lang->line('Ventas por series y meses') . '  ' . $year, 'iconoReportTab');
		}
		else
		{
			$this->_show_js('ventas_series', 'oltp/ventasseries.js');
		}
	}

	/**
	 * Ventas en un periodo
	 * @param date $fecha1 Fecha de inicio
	 * @param date $fecha2 Fecha de final
	 */
	function ventas_series_periodo($fecha1 = null, $fecha2 = null)
	{
		$this->userauth->roleCheck('oltp.ventas_series');
		$fecha1		= isset($fecha1)?$fecha1:$this->input->get_post('fecha1');
		$fecha2		= isset($fecha2)?$fecha2:$this->input->get_post('fecha2');

		if ($fecha1 && $fecha2)
		{
			$fecha1 = to_date($fecha1);
			$fecha2 = to_date($fecha2);
			$datos = $this->reg->ventas_series_periodo($fecha1, $fecha2);
			$data['fecha1'] = format_date($fecha1);
			$data['fecha2'] = format_date($fecha2);
			$data['valores'] = $datos;
			$body = $this->load->view('oltp/ventasseriesperiodo', $data, TRUE);
			$this->out->html_file($body, $this->lang->line('Ventas por series en un periodo'), 'iconoReportTab');
		}
		else
		{
			$data['url'] = site_url('oltp/oltp/ventas_series_periodo');
			$data['title'] = $this->lang->line('Ventas por series en un periodo');
			$this->_show_js('ventas_series', 'oltp/ventassiniva.js', $data);
		}
	}

	/**
	 * Acción de ventas de secciones, devuelve un HTML
	 *
	 * @param int $id Sección
	 * @param date $fecha Fecha límite ventas
	 * @param bool $task Se ejecuta como tarea
	 * @return VIEW
	 */
	function ventas_secciones($seccion = null, $fecha = null, $task = null)
	{
		$this->userauth->roleCheck(('oltp.ventas_secciones'));
		$fecha 	= isset($fecha)?$fecha:$this->input->get_post('fecha');
		#$fecha2 	= isset($fecha2)?$fecha2:$this->input->get_post('fecha2');
		$seccion= isset($seccion)?$seccion:$this->input->get_post('seccion');
		$task = isset($task)?$task:$this->input->get_post('task');

		if ($task === FALSE) $task = 1;

		if (!empty($fecha) /*&& !empty($fecha2)*/)
		{
			if ($task == 1)
			{
				$this->load->library('tasks');
				if (!is_numeric($seccion)) $seccion = 'null';
				$fecha = str_replace('/', '-', $fecha);
				#$fecha2 = str_replace('/', '-', $fecha2);
				$cmd = site_url("oltp/oltp/ventas_secciones/{$seccion}/{$fecha}/0");
				$this->tasks->add2($this->lang->line('Ventas Secciones'), $cmd);
			}
			else
			{
				if ($seccion == 'null') $seccion = null;
				set_time_limit(0);
				$this->_ventas_secciones_view($seccion, $fecha/*, $fecha2*/, false);
			}
		}
		else
		{
			$data['url'] = site_url('oltp/oltp/ventas_secciones');
			$data['title']= $this->lang->line('Ventas Secciones');
			#$data['icon'] = 'iconoReportTab';
			$this->_show_js('comparativa_ventas', 'oltp/ventassecciones.js', $data);
		}
	}

	/**
	 * Acción de ventas de materias, devuelve un HTML
	 *
	 * @param int $materia Materia
	 * @param date $fecha Fecha límite ventas
	 * @param bool $task Se ejecuta como tarea
	 * @return VIEW
	 */
	function ventas_materias($materia = null, $fecha = null, $task = null)
	{
		$this->userauth->roleCheck(('oltp.ventas_materias'));
		$fecha 	= isset($fecha)?$fecha:$this->input->get_post('fecha');
		#$fecha2 	= isset($fecha2)?$fecha2:$this->input->get_post('fecha2');
		$materia= isset($materia)?$materia:$this->input->get_post('materia');
		$task = isset($task)?$task:$this->input->get_post('task');

		if ($task === FALSE) $task = 1;

		if (!empty($fecha))
		{
			if ($task == 1)
			{
				$this->load->library('tasks');
				if (!is_numeric($materia)) $materia = 'null';
				$fecha = str_replace('/', '-', $fecha);
				#$fecha2 = str_replace('/', '-', $fecha2);
				$cmd = site_url("oltp/oltp/ventas_materias/{$materia}/{$fecha}/0");
				$this->tasks->add2($this->lang->line('Ventas Materias'), $cmd);
			}
			else
			{
				if ($materia == 'null') $materia = null;
				set_time_limit(0);
				$this->_ventas_materias_view($materia, $fecha/*, $fecha2*/, false);
			}
		}
		else
		{
			$data['url'] = site_url('oltp/oltp/ventas_materias');
			$data['title']= $this->lang->line('Ventas Materias');
			#$data['icon'] = 'iconoReportTab';
			$this->_show_js('ventas_materias', 'oltp/ventasmaterias.js', $data);
		}
	}

	/**
	 * Acción de ventas de secciones, devuelve un HTML
	 * @todo  Sin actualizar por no tener uso
	 * @param int $id Sección
	 * @param date $fecha Fecha límite ventas
	 * @param bool $task Se ejecuta como tarea
	 * @return VIEW
	 */
	function variacion_stock($seccion = null, $fecha = null, $task = null)
	{

		$this->userauth->roleCheck(('oltp.variacion_stock'));
		$fecha 	= isset($fecha)?$fecha:$this->input->get_post('fecha');
		$seccion= isset($seccion)?$seccion:$this->input->get_post('seccion');
		$task = isset($task)?$task:$this->input->get_post('task');

		if ($task === FALSE) $task = 1;

		if ($fecha)
		{
			if ($task == 1)
			{
				$this->load->library('tasks');
				if (!is_numeric($seccion)) $seccion = 'null';
				$fecha = str_replace('/', '-', $fecha);
				$cmd = site_url("oltp/oltp/variacion_stock/{$seccion}/{$fecha}/0");
				$this->tasks->add2($this->lang->line('Variación stocks secciones'), $cmd);
			}
			else
			{
				if ($seccion == 'null') $seccion = null;
				set_time_limit(0);
				$this->_variacion_stock_view($seccion, $fecha, false);
			}
		}
		else
		{
			$data['url'] = site_url('oltp/oltp/variacion_stock');
			$data['title']= $this->lang->line('Variación stocks secciones');
			$this->_show_js('variacion_stock', 'oltp/ventassecciones.js');
		}
	}

	/**
	 * Acción de ventas de secciones, devuelve el resultado HTML sin cabeceras, solo el BODY
	 * (para llamadas AJAX).
	 * Devuelve un HTML
	 *
	 * @param int $id Sección
	 * @param date $fecha Fecha límite ventas
	 */
	/*function ventas_secciones2($id = null, $fecha = null)
	{
		$this->userauth->roleCheck(('oltp.ventas_secciones'));
		$fecha 	= isset($fecha)?$fecha:$this->input->get_post('fecha', null);
		$id		= isset($id)?$id:$this->input->get_post('id', null);
		//die($fecha);
		$this->_ventas_secciones_view($id, $fecha, TRUE);
	}*/

	/**
	 * Acción de ventas de secciones, devuelve el resultado el HTML. Solo dibuja
	 *
	 * @param int $id Sección
	 * @param date $fecha Fecha límite ventas
	 * @param bool $ajax TRUE: devuelve el BODY, false: la página completa
	 */
	private function _variacion_stock_view($id = null, $fecha = null, $ajax = false)
	{
		if ($id == 'null') $id = null;
		$fecha = (!isset($fecha) || ($fecha == ''))?$fecha = to_date(date('d-m-Y',time())):to_date($fecha);
		$anno = date('Y', $fecha);

		$c_id = 'variacion_stock'.$id.$fecha.$ajax;

		set_time_limit(0);
		$this->load->helper('asset');
		$this->load->model('generico/m_seccion', 'sec');
		//Padre
		if (isset($id)&&($id!=''))
		{
			$s = $this->sec->load($id);
			$seccion = $s['cNombre'];
			$unido = $seccion['nHijos'] > 0;
		}
		else
		{
			$seccion = $this->lang->line('TOTAL');
			$unido = TRUE;
		}

		if ($unido)
		{
			$final = $this->_variacion_stock($seccion, $anno, $id, $fecha);
		}
		else
		{
			$final = $this->_variacion_stock($seccion, $anno, $id, $fecha);
		}
		$final['link'] = false;
		$body = $this->load->view('oltp/variacionstock', $final, TRUE);

		// Hijos
		$secciones = $this->sec->get_by_padre($id);
		foreach($secciones as $s)
		{
			$final = $this->_variacion_stock($s['cNombre'], $anno, $s['nIdSeccion'], $fecha);
			$final['link'] = TRUE;
			$final['mes'] = date('m', $fecha);
			$body .= $this->load->view('oltp/ventassecciones', $final, TRUE);
		}

		$datos['title'] = $this->lang->line('Variación stocks secciones');
		$datos['body'] = $body;
		$r = $this->load->view('oltp/reports', $datos, TRUE);
		$this->out->html_file($r, $this->lang->line('Variación stocks secciones'). ' ' . $seccion . ' ' . format_date($fecha), 'iconoReportTab', null, TRUE);
	}

	/**
	 * Acción de ventas de secciones, devuelve el resultado el HTML. Solo dibuja
	 *
	 * @param int $id Sección
	 * @param date $fecha Fecha límite ventas
	 * @param bool $ajax TRUE: devuelve el BODY, false: la página completa
	 */
	private function _ventas_secciones_view($id = null, $fecha = null, $ajax = false)
	{
		if ($id == 'null') $id = null;
		$fecha = (!isset($fecha) || ($fecha == ''))?$fecha = to_date(date('d-m-Y',time())):to_date($fecha);
		$anno = date('Y', $fecha);

		#$c_id = 'ventas_secciones'.$id.$fecha.$ajax;

		set_time_limit(0);
		$this->load->helper('asset');
		$this->load->model('generico/M_seccion', 'sec');
		//Padre
		if (isset($id)&&($id!=''))
		{
			$s = $this->sec->load($id);
			$seccion = $s['cNombre'];
			$unido = $s['nHijos'] > 0;
		}
		else
		{
			$seccion = $this->lang->line('TOTAL');
			$unido = TRUE;
		}

		if ($unido)
		{
			$final = $this->_ventas_secciones($seccion, $anno, $id, $fecha);
		}
		else
		{
			$final = $this->_ventas_secciones($seccion, $anno, $id, $fecha);
		}
		$final['link'] = false;
		$final['fecha'] = $fecha;
		$final['mes'] = (int) date('m', $fecha);
		$body = $this->load->view('oltp/ventassecciones', $final, TRUE);

		// Hijos
		$secciones = $this->sec->get_by_padre($id);
		foreach($secciones as $s)
		{
			$final = $this->_ventas_secciones($s['cNombre'], $anno, $s['nIdSeccion'], $fecha);
			$final['link'] = TRUE;
			$final['mes'] = (int) date('m', $fecha);
			$body .= $this->load->view('oltp/ventassecciones', $final, TRUE);
		}

		$datos['title'] = $this->lang->line('Ventas Secciones');
		$datos['body'] = $body;
		$r = $this->load->view('oltp/reports', $datos, TRUE);
		$this->out->html_file($r, $this->lang->line('Ventas Secciones'). ' ' . $seccion . ' ' . format_date($fecha), 'iconoReportTab', null, TRUE);
	}

	/**
	 * Acción de ventas de maetrias, devuelve el resultado el HTML. Solo dibuja
	 *
	 * @param int $id Materia
	 * @param date $fecha Fecha límite ventas
	 * @param bool $ajax TRUE: devuelve el BODY, false: la página completa
	 */
	private function _ventas_materias_view($id = null, $fecha = null, $ajax = false)
	{
		if ($id == 'null') $id = null;
		$fecha = (!isset($fecha) || ($fecha == ''))?$fecha = to_date(date('d-m-Y',time())):to_date($fecha);
		$anno = date('Y', $fecha);

		#$c_id = 'ventas_materias'.$id.$fecha.$ajax;

		set_time_limit(0);
		$this->load->helper('asset');
		$this->load->model('catalogo/m_materia', 'sec');
		//Padre
		if (isset($id)&&($id!=''))
		{
			$s = $this->sec->load($id);
			$materia = $s['cNombre'];
			$unido = $s['nHijos'] > 0;
		}
		else
		{
			$materia = $this->lang->line('TOTAL');
			$unido = TRUE;
		}

		if ($unido)
		{
			$final = $this->_ventas_materias($materia, $anno, $id, $fecha);
		}
		else
		{
			$final = $this->_ventas_materias($materia, $anno, $id, $fecha);
		}
		$final['link'] = false;
		$final['fecha'] = $fecha;
		$final['mes'] = (int) date('m', $fecha);
		$body = $this->load->view('oltp/ventasmaterias', $final, TRUE);

		// Hijos
		$materias = $this->sec->get_by_padre($id);
		foreach($materias as $s)
		{
			$final = $this->_ventas_materias($s['cNombre'], $anno, $s['nIdMateria'], $fecha);
			$final['link'] = TRUE;
			$final['mes'] = (int) date('m', $fecha);
			$body .= $this->load->view('oltp/ventasmaterias', $final, TRUE);
		}

		$datos['title'] = $this->lang->line('Ventas Materias');
		$datos['body'] = $body;
		$r = $this->load->view('oltp/reports', $datos, TRUE);
		$this->out->html_file($r, $this->lang->line('Ventas Materias'). ' ' . $materia . ' ' . format_date($fecha), 'iconoReportTab', null, TRUE);
	}

	/**
	 * Crea la vista de las ventas de secciones para una sección dada
	 *
	 * @param string $seccion Nombre de la sección
	 * @param int $anno Año máximo que mostrar (viene de la fecha)
	 * @param int $id Id de la sección
	 * @param date $fecha Fecha límite
	 * @return array
	 */
	private function _ventas_secciones($seccion, $anno, $id = null, $fecha = null)
	{
		$final['seccion'] = $seccion;
		$final['id'] = $id;
		$inicio = $this->config->item('bp.ventas.year.inicio');
		//Ventas y coste de las ventas
		$data_old = null;
		for ($i = $inicio; $i<=$anno; $i++)
		{
			$data = $this->reg->ventas_meses($i, $id, $fecha);

			$ventas = $data['ventas'];
			$coste = $data['coste'];
			$albaranes = $data['albaranes'];

			$ventas[] = array_total($ventas);
			$final['ventas'][$i] = $ventas;

			$coste[] = array_total($coste);
			$final['coste_ventas'][$i] = $coste;

			$albaranes[] = array_total($albaranes);
			$final['albaranes'][$i] = $albaranes;
			#var_dump($fecha); die();
			#echo '<pre>'; print_r($coste); echo '</pre>'; die();
			if (isset($data_old))
			{
				$final['comparacion'][$i] = array_compare_percent($data_old, $ventas);
			}
			$data_old = $ventas;
		}
		$final['Fecha'] = format_date($fecha);
		$final['anno'] = $anno;
			
		// Compras
		$data = $this->reg->compras_meses($anno, $id, $fecha);
		$data[] = array_total($data);
		$final['compras'] = $data;

		//Devoluciones
		$data = $this->reg->devoluciones_meses($anno, $id, $fecha);
		$data[] = array_total($data);
		$final['devoluciones'] = $data;
			
		//Mov. Salida
		$data = $this->reg->movimientos_origen_meses($anno, $id, $fecha);
		$data[] = array_total($data);
		$final['movsalida'] = $data;

		//Mov. Entrada
		$data = $this->reg->movimientos_destino_meses($anno, $id, $fecha);
		$data[] = array_total($data);
		$final['moventrada'] = $data;
			
		//Diferencia
		$data = array_add($final['coste_ventas'][$anno], $final['devoluciones']);
		$data = array_add($final['albaranes'][$anno], $data);
		$data = array_subs($data, $final['compras']);
		$data = array_subs($data, $final['moventrada']);
		$data = array_add($data, $final['movsalida']);
		$data[] = array_total($data);
		$final['diferencia'] = $data;
			
		return $final;
	}

	/**
	 * Crea la vista de las ventas de materias para una sección dada
	 *
	 * @param string $materia Nombre de la materia
	 * @param int $anno Año máximo que mostrar (viene de la fecha)
	 * @param int $id Id de la sección
	 * @param date $fecha Fecha límite
	 * @return array
	 */
	private function _ventas_materias($materia, $anno, $id = null, $fecha = null)
	{
		$final['materia'] = $materia;
		$final['id'] = $id;
		$inicio = $this->config->item('bp.ventas.year.inicio');
		//Ventas y coste de las ventas
		$data_old = null;
		for ($i = $inicio; $i<=$anno; $i++)
		{
			$ventas = $this->reg->ventas_meses_materias($i, $id, $fecha);
			#var_dump($ventas); die();

			$ventas[] = array_total($ventas);
			$final['ventas'][$i] = $ventas;

			if (isset($data_old))
			{
				$final['comparacion'][$i] = array_compare_percent($data_old, $ventas);
			}
			$data_old = $ventas;
		}
		$final['Fecha'] = format_date($fecha);
		$final['anno'] = $anno;
			
		return $final;
	}

	/**
	 * Crea la vista de las ventas de secciones para una sección dada
	 *
	 * @param string $seccion Nombre de la sección
	 * @param int $anno Año máximo que mostrar (viene de la fecha)
	 * @param int $id Id de la sección
	 * @param date $fecha Fecha límite
	 * @return array
	 */
	private function _variacion_stock($seccion, $anno, $id = null, $fecha = null)
	{
		$final['seccion'] = $seccion;
		$final['id'] = $id;
		$inicio = $this->config->item('bp.ventas.year.inicio');
		//Ventas y coste de las ventas
		$final['Fecha'] = format_date($fecha);
		$final['anno'] = $anno;
			
		// Compras
		$data = $this->reg->compras_meses($anno, $id, $fecha);
		$data[] = array_total($data);
		$final['compras'] = $data;

		//Devoluciones
		$data = $this->reg->devoluciones_meses($anno, $id, $fecha);
		$data[] = array_total($data);
		$final['devoluciones'] = $data;
			
		//Mov. Salida
		$data = $this->reg->movimientos_origen_meses($anno, $id, $fecha);
		$data[] = array_total($data);
		$final['movsalida'] = $data;

		//Mov. Entrada
		$data = $this->reg->movimientos_destino_meses($anno, $id, $fecha);
		$data[] = array_total($data);
		$final['moventrada'] = $data;
			
		//Diferencia
		$data = array_add($final['coste_ventas'][$anno], $final['devoluciones']);
		$data = array_add($final['albaranes'][$anno], $data);
		$data = array_subs($data, $final['compras']);
		$data = array_subs($data, $final['moventrada']);
		$data = array_add($data, $final['movsalida']);
		$data[] = array_total($data);
		$final['diferencia'] = $data;
			
		return $final;
	}

	/**
	 * Genera una comparativa de las ventas por series a una fecha con el mismo día
	 * del año anterior.
	 * Devuelve el HTML del informe
	 *
	 * @param string $fecha Fecha
	 */
	function comparativa_ventas($fecha1 = null, $fecha2 = null)
	{
		$this->userauth->roleCheck('oltp.comparativa_ventas');
		$fecha1 	= isset($fecha1)?$fecha1:$this->input->get_post('fecha1');
		$fecha2 	= isset($fecha2)?$fecha2:$this->input->get_post('fecha2');

		if (!empty($fecha1) && !empty($fecha2))
		{
			$fecha1 = to_date($fecha1);
			$fecha2 = to_date($fecha2);

			$y1= date('Y', $fecha1);
			$y2= date('Y', $fecha2);

			if ($y1 >= $y2)
				$this->out->error(sprintf($this->lang->line('comparativa_ventas-error-year'), $y1, $y2));

			$cmd = site_url("oltp/oltp/comparativa_ventas_task/{$fecha1}/{$fecha2}");

			$this->load->library('tasks');
			$this->tasks->add2($this->lang->line('Comparativa Ventas Áreas') , $cmd);
		}
		else
		{
			$data['url'] = site_url('oltp/oltp/comparativa_ventas');
			$data['title'] = $this->lang->line('Comparativa Ventas Áreas');
			$data['icon'] = 'iconoReportTab';
			$this->_show_js('comparativa_ventas', 'oltp/ventassiniva.js', $data);
		}
	}

	/**
	 * Genera una comparativa de las ventas por series a una fecha con el mismo día
	 * del año anterior.
	 * Devuelve el HTML del informe
	 *
	 * @param date $fecha1 Fecha de inicio
	 * @param date $fecha2 Fecha de final
	 * @return HTML
	 */
	function comparativa_ventas_task($fecha1 = null, $fecha2 = null)
	{
		$this->userauth->roleCheck(('oltp.comparativa_ventas'));

		$fecha1 	= isset($fecha1)?$fecha1:$this->input->get_post('fecha1');
		$fecha2 	= isset($fecha2)?$fecha2:$this->input->get_post('fecha2');

		set_time_limit(0);

		$this->load->helper('asset');
		#$fecha2 = $this->utils->yearbefore($fecha);

		$valores = $this->reg->comparativa_ventas($fecha2, $fecha1);

		$data['valores'] = $valores;
		$data['fecha1'] = format_date($fecha1);
		$data['fecha2'] = format_date($fecha2);
		$data['year1'] = date('Y', $fecha1);
		$data['year2'] = date('Y', $fecha2);

		$body = $this->load->view('oltp/comparativaventas', $data, TRUE);

		$datos['title'] = $this->lang->line('Comparativa Ventas Áreas');
		$datos['body'] = $body;
		$r = $this->load->view('oltp/reports', $datos, TRUE);

		$this->out->html_file($r, $this->lang->line('Comparativa Ventas Áreas'). ' ' . format_date($fecha1). ' ' . format_date($fecha2), 'iconoReportTab', null, TRUE);
	}

	/**
	 * Genera una comparativa de stocks a 2 fechas
	 * Devuelve el HTML del informe
	 *
	 * @param string $fecha1 Fecha inicio
	 * @param string $fecha2 Fecha final
	 * @param int $id Id de las sección
	 * @return HTML
	 */
	function comparativa_stocks($fecha1 = null, $fecha2 = null, $id = null)
	{
		$this->userauth->roleCheck(('oltp.comparativa_stocks'));

		$fecha1	= isset($fecha1)?$fecha1:$this->input->get_post('fecha1', null);
		$fecha2	= isset($fecha2)?$fecha2:$this->input->get_post('fecha2', null);
		$id		= isset($id)?$id:$this->input->get_post('id', null);

		if ($fecha1 &&  $fecha2)
		{

			if ($id == 'null' || $id == '') $id = null;
			$fecha1 = to_date($fecha1);
			$fecha2 = to_date($fecha2);

			$c_id = 'comparativa_stocks'.$fecha1.$fecha2.$id;

			// Valoración del stock
			$r = array();
			$valor1 = $this->reg->antiguedad_seccion($id, $fecha1);
			#echo '<pre>'; var_dump($this->db->queries); echo '</pre>';
			#var_dump($valor1); die();
			$total1 = array();
			if (!count($valor1))
			{
				$this->out->error(sprintf($this->lang->line('stocks-no-day'), format_date($fecha1)));
			}
			foreach($valor1 as $v)
			{
				$r[$v['cSeccion']]['valor1'] = $v;
				$total1 = array_add($v, $total1);
			}

			$valor2 = $this->reg->antiguedad_seccion($id, $fecha2);
			$total2 = array();
			if (!count($valor2))
			{
				$this->out->error(sprintf($this->lang->line('stocks-no-day'), format_date($fecha2)));
			}
			foreach($valor2 as $v)
			{
				$r[$v['cSeccion']]['valor2'] = $v;
				$total2 = array_add($v, $total2);
			}

			$data['valores'] = $r;
			$data['depreciacion1'] = $this->reg->depreciar($total1['Importe1'] ,
			$total1['Importe2'],
			$total1['Importe3'],
			$total1['Importe4']);
			$data['depreciacion2'] = $this->reg->depreciar($total2['Importe1'] ,
			$total2['Importe2'],
			$total2['Importe3'],
			$total2['Importe4']);
			if (!is_numeric($id))
			{
				$data['depreciacion_ant'] = $this->config->item('bp.oltp.valordpr');
				$data['depreciacion_fecha'] = $this->config->item('bp.oltp.fechadpr');
			}
			$data['fecha1'] = format_date($fecha1);
			$data['fecha2'] = format_date($fecha2);
			$data['total1'] = $total1;
			$data['total2'] = $total2;

			#print '<pre>'; var_dump($data); print '</pre>';
			$this->load->helper('asset');
			$body = $this->load->view('oltp/comparativastocks', $data, TRUE);

			$datos['title'] = $this->lang->line('Comparativa Stocks');
			$datos['body'] = $body;
			$r = $this->load->view('oltp/reports', $datos, TRUE);
			$this->out->html_file($r, $this->lang->line('Comparativa Stocks'), 'iconoReportTab', null, TRUE);
		}
		else
		{
			$data['title'] = $this->lang->line('Comparativa Stocks');
			$data['url'] = site_url('oltp/oltp/comparativa_stocks');
			$data['una'] = FALSE;
			$this->_show_js('comparativa_stocks', 'oltp/comparativastocks.js', $data);
		}
	}

	/**
	 * Desglosa el stock a una fecha dada para una sección indicada y de sus hijas
	 * Devuelve el HTML del informe
	 *
	 * @param string $fecha Fecha
	 * @param int $id Id de las sección
	 * @return HTML
	 */
	function ver_stocks($fecha1 = null, $id = null)
	{
		$this->userauth->roleCheck(('oltp.comparativa_stocks'));

		$fecha1	= isset($fecha1)?$fecha1:$this->input->get_post('fecha1', null);
		$id		= isset($id)?$id:$this->input->get_post('id', null);

		if ($fecha1)
		{
			if ($id == 'null' || $id == '') $id = null;
			$fecha1 = to_date($fecha1);

			$c_id = 'ver_stocks'.$fecha1.$id;

			// Valoración del stock
			$r = array();
			$valor1 = $this->reg->antiguedad_seccion($id, $fecha1);
			#echo '<pre>'; print_r($this->db->queries); echo '</pre>';
			#var_dump($valor1); die();
			$total1 = array();
			if (!count($valor1))
			{
				$this->out->error(sprintf($this->lang->line('stocks-no-day'), format_date($fecha1)));
			}
			foreach($valor1 as $v)
			{
				$r[$v['cSeccion']]['valor1'] = $v;
				$total1 = array_add($v, $total1);
			}

			$data['valores'] = $r;
			$data['depreciacion1'] = $this->reg->depreciar($total1['Importe1'] ,
			$total1['Importe2'],
			$total1['Importe3'],
			$total1['Importe4']);
			if (!is_numeric($id))
			{
				$data['depreciacion_ant'] = $this->config->item('bp.oltp.valordpr');
				$data['depreciacion_fecha'] = $this->config->item('bp.oltp.fechadpr');
			}
			$data['fecha1'] = format_date($fecha1);
			$data['total1'] = $total1;

			#print '<pre>'; print_r($data); print '</pre>'; die();
			$body = $this->load->view('oltp/stocksdia', $data, TRUE);

			$datos['title'] = $this->lang->line('Stocks a una fecha');
			$datos['body'] = $body;
			$r = $this->load->view('oltp/reports', $datos, TRUE);
			$this->out->html_file($r, $this->lang->line('Stocks a una fecha'), 'iconoReportTab', null, TRUE);
		}
		else
		{
			$data['title'] = $this->lang->line('Stocks a una fecha');
			$data['url'] = site_url('oltp/oltp/ver_stocks');
			$data['una'] = TRUE;
			$this->_show_js('comparativa_stocks', 'oltp/comparativastocks.js', $data);
		}
	}

	/**
	 * Cobros por caja, día y modo entre 2 fechas
	 * Devuelve los datos en JSON
	 * @param data $fecha1 Fecha incial
	 * @param data $fecha2 Fecha final
	 * @return HTML
	 */
	function caja_dia_modo($fecha1 = null, $fecha2 = null, $caja = null, $modo = null)
	{
		$this->userauth->roleCheck(('oltp.caja_dia_modo'));

		$fecha1	= isset($fecha1)?$fecha1:$this->input->get_post('fecha1');
		$fecha2	= isset($fecha2)?$fecha2:$this->input->get_post('fecha2');
		$caja	= isset($caja)?$caja:$this->input->get_post('caja');
		$modo	= isset($modo)?$modo:$this->input->get_post('modo', null);

		if ($fecha1 && $fecha2)
		{
			$fecha1 = to_date($fecha1);
			$fecha2 = to_date($fecha2);

			#$c_id = 'caja_dia_modo'.$fecha1.$fecha2;

			set_time_limit(0);

			$data['fecha1'] = format_date($fecha1);
			$data['fecha2'] = format_date($fecha2);
			$caja = ($caja == FALSE)?null:$caja;
			$modo = ($modo == FALSE)?null:$modo;
			#var_dump($caja); var_dump($modo);
			$data['valores'] = $this->reg->caja_dia_modo($fecha1, $fecha2, $caja, $modo);
			if (count($data['valores']) == 0)
				$this->out->success($this->lang->line('no-hay-documentos'));
			ksort($data['valores']['data']);
			#var_dump($data['valores']); die();
			#sksort($data['valores']['data'], 'dDia');

			#print '<pre>'; print_r($data['valores']); print '</pre>';
			#return;

			$this->load->helper('asset');
			$body = $this->load->view((isset($caja) && isset($modo))?'oltp/cajadiamododesglose':'oltp/cajadiamodo', $data, TRUE);

			$datos['title'] = $this->lang->line('Cobros por día, caja y modo');
			$datos['body'] = $body;
			$r = $this->load->view('oltp/reports', $datos, TRUE);
			/*if ($this->config->item('bp.cache.html'))
			 {
				$this->cache->save($c_id, $r, 'html', 0);
				}*/
			$this->out->html_file($r, $this->lang->line('Cobros por día, caja y modo'), 'iconoReportTab', null, TRUE);
		}
		$this->_show_js('caja_dia_modo', 'oltp/cajadiamodo.js');
	}

	/**
	 * Ventas en un periodo.
	 * Usa caché
	 *
	 * @param datatime $fecha1 Fecha inicial
	 * @param datatime $fecha2 Fecha final
	 * @param int $idserie Id de la serie
	 * @param int $idseccion Id de la sección
	 * @param string $cmpdto Comparación descuento (>, =, <)
	 * @param int $dto Descuento
	 * @param string $cmpmargen Comparación margen (>, =, <)
	 * @param int $margen Margen
	 * @return HTML
	 */
	function ventas_periodo($list = null, $fecha1 = null, $fecha2 = null, $idserie = null, $idseccion = null, $idarea = null, $idcliente = null, $cmpdto = null, $dto = null, $cmpmargen = null, $margen = null)
	{
		$this->userauth->roleCheck(('oltp.ventas_periodo'));

		$list		= isset($list)?$list:$this->input->get_post('list');
		$fecha1		= isset($fecha1)?$fecha1:$this->input->get_post('fecha1');
		$fecha2		= isset($fecha2)?$fecha2:$this->input->get_post('fecha2');
		$idseccion	= isset($idseccion)?$idseccion:$this->input->get_post('idseccion');
		$idserie	= isset($idserie)?$idserie:$this->input->get_post('idserie');
		$idarea		= isset($idarea)?$idarea:$this->input->get_post('idarea');
		$cmpdto		= isset($cmpdto)?$cmpdto:$this->input->get_post('cmpdto');
		$cmpmargen	= isset($cmpmargen)?$cmpmargen:$this->input->get_post('cmpmargen');
		$dto		= isset($dto)?$dto:$this->input->get_post('dto');
		$margen		= isset($margen)?$margen:$this->input->get_post('margen');
		$idcliente	= isset($idcliente)?$idcliente:$this->input->get_post('idcliente');

		if (!is_numeric($idserie)) $idserie = null;
		if (!is_numeric($idseccion)) $idseccion = null;
		if (!is_numeric($idarea)) $idarea = null;
		if (!is_numeric($idcliente)) $idcliente = null;
		if (!is_numeric($list)) $list = 0;

		if ($fecha1 && $fecha2)
		{
			set_time_limit(0);

			$fecha1 = to_date($fecha1);
			$fecha2 = to_date($fecha2);

			$c_id = 'ventas_periodo'.$fecha1.$fecha2.$idcliente.$idserie.$idseccion.$cmpdto.$cmpmargen.$dto.$margen.$idarea.$list;

			$unido = ((int)$list != 1);
			if ((isset($dto) && (isset($cmpdto)) && ($cmpdto!=''))||(isset($margen) && (isset($cmpmargen)) && ($cmpmargen != '')))
			{
				$unido = FALSE;
			}			
			//Padre
			if (is_numeric($idseccion))
			{
				$this->load->model('generico/m_seccion');
				$seccion = $this->m_seccion->get(0, 1, null, null, "nIdSeccionPadre = {$idseccion}");
			}
			else
			{
				$seccion = $this->lang->line('TOTAL');
			}
			#echo $unido?'UNIDO':'NO UNIDO'; die();
			$data['fecha1'] = format_date($fecha1);
			$data['fecha2'] = format_date($fecha2);
			$data['idserie'] = $idserie;
			$data['idseccion'] = $idseccion;
			$data['idcliente'] = $idcliente;
			$data['desglosado'] = !$unido;

			if ($unido)
			{
				$data['valores'] = $this->reg->ventas_periodo_secciones($fecha1, $fecha2, $idserie, $idseccion, $idarea, $idcliente);
			}
			else
			{
				$data['valores'] = $this->reg->ventas_periodo_libros($fecha1, $fecha2, $idserie, $idseccion, $idarea, $cmpdto, $dto, $cmpmargen, $margen, $idcliente);
			}


			$this->load->helper('asset');
			$body = $this->load->view('oltp/ventasperiodo', $data, TRUE);

			$datos['title'] = $this->lang->line('Ventas en un Periodo');
			$datos['body'] = $body;
			$r = $this->load->view('oltp/reports', $datos, TRUE);
			$this->out->html_file($r, $this->lang->line('Ventas en un Periodo'), 'iconoReportTab', null, TRUE);
		}
		else
		{
			$this->_show_js('ventas_periodo', 'oltp/ventasperiodo.js');
		}
	}

	/**
	 * Compras en un periodo.
	 *
	 * @param datatime $fecha1 Fecha inicial
	 * @param datatime $fecha2 Fecha final
	 * @param int $idserie Id de la serie
	 * @param int $idseccion Id de la sección
	 * @param string $cmpdto Comparación descuento (>, =, <)
	 * @param int $dto Descuento
	 * @param string $cmpmargen Comparación margen (>, =, <)
	 * @param int $margen Margen
	 * @return HTML
	 */
	function compras_periodo($list = null, $fecha1 = null, $fecha2 = null, $idseccion = null, $idproveedor = null, $cmpdto = null, $dto = null, $sinalbaran = null)
	{
		$this->userauth->roleCheck(('oltp.ventas_periodo'));

		$list		= isset($list)?$list:$this->input->get_post('list');
		$fecha1		= isset($fecha1)?$fecha1:$this->input->get_post('fecha1');
		$fecha2		= isset($fecha2)?$fecha2:$this->input->get_post('fecha2');
		$idseccion	= isset($idseccion)?$idseccion:$this->input->get_post('idseccion');
		$cmpdto		= isset($cmpdto)?$cmpdto:$this->input->get_post('cmpdto');
		$dto		= isset($dto)?$dto:$this->input->get_post('dto');
		$idproveedor= isset($idproveedor)?$idproveedor:$this->input->get_post('idproveedor');
		$sinalbaran	= isset($sinalbaran)?$sinalbaran:$this->input->get_post('sinalbaran');

		if (!is_numeric($idseccion)) $idseccion = null;
		if (!is_numeric($idproveedor)) $idproveedor = null;
		if (empty($list)) $list = 0;
		if (empty($sinalbaran)) $sinalbaran = 0;

		if ($fecha1 && $fecha2)
		{
			set_time_limit(0);

			$fecha1 = to_date($fecha1);
			$fecha2 = to_date($fecha2);

			#$c_id = 'ventas_periodo'.$fecha1.$fecha2.$idcliente.$idserie.$idseccion.$cmpdto.$cmpmargen.$dto.$margen.$idarea.$list;
			$list = format_tobool($list);
			$sinalbaran = format_tobool($sinalbaran);
			$unido = ((int)$list != 1);
			if ((isset($dto) && (isset($cmpdto)) && ($cmpdto!=''))||(isset($margen) && (isset($cmpmargen)) && ($cmpmargen != '')))
			{
				$unido = FALSE;
			}			
			//Padre
			if (is_numeric($idseccion))
			{
				$this->load->model('generico/m_seccion');
				$data['seccion'] = $this->m_seccion->load($idseccion);
			}
			#var_dump($idproveedor);
			if (is_numeric($idproveedor))
			{
				$this->load->model('proveedores/m_proveedor');
				$data['proveedor'] = $this->m_proveedor->load($idproveedor);				
			} 
			#echo $unido?'UNIDO':'NO UNIDO'; die();
			$data['fecha1'] = format_date($fecha1);
			$data['fecha2'] = format_date($fecha2);
			$data['idseccion'] = $idseccion;
			$data['idproveedor'] = $idproveedor;
			$data['desglosado'] = !$unido;
			$data['sinalbaran'] = $sinalbaran;

			if ($unido)
			{
				$data['valores'] = $this->reg->compras_periodo_secciones($fecha1, $fecha2, $idseccion, $idproveedor);
			}
			elseif ($sinalbaran)
			{
				$data['valores'] = $this->reg->compras_periodo_libros_sin($fecha1, $fecha2, $idseccion, /*$cmpdto, $dto,*/ $idproveedor);
				#var_dump($data); die();
			}
			else
			{
				$data['valores'] = $this->reg->compras_periodo_libros($fecha1, $fecha2, $idseccion, /*$cmpdto, $dto,*/ $idproveedor);
				#var_dump($data); die();
			}
			
			#var_dump($data); die();

			$this->load->helper('asset');
			$body = $this->load->view('oltp/comprasperiodo', $data, TRUE);

			#echo $body; die();

			$datos['title'] = $this->lang->line('Compras en un periodo');
			$datos['body'] = $body;
			$r = $this->load->view('oltp/reports', $datos, TRUE);
			$this->out->html_file($r, $this->lang->line('Compras en un periodo'), 'iconoReportTab', null, TRUE);
		}
		else
		{
			$this->_show_js('ventas_periodo', 'oltp/comprasperiodo.js');
		}
	}

	/**
	 * Ventas sin iva.
	 * Usa caché
	 *
	 * @param date $fecha1 Fecha inicial
	 * @param date $fecha2 Fecha final
	 * @param int $idtipo Tipo de grupo de IVA
	 * @return HTML
	 */
	function ventas_sin_iva($fecha1 = null, $fecha2 = null, $idtipo = null)
	{
		$this->userauth->roleCheck(('oltp.ventas_sin_iva'));

		$fecha1		= isset($fecha1)?$fecha1:$this->input->get_post('fecha1');
		$fecha2		= isset($fecha2)?$fecha2:$this->input->get_post('fecha2');
		$idtipo		= isset($idtipo)?$idtipo:$this->input->get_post('idtipo');

		if ($fecha1 && $fecha2)
		{
			$fecha1 = to_date($fecha1);
			$fecha2 = to_date($fecha2);
			$cmd = site_url("oltp/oltp/ventas_sin_iva_task/{$fecha1}/{$fecha2}/{$idtipo}");

			$this->load->library('tasks');
			$this->tasks->add2($this->lang->line('report-ventas-exentas-iva') , $cmd);
		}
		else
		{
			$data['title'] = $this->lang->line('report-ventas-exentas-iva');
			$data['url'] = site_url('oltp/oltp/ventas_sin_iva');
			$this->_show_js('ventas_sin_iva', 'oltp/ventassiniva.js', $data);
		}
	}

	/**
	 * Ventas sin iva.
	 * Usa caché
	 *
	 * @param date $fecha1 Fecha inicial
	 * @param date $fecha2 Fecha final
	 * @param int $idtipo Tipo de grupo de IVA
	 * @return HTML
	 */
	function ventas_sin_iva_task($fecha1 = null, $fecha2 = null, $idtipo = null)
	{
		$this->userauth->roleCheck(('oltp.ventas_sin_iva'));

		$fecha1		= isset($fecha1)?$fecha1:$this->input->get_post('fecha1', null);
		$fecha2		= isset($fecha2)?$fecha2:$this->input->get_post('fecha2', null);
		$idtipo		= isset($idtipo)?$idtipo:$this->input->get_post('idtipo', null);

		if ($fecha1 && $fecha2)
		{
			if ($idtipo == '') $idtipo = null;

			$c_id = 'ventas_sin_iva2'.$fecha1.$fecha2.$idtipo;

			set_time_limit(0);

			$data['fecha1'] = format_date($fecha1);
			$data['fecha2'] = format_date($fecha2);
			if ($idtipo)
			{
				$data['idtipo'] = $idtipo;
				$data['valores'] = $this->reg->ventas_sin_iva_desglose($fecha1, $fecha2, $idtipo);

				$body = $this->load->view('oltp/ventassinivadesglose', $data, TRUE);
			}
			else
			{
				$data['valores'] = $this->reg->ventas_sin_iva($fecha1, $fecha2, $idtipo);

				$body = $this->load->view('oltp/ventassiniva', $data, TRUE);

			}
			// Respuesta
			$this->out->html_file($body, $this->lang->line('report-ventas-exentas-iva'), 'iconoReportTab');
		}
	}

	/**
	 * Títulos vendidos
	 * @param date $fecha1 Desde
	 * @param date $fecha2 Hasta
	 * @param int $min Unidades mínimas
	 * @param int $id Id sección
	 */
	function ventas_titulos($fecha1 = null, $fecha2 = null, $min = null, $id = null)
	{
		$this->userauth->roleCheck(('oltp.ventas_titulos'));

		$fecha1		= isset($fecha1)?$fecha1:$this->input->get_post('fecha1');
		$fecha2		= isset($fecha2)?$fecha2:$this->input->get_post('fecha2');
		$id			= isset($id)?$id:$this->input->get_post('id');
		$min		= isset($min)?$min:$this->input->get_post('min');

		if ($fecha1 && $fecha2)
		{

			$fecha1 = to_date($fecha1);
			$fecha2 = to_date($fecha2);

			if ($id === FALSE || $id == '' || $id==-1)  $id = null;
			if ($min === FALSE)  $min = 1;
			$c_id = 'ventas_titulos'.$fecha1.$fecha2.$id.$min;

			if (isset($id))
			{
				$this->load->model('generico/m_seccion');
				$seccion = $this->m_seccion->load($id);
				$data['seccion'] = $seccion['cNombre'];
			}
			else
			{
				$data['seccion'] = $this->lang->line('Todo');
			}

			$data['fecha1'] = $fecha1;
			$data['fecha2'] = $fecha2;
			$data['min'] = $min;
			$data['id'] = $id;
			$data['valores'] = $this->reg->ventas_titulos($fecha1, $fecha2, $min, $id);

			$this->load->helper('asset');
			$body = $this->load->view('oltp/ventastitulos', $data, TRUE);
			$this->out->html_file($body, $this->lang->line('Ventas por Títulos'), 'iconoReportTab');
		}
		else
		{
			$this->_show_js('ventas_titulos', 'oltp/ventastitulos.js');
		}
	}

	/**
	 * Genera una comparativa de stocks a 2 fechas
	 * Devuelve el HTML del informe
	 *
	 * @param string $fecha1 Fecha inicio
	 * @param string $fecha2 Fecha final
	 * @param int $id Id de las sección
	 * @return HTML
	 */
	function desglose_antiguedad($fecha = null, $tipo = null, $orden = null, $ids = null)
	{
		$this->userauth->roleCheck(('oltp.comparativa_stocks'));

		$fecha	= isset($fecha)?$fecha:$this->input->get_post('fecha');
		$ids		= isset($ids)?$ids:$this->input->get_post('ids');
		$tipo = isset($tipo) ? $tipo : $this->input->get_post('tipo');
		$orden = isset($orden) ? $orden : $this->input->get_post('orden');		

		if ($fecha)
		{

			if ($ids == 'null' || $ids == '') $ids = null;
			$fecha = to_date($fecha);
			$orden = urldecode($orden);

			#var_dump($orden); die();
			if (!is_string($orden) || $orden === FALSE || $orden === 0 || $orden =='') $orden = 'cTitulo';
			if ($tipo == '') $tipo = null;
			if (isset($orden)) $orden = urldecode($orden);
			if (!isset($orden) || ($orden == '')) $orden = 'cTitulo';


			// Artículos
			$datos = $this->reg->antiguedad_seccion_desglose($ids, $fecha, $orden, $tipo);
			#echo '<pre>'; echo array_pop($this->db->queries); echo '</pre>';
			#var_dump($datos); die();
			if (!count($datos))
			{
				$this->out->error(sprintf($this->lang->line('stocks-no-day'), format_date($fecha1)));
			}
			$data['fecha'] = format_date($fecha);
			$data['valores'] = $datos;
			$message = $this->load->view('oltp/antiguedaddesglose', $data, TRUE);
			#echo $message; die();
			$this->out->html_file($message, $this->lang->line('Desglose antiguedad') . ' ' . $data['fecha'], 'iconAntiguoTab');
		}
		else
		{
			$this->_show_js('comparativa_stocks', 'oltp/desgloseantiguedad.js');
		}
	}

	/**
	 * Ventas por horas y días
	 * @param int $seccion Id de la sección
	 * @param int $fecha1 Fecha inicial
	 * @param int $fecha2 Fecha final
	 * @param bool $sj TRUE: no tiene en cuenta SANT JORDI
	 * @param string $multi IDs de secciones separadas por espacio, comas (,) o puntocoma (;)
	 * @return HTML
	 */
	function ventas_horas($seccion = null, $fecha1 = null, $fecha2 = null, $sj = null, $multi = null)
	{
		$this->userauth->roleCheck('oltp.ventas_horas');

		$fecha1		= isset($fecha1)?$fecha1:$this->input->get_post('fecha1');
		$fecha2		= isset($fecha2)?$fecha2:$this->input->get_post('fecha2');
		$seccion 	= isset($seccion)?$seccion:$this->input->get_post('seccion');
		$multi 	= isset($multi)?$multi:$this->input->get_post('multi');

		$sj = isset($sj)?$sj:$this->input->get_post('sj');

		$sj= !format_tobool($sj);

		if ($fecha1 && $fecha2)
		{
			$fecha1 = to_date($fecha1);
			$fecha2 = to_date($fecha2);
			if (!empty($multi))
			{
				$multi = preg_split('/[\;\s\n\r\;,]/', $multi);
				$multi = array_unique($multi);
				$seccion = null;
			}
			#var_dump($multi); die();
			$datos = $this->reg->ventas_horas($seccion, $fecha1, $fecha2, $sj, $multi);
			#echo '<pre>'; print_r($datos); die();
			#echo array_pop($this->db->queries); die();

			# Ordenamos por meses, días y horas. COPON
			$final = array();
			foreach ($datos as $reg)
			{
				$final[$reg['yy']][$reg['mm']]['datos'][$reg['dd']][$reg['hh']] = $reg;
				$final[$reg['yy']][$reg['mm']]['max'] = max(isset($final[$reg['yy']][$reg['mm']]['max'])?$final[$reg['yy']][$reg['mm']]['max']:0, $reg['hh']);
				$final[$reg['yy']][$reg['mm']]['min'] = min(isset($final[$reg['yy']][$reg['mm']]['min'])?$final[$reg['yy']][$reg['mm']]['min']:23, $reg['hh']);

				$subs[$reg['yy']]['max'] = max(isset($subs[$reg['yy']]['max'])?$subs[$reg['yy']]['max']:0, $reg['hh']);
				$subs[$reg['yy']]['min'] = min(isset($subs[$reg['yy']]['min'])?$subs[$reg['yy']]['min']:23, $reg['hh']);

				if (!isset($final[$reg['yy']][$reg['mm']]['total_dia'][$reg['dd']]))
					$final[$reg['yy']][$reg['mm']]['total_dia'][$reg['dd']] = 0;
				if (!isset($final[$reg['yy']][$reg['mm']]['total_dw'][$reg['dw']]))
					$final[$reg['yy']][$reg['mm']]['total_dw'][$reg['dw']] = 0;
				if (!isset($final[$reg['yy']][$reg['mm']]['total_hh'][$reg['hh']]))
					$final[$reg['yy']][$reg['mm']]['total_hh'][$reg['hh']] = 0;
				if (!isset($final[$reg['yy']][$reg['mm']]['total']))
					$final[$reg['yy']][$reg['mm']]['total'] = 0;
				if (!isset($final[$reg['yy']][$reg['mm']]['total_wk_dw'][$reg['wk']][$reg['dw']]))
					$final[$reg['yy']][$reg['mm']]['total_wk_dw'][$reg['wk']][$reg['dw']] = 0;
				if (!isset($final[$reg['yy']][$reg['mm']]['total_wk'][$reg['wk']]))
					$final[$reg['yy']][$reg['mm']]['total_wk'][$reg['wk']] = 0;

				if (!isset($subs[$reg['yy']]['total_dia'][$reg['dd']]))
					$subs[$reg['yy']]['total_dia'][$reg['dd']] = 0;
				if (!isset($subs[$reg['yy']]['total_dw'][$reg['dw']]))
					$subs[$reg['yy']]['total_dw'][$reg['dw']] = 0;
				if (!isset($subs[$reg['yy']]['total_hh'][$reg['hh']]))
					$subs[$reg['yy']]['total_hh'][$reg['hh']] = 0;
				if (!isset($subs[$reg['yy']]['total']))
					$subs[$reg['yy']]['total'] = 0;
				if (!isset($subs[$reg['yy']]['total_wk_dw'][$reg['wk']][$reg['dw']]))
					$subs[$reg['yy']]['total_wk_dw'][$reg['wk']][$reg['dw']] = 0;
				if (!isset($subs[$reg['yy']]['total_wk'][$reg['wk']]))
					$subs[$reg['yy']]['total_wk'][$reg['wk']] = 0;
				if (!isset($subs[$reg['yy']]['total_mm'][$reg['mm']]))
					$subs[$reg['yy']]['total_mm'][$reg['mm']] = 0;

				$final[$reg['yy']][$reg['mm']]['total_dia'][$reg['dd']] += $reg['vv'];
				$final[$reg['yy']][$reg['mm']]['total_dw'][$reg['dw']] += $reg['vv'];
				$final[$reg['yy']][$reg['mm']]['total_hh'][$reg['hh']] += $reg['vv'];
				$final[$reg['yy']][$reg['mm']]['total_wk'][$reg['wk']] += $reg['vv'];
				$final[$reg['yy']][$reg['mm']]['total_wk_dw'][$reg['wk']][$reg['dw']] += $reg['vv'];
				$final[$reg['yy']][$reg['mm']]['total'] += $reg['vv'];

				$subs[$reg['yy']]['total_dia'][$reg['dd']] += $reg['vv'];
				$subs[$reg['yy']]['total_dw'][$reg['dw']] += $reg['vv'];
				$subs[$reg['yy']]['total_hh'][$reg['hh']] += $reg['vv'];
				$subs[$reg['yy']]['total_wk'][$reg['wk']] += $reg['vv'];
				$subs[$reg['yy']]['total_wk_dw'][$reg['wk']][$reg['dw']] += $reg['vv'];
				$subs[$reg['yy']]['total'] += $reg['vv'];
				$subs[$reg['yy']]['total_mm'][$reg['mm']] += $reg['vv'];
			}

			$data['fecha1'] = format_date($fecha1);
			$data['fecha2'] = format_date($fecha2);
			$data['seccion'] = (!empty($multi))?implode(', ', $multi):$seccion;
			$data['valores'] = $final;
			$data['subs'] = $subs;
			$data['sj'] = $sj;
			$body = $this->load->view('oltp/ventashoras', $data, TRUE);
			#echo $body; die();
			$datos['title'] = $this->lang->line('Ventas por horas y días');
			$datos['body'] = $body;
			$r = $this->load->view('oltp/reports', $datos, TRUE);
			$this->out->html_file($r, $this->lang->line('Ventas por horas y días'), 'iconoReportTab', null, TRUE);
			$this->out->html_file($body, $this->lang->line('Ventas por horas y días'), 'iconoReportTab');
		}
		else
		{
			$data['url'] = site_url('oltp/oltp/ventas_horas');
			$data['title'] = $this->lang->line('Ventas por horas y días');
			$this->_show_js('ventas_horas', 'oltp/ventashoras.js', $data);
		}

	}
}

/* End of file oltp.php */
/* Location: ./system/application/controllers/oltp.php */
