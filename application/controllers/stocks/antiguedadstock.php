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
 * Antigüedad del Stock
 *
 */
class Antiguedadstock extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Antiguedadstock
	 */
	function __construct()
	{
		parent::__construct('stocks.antiguedadstock', 'stocks/M_antiguedadstock', TRUE, null, 'Antigüedad stock');
	}
	
	/**
	 * Analiza el valor de stock de retrocedido
	 * @return HTML_FILE
	 */
	function analisis()
	{
		$this->userauth->roleCheck($this->auth . '.analisis');

		// Modelos
		$dpr1 = $this->config->item('bp.oltp.depreciacion1');
		$dpr2 = $this->config->item('bp.oltp.depreciacion2');
		$dpr3 = $this->config->item('bp.oltp.depreciacion3');
		$dpr4 = $this->config->item('bp.oltp.depreciacion4');
		$data = $this->reg->analisis();
		$data['nCosteDepreciado1'] = $data['nCosteFirme1'] * ($dpr1);
		$data['nCosteDepreciado2'] = $data['nCosteFirme2'] * ($dpr2);
		$data['nCosteDepreciado3'] = $data['nCosteFirme3'] * ($dpr3);
		$data['nCosteDepreciado4'] = $data['nCosteFirme4'] * ($dpr4);
		$data['fecharetroceso'] = $this->config->item('bp.oltp.fechadpr');

		$message = $this->load->view('stocks/depreciacion', $data, TRUE);
		$this->out->html_file($message, $this->lang->line('Análisis stock retrocedido'), 'iconoReportTab');
	}

	/**
	 * Retrocede el stock
	 * @param int $task 0: Se ejecuta ahora, 1: se ejecuta como tarea
	 * @return DIALOG
	 */
	function get_retroceso($task = null)
	{
		$this->userauth->roleCheck($this->auth . '.retroceder');
		$task = isset($task)?$task:$this->input->get_post('task');

		if ($task === FALSE) $task = 1;

		if ($task == 1)
		{
			$this->load->library('tasks');
			$cmd = site_url("stocks/antiguedadstock/get_retroceso/0");
			$this->tasks->add2($this->lang->line('Generar EXCEL retroceso'), $cmd);
		}
		else
		{
			set_time_limit(0);

			$this->load->library('ExcelData');
			$this->load->library('HtmlFile');
			$this->load->library('zip');


			// Stock retrocedido
			$wb = $this->exceldata->create();
			$data = $this->reg->get();
			$data = array_chunk($data, $this->config->item('bp.max.excel.rows'));
			$ct = 1;
			foreach ($data as $d)
			{
				$this->exceldata->add($wb, $d, $this->lang->line('Stock Retrocedido') . $ct);
				++$ct;
			}

			$this->exceldata->close($wb);
			$file = $this->htmlfile->pathfile($this->exceldata->get_filename($wb));
			$xls = $this->htmlfile->pathfile($this->lang->line('Stock Retrocedido') . '.xls');
			copy($file, $xls);
			unlink($file);
			$this->zip->read_file($xls);

			// Documentos que afectan
			$wb = $this->exceldata->create();
			$data = $this->reg->documentos();
			$this->exceldata->add($wb, $data['albaranesentrada'], $this->lang->line('Albaranes de entrada'));
			$this->exceldata->add($wb, $data['albaranessalida'], $this->lang->line('Albaranes de salida'));
			$this->exceldata->add($wb, $data['devoluciones'], $this->lang->line('Devoluciones'));
			$this->exceldata->add($wb, $data['ajustes'], $this->lang->line('Ajustes de Stock'));

			$this->exceldata->close($wb);
			$file = $this->htmlfile->pathfile($this->exceldata->get_filename($wb));
			$xls = $this->htmlfile->pathfile($this->lang->line('Documentos') . '.xls');
			copy($file, $xls);
			unlink($file);
			$this->zip->read_file($xls);

			// Stock contado
			$wb = $this->exceldata->create();
			$this->load->model('stocks/m_stockcontado');
			$data = $this->m_stockcontado->get();

			$data = array_chunk($data, $this->config->item('bp.max.excel.rows'));
			$ct = 1;
			foreach ($data as $d)
			{
				$this->exceldata->add($wb, $d, $this->lang->line('Stock Contado') . $ct);
				++$ct;
			}
			$this->exceldata->close($wb);
			$file = $this->htmlfile->pathfile($this->exceldata->get_filename($wb));
			$xls = $this->htmlfile->pathfile($this->lang->line('Stock Contado') . '.xls');
			copy($file, $xls);
			unlink($file);
			$this->zip->read_file($xls);

			#$this->exceldata->close($wb);
			#$file = $this->exceldata->get_filename($wb);
			#$this->zip->read_file($this->htmlfile->pathfile($file));
			$zipname = $this->lang->line('Retroceso') . '-' . time() . '.zip';
			$zip = DIR_TEMP_PATH . $zipname;
			$this->zip->archive($zip);
			$url = $this->htmlfile->url($zipname);
			#$xlsname = time() . '.xls';
			#$xls = DIR_TEMP_PATH . $xlsname;
			#copy($this->htmlfile->pathfile($file), $xls);
			#$url = $this->htmlfile->url($xlsname);
			$message = sprintf($this->lang->line('msg-stock-retrocedido-fichero-ok'), "<a href='" . $url . "'>{$zipname}</a>");
			#$message = sprintf($this->lang->line('msg-stock-retrocedido-fichero-ok'), "<a href='" . $url . "'>{$xlsname}</a>");
			// Envía un mensaje
			$this->load->library('Mensajes');
			$this->mensajes->usuario($this->userauth->get_username(), $message);
			$this->out->dialog(TRUE, $message);
			//$this->out->dialog(TRUE, $this->lang->line('msg-stock-retrocedido-ok'));
		}
	}

	/**
	 * Retrocede el stock
	 * @param int $task 0: Se ejecuta ahora, 1: se ejecuta como tarea
	 * @return DIALOG
	 */
	function retroceder($task = null)
	{
		$this->userauth->roleCheck($this->auth . '.retroceder');
		$task = isset($task)?$task:$this->input->get_post('task');

		if ($task === FALSE) $task = 1;

		if ($task == 1)
		{
			$this->load->library('tasks');
			$cmd = site_url("stocks/antiguedadstock/retroceder/0");
			$this->tasks->add2($this->lang->line('Retroceder stock'), $cmd);
		}
		else
		{
			set_time_limit(0);

			$this->reg->retroceder();

			$message = $this->lang->line('msg-stock-retrocedido-ok');
			// Envía un mensaje
			$this->load->library('Mensajes');
			#$this->mensajes->usuario($this->userauth->get_username(), $message);
			$this->out->dialog(TRUE, $message);
			//$this->out->dialog(TRUE, $this->lang->line('msg-stock-retrocedido-ok'));
		}
	}

	/**
	 * Retrocede el stock
	 * @param int $task 0: Se ejecuta ahora, 1: se ejecuta como tarea
	 * @return DIALOG
	 */
	function retroceder2($task = null)
	{
		$this->userauth->roleCheck($this->auth . '.retroceder');
		$task = isset($task)?$task:$this->input->get_post('task');

		if ($task === FALSE) $task = 1;

		if ($task == 1)
		{
			$this->load->library('tasks');
			$cmd = site_url("stocks/antiguedadstock/retroceder/0");
			$this->tasks->add2($this->lang->line('Retroceder stock'), $cmd);
		}
		else
		{
			set_time_limit(0);

			$this->reg->retroceder2();

			$message = $this->lang->line('msg-stock-retrocedido-ok');
			// Envía un mensaje
			$this->load->library('Mensajes');
			#$this->mensajes->usuario($this->userauth->get_username(), $message);
			$this->out->dialog(TRUE, $message);
			//$this->out->dialog(TRUE, $this->lang->line('msg-stock-retrocedido-ok'));
		}
	}

	/**
	 * Valorar el stock de los artículos en firme
	 * @return MSG
	 */
	function valorar()
	{
		#die();
		set_time_limit(0);
		$start = microtime(TRUE);
		$res = $this->reg->valorar();
		if ($res)
		{
			$this->load->library('Logger');
			#$time2 = date($time, )
			$this->logger->log("VALORACION STOCK en {$time} s ", 'stocks');
			if (!$this->reg->limpiar())
			{
				$this->out->error($this->reg->error_message());
			}
		}	
		$time = round((microtime(TRUE) - $start), 4);
		($res)?$this->out->success(sprintf($this->lang->line('antiguedadstock-valorar-ok'), $res, $time)):$this->out->error($this->reg->error_message());
	}

	/**
	 * Valorar el stock de los artículos en firme
	 * @return MSG
	 */
	function valorar2()
	{
		set_time_limit(0);
		$start = microtime(TRUE);
		$res = $this->reg->valorar2();
		$time = round((microtime(TRUE) - $start), 4);
		#var_dump($res, $time); die();
		if ($res !== FALSE)
		{
			$this->load->library('Logger');
			#$time2 = date($time, )
			$this->logger->log("VALORACION STOCK en {$time} s ", 'stocks');
		}
		($res)?$this->out->success(sprintf($this->lang->line('antiguedadstock-valorar-ok'), $res, $time)):$this->out->error($this->reg->error_message());
	}

	/**
	 * Limpia los stocks atrasados
	 * @return MSG
	 */
	function limpiar()
	{
		set_time_limit(0);
		($this->reg->limpiar())?$this->out->success($this->lang->line('antiguedadstock-limpiar-ok')):$this->out->error($this->reg->error_message());
	}
}

/* End of file Antiguedadstock.php */
/* Location: ./system/application/controllers/stocks/Antiguedadstock.php */