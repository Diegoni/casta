<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	suscripciones
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * OLTP de suscripciones
 *
 */
class Oltpsuscripcion extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Oltpsuscripcion
	 */
	function __construct()
	{
		parent::__construct('suscripciones', 'suscripciones/M_oltpsuscripcion', TRUE);
	}

	/**
	 * Consulta el estado de las compras y devoluciones a una fecha dada
	 * @return HTML
	 */
	function corte_suscripciones_index()
	{
		$this->_show_form('corte', 'suscripciones/suscripcionescorte.js', $this->lang->line('Corte operaciones suscripciones'));
	}

	/**
	 * Actualiza el corte de operaciones - TAREA
	 * @return JSON
	 */
	function update_corte()
	{
		$this->userauth->roleCheck(($this->auth.'.upd_corte'));
		$this->load->library('tasks');
		$runner = $this->userauth->get_username();
		$cmd = site_url("suscripciones/oltpsuscripcion/update_corte_task/{$runner}");
		$id_task = $this->tasks->add($this->lang->line('suscripciones-task-send'), $cmd);
		$message = sprintf($this->lang->line('mailing-task-cola'), $id_task);
		$success = TRUE;

		// Respuesta
		echo $this->out->message($success, $message);
	}

	/**
	 * Actualiza el corte de operaciones - RUN
	 * @return JSON
	 */
	function update_corte_task($runner = null)
	{
		$this->userauth->roleCheck(($this->auth.'.upd_corte'));
		set_time_limit(0);
		$this->_checkdberror($this->reg->crear_corte());
		$message = $this->lang->line('corte-suscripciones-ok');
		// Respuesta
		if (isset($runner))
		{
			// Envía un mensaje
			//$msg = $this->out->message($success, $message, FALSE);
			$msg = $message;
			$this->load->library('Mensajes');
			$this->userauth->set_username();
			$this->mensajes->usuario($runner, $msg);
			echo $message;
			exit;
		}
		$this->out->success($message);
	}

	/**
	 * Busca las ventas a una fecha en las que aún no había entrado la compra
	 * @param date $fecha Fecha de corte
	 * @return JSON
	 */
	function ventas_anticipadas($fecha = null)
	{
		$this->userauth->roleCheck(($this->auth.'.corte'));

		$fecha	= isset($fecha)?$fecha:$this->input->get_post('fecha');
		if (isset($fecha))
		{
			$fecha = to_date($fecha);

			set_time_limit(0);

			$data = $this->reg->get_ventas_anticipadas($fecha);

			$res = array(
				'total_data' => $this->reg->get_count(),
				'value_data' => $data
			);
			// Respuesta
			echo $this->out->send($res);
		}

	}

	/**
	 * Busca las compras a una fecha en las que las ventas se han realizado posterior a esa fecha
	 * @param date $fecha Fecha de corte
	 * @return JSON
	 */
	function compras_anticipadas($fecha = null)
	{
		$this->userauth->roleCheck(($this->auth.'.corte'));

		$fecha	= isset($fecha)?$fecha:$this->input->get_post('fecha');
		if (isset($fecha))
		{
			$fecha = to_date($fecha);

			set_time_limit(0);

			$data = $this->reg->get_compras_anticipadas($fecha);
			$res = array(
				'total_data' => $this->reg->get_count(),
				'value_data' => $data
			);
			// Respuesta
			echo $this->out->send($res);
		}
		//$this->_checkdberror(($valores = $this->reg->get_compras_anticipadas($fecha)));
	}

	/**
	 * Busca las compras realizadas a una fecha en las que no existen ventas
	 * @param date $fecha Fecha de corte
	 * @return JSON
	 */
	function compras_sin_venta($fecha = null)
	{
		$this->userauth->roleCheck(($this->auth.'.corte'));

		$fecha	= isset($fecha)?$fecha:$this->input->get_post('fecha');
		if (isset($fecha))
		{
			$fecha = to_date($fecha);

			set_time_limit(0);

			$data = $this->reg->get_compras_sin_venta($fecha);
			$res = array(
				'total_data' => $this->reg->get_count(),
				'value_data' => $data
			);
			// Respuesta
			echo $this->out->send($res);
		}
	}

	/**
	 * Marca una factura como procesada
	 * @param $ids Array de Ids Factura_Ids suscripciones
	 * @return JSON
	 */
	function del_venta($ids = null)
	{
		$this->userauth->roleCheck($this->auth.'.del_corte');

		$ids 	= isset($ids)?$ids:$this->input->get_post('ids');

		if ($ids)
		{
			if (is_string($ids)) $ids = preg_split('/\;/', $ids);
			set_time_limit(0);
			$contador = $this->reg->del_venta($ids);
			$res = TRUE;
		}
		else
		{
			$res = sprintf($this->lang->line('mensaje_faltan_datos'));
		}

		if ($res === TRUE)
		{
			$this->out->success(sprintf($this->lang->line('ventas-marcadas'), $contador));
		}
		else
		{
			$this->out->error($res);
		}
		// Respuesta
		echo $this->out->send($ajax_res);
	}

	/**
	 * Marca un albaán como procesado
	 * @param $ids Array de Ids Albarán_Ids suscripciones
	 * @return JSON
	 */
	function del_compra($ids = null)
	{
		$this->userauth->roleCheck($this->auth.'.del_corte');

		$ids 	= isset($ids)?$ids:$this->input->get_post('ids');

		if ($ids)
		{
			if (is_string($ids)) $ids = preg_split('/\;/', $ids);
			set_time_limit(0);
			$contador = $this->reg->del_compra($ids);
			$this->out->success(sprintf($this->lang->line('compras-marcadas'), $contador));
		}
		else
		{
			$this->out->error(sprintf($this->lang->line('mensaje_faltan_datos')));
		}
	}

	function estadoavisos($id = null)
	{
		$this->userauth->roleCheck($this->auth .'.get_list');

		$idl		= isset($idl)?$idl:$this->input->get_post('idl');
		$fecha1 	= isset($fecha1)?$fecha1:$this->input->get_post('fecha1');
		$fecha2 	= isset($fecha2)?$fecha2:$this->input->get_post('fecha2');
		$ids		= isset($ids)?$ids:$this->input->get_post('ids');
		$tipo		= isset($tipo)?$tipo:$this->input->get_post('tipo');
		if (!empty($tipo))
		{
			$tipo = preg_split("/[,\|\s]/", $tipo);
		}
		if ($tipo === FALSE) $tipo = TRUE;

		if (!empty($idl) && !empty($fecha1) && !empty($fecha2))
		{
			$art = $this->reg->load($idl);
			$fecha1 = to_date($fecha1);
			$fecha2 = to_date($fecha2);
			//print "{$fecha1} - {$fecha2}\n"; die();
			//var_dump($tipo); die();
			$docs = $this->reg->get_documentos($idl, $fecha1, $fecha2, $ids, $tipo);
			if (count($docs)>0)	sksort($docs, 'dFecha');
			$data['articulo'] = $art;
			$data['docs'] = $docs;
			$data['fecha1'] = $fecha1;
			$data['fecha2'] = $fecha2;
			$message = $this->load->view('catalogo/documentos', $data, TRUE);
			$this->out->html_file($message, sprintf($this->lang->line('documentos_articulo'), $idl), 'iconoReportTab');
			return;
		}
		else
		{
			$data['url'] = site_url('catalogo/articulo/documentos');
			$data['title'] = $this->lang->line('documentos_articulo_form');
			if (!empty($idl)) $data['idl'] = $idl;

			$this->_show_js('get_list', 'catalogo/documentosasticulo.js', $data);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Facturas de suscripciones entre fechas 
	 * @param date $fecha1 Desde
	 * @param date $fecha2 Hasta
	 */
	function facturas($fecha1 = null, $fecha2 = null)
	{
		$this->userauth->roleCheck($this->auth .'.suscripcion.get_list');

		$fecha1		= isset($fecha1)?$fecha1:$this->input->get_post('fecha1');
		$fecha2		= isset($fecha2)?$fecha2:$this->input->get_post('fecha2');

		if (!empty($fecha1) && !empty($fecha2))
		{

			$fecha1 = to_date($fecha1);
			$fecha2 = to_date($fecha2);

			$data['fecha1'] = $fecha1;
			$data['fecha2'] = $fecha2;
			$data['valores'] = $this->reg->facturas($fecha1, $fecha2);

			$body = $this->load->view('suscripciones/facturas', $data, TRUE);
			$this->out->html_file($body, $this->lang->line('Facturas suscripciones'), 'iconoReportTab');
		}
		else
		{
			$data['title'] = $this->lang->line('Facturas suscripciones');
			$data['url'] = site_url('suscripciones/oltpsuscripcion/facturas');
			$this->_show_js('suscripcion.get_list', 'oltp/ventassiniva.js', $data);
		}
	}

}

/* End of file oltpsuscripcion.php */
/* Location: ./system/application/controllers/oltpsuscripcion.php */
