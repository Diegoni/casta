<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	ventas
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Controlador de las tarifas de envío
 *
 */
class TarifasEnvio extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return TarifasEnvio
	 */
	function __construct()
	{
		parent::__construct('ventas.tarifasenvio', 'ventas/M_tarifasenvio', TRUE, 'ventas/tarifasenvio.js', 'Tarifas de envio');
	}

	/**
	 * Calcula las tarifas de envío
	 *
	 * @param int $paisId Id del país
	 * @param int $regionId Id de la región
	 * @param int $peso Peso en gramos
	 * @param int $unidades Unidades a enviar
	 * @param int $pedido Id del pedido
	 * @return JSON array
	 */
	function get_tarifas($paisId = null, $regionId = null, $peso = null, $unidades = null, $pedido = null, $web = null)
	{

		$this->userauth->roleCheck(($this->auth . '.get'));

		$paisId		= isset($paisId)?$paisId:$this->input->get_post('paisId');
		$regionId	= isset($regionId)?$regionId:$this->input->get_post('regionId');
		$peso		= isset($peso)?$peso:$this->input->get_post('peso');
		$unidades	= isset($unidades)?$unidades:$this->input->get_post('unidades');
		$pedido		= isset($pedido)?$pedido:$this->input->get_post('pedido');
		$web		= isset($web)?$web:$this->input->get_post('web');
		if (empty($web)) $web = FALSE;

		if ($paisId && $regionId && ($peso || $unidades || $pedido))
		{
			if (is_numeric($pedido)) return $this->get_tarifaspedido($pedido, $web);
			if (is_numeric($unidades)) return $this->get_tarifasunidades($paisId, $regionId, $unidades, $web);
			if (is_numeric($peso)) return $this->get_tarifaspeso($paisId, $regionId, $peso, $web);
		}
		$this->out->noCache();
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Calcula las tarifas de envío según el peso
	 *
	 * @param int $paisId Id del país
	 * @param int $regionId Id de la región
	 * @param int $peso Peso en gramos
	 * @return JSON array
	 */
	function get_tarifaspeso($paisId = null, $regionId = null, $peso = null, $web = null )
	{
		$this->userauth->roleCheck(($this->auth . '.get'));

		$paisId		= isset($paisId)?$paisId:$this->input->get_post('paisId');
		$regionId	= isset($regionId)?$regionId:$this->input->get_post('regionId');
		$peso		= isset($peso)?$peso:$this->input->get_post('peso');
		$web		= isset($web)?$web:$this->input->get_post('web');
		
		if (empty($web)) $web = FALSE;
		
		if ($paisId && $regionId && ($peso || $unidades || $pedido))
		{

			if (is_numeric($paisId) && is_numeric($regionId) && is_numeric($peso))
			{
				$data = $this->reg->get_tarifas_peso($paisId, $regionId, $peso, $web);
				$this->out->data($data);
			}
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Calcula las tarifas de envío según unidades
	 *
	 * @param int $paisId Id del país
	 * @param int $regionId Id de la región
	 * @param int $unidades Unidades a enviar
	 * @return JSON array
	 */
	function get_tarifasunidades($paisId = null, $regionId = null, $unidades = null, $web = null )
	{
		$this->userauth->roleCheck(($this->auth . '.get'));
		$paisId		= isset($paisId)?$paisId:$this->input->get_post('paisId');
		$regionId	= isset($regionId)?$regionId:$this->input->get_post('regionId');
		$unidades	= isset($unidades)?$unidades:$this->input->get_post('unidades');
		$web		= isset($web)?$web:$this->input->get_post('web');
		
		if (empty($web)) $web = FALSE;

		if ($paisId && $regionId && $unidades)
		{

			if (is_numeric($paisId) && is_numeric($regionId) && is_numeric($unidades))
			{
				$grlibro = $this->config->item('bp_tarifasenvio_gramos_libro');
				$data = $this->reg->get_tarifas_peso($paisId, $regionId, $unidades * $grlibro, $web);
				$this->out->data($data);
			}
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Calcula las tarifas de envío según el pedido
	 *
	 * @param int $pedido Id del pedido
	 * @return JSON array
	 */
	function get_tarifaspedido( $pedido = null, $web = null )
	{
		$this->userauth->roleCheck(($this->auth . '.get'));

		$paisId		= isset($paisId)?$paisId:$this->input->get_post('paisId');
		$regionId	= isset($regionId)?$regionId:$this->input->get_post('regionId');
		$pedido		= isset($pedido)?$pedido:$this->input->get_post('pedido');
		$web		= isset($web)?$web:$this->input->get_post('web');
		if (empty($web)) $web = FALSE;

		if ($paisId && $regionId && ($peso || $unidades || $pedido))
		{

			$grlibro = $this->config->item('bp_tarifasenvio_gramos_libro');
			$data = $this->reg->get_tarifas_pedido($pedido, $grlibro, $web);
			$this->out->data($data);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Actualiza las tarifas de envío desde un archivo EXCEL
	 *
	 * @param int $modoenvio Id del modo de envío
	 * @param string $file Fichero EXCEL
	 */
	function set_tarifas($modoenvio = null, $file = null )
	{
		$this->userauth->roleCheck(($this->auth . '.set'));
		$modoenvio = isset($modoenvio)?$modoenvio:$this->input->get_post('modoenvio');
		$file = isset($file)?$file:$this->input->get_post('file');

		if (isset($file))
		{
			$destino = $this->config->item('bp_upload_path');
			$name = $file;
			$file = $destino . '/' . $file;
		}
		
		if (!isset($modoenvio))
		{
			$modoenvio = $this->input->get_post('modoenvio', null);
		}
		
		if (!isset($file) && (isset($_FILES['excelfile'])))
		{
			$destino = $this->config->item('bp_upload_path');
			$file = $destino . '/' . $_FILES ['excelfile'][ 'name'];
			$name = $_FILES [ 'excelfile' ][ 'name' ];
			move_uploaded_file ( $_FILES ['excelfile']['tmp_name'], $file);
		}
		
		//echo $this->out->message(FALSE, $file);
		$error = $this->reg->set_tarifas($modoenvio, $file);
		//unlink($file);
		if ($error === TRUE)
		{
			$message = sprintf($this->lang->line('tarifasenvio-set-tarifas-ok'), $name);
			$success = TRUE;
		}
		else
		{
			$message = $error;
			$success = FALSE;
		}
		$this->out->message($success, $message);
	}

	/**
	 * Muestra la ventana de pendientes de cerrar
	 * @return FORM
	 */
	function consultar()
	{
		$this->_show_form('get_list', 'ventas/vertarifas.js', $this->lang->line('Tarifas de envio'));
	}
	
	/**
	 * Actualiza las tarifas de envío en gramos desde un archivo EXCEL
	 *
	 * @param string $file Fichero EXCEL
	 */
	function set_tarifasgramos($file = null )
	{
		$this->userauth->roleCheck(($this->auth . '.set'));
		$file = isset($file)?$file:$this->input->get_post('file');
		if (empty($file))
		{
			$this->_show_js('set', 'main/excel.js', array('seccion' => FALSE, 'url' => 'ventas/tarifasenvio/set_tarifasgramos'));
		}

		if (isset($file))
		{
			$destino = $this->config->item('bp_upload_path');
			$name = $file;
			$file = $destino . '/' . $file;
		}
		
		$error = $this->reg->set_tarifas_gramos($file);
		unlink($file);
		if ($error === TRUE)
		{
			$this->out->success($this->lang->line('tarifasenvio-set-tarifas-gramos-ok'));
		}
		else
		{
			$this->out->error($error);
		}
	}

	/**
	 * Obtiene las tarifas de envío de un tipo dado
	 *
	 * @param int $tipo Id del tipo de envío
	 * @return JSON array
	 */
	function get_tarifastipo($tipo = null )
	{

		$this->userauth->roleCheck(($this->auth . '.get'));

		$tipo = isset($tipo)?$tipo:$this->input->get_post('tipo');

		if (is_numeric($tipo))
		{
			$data = $this->reg->get_tarifas($tipo);
			$this->out->data($data);
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Actualiza las tarifas de envío desde un archivo EXCEL
	 *
	 * @param int $modoenvio Id del modo de envío
	 * @param string $file Fichero EXCEL
	 */
	function set_tarifa($id = null, $kilo = null, $value = null)
	{
		$this->userauth->roleCheck(($this->auth . '.set'));
		
		$id 	= isset($id)?$id:$this->input->get_post('id');
		$kilo 	= isset($kilo)?$kilo:$this->input->get_post('kilo');
		$value 	= isset($value)?$value:$this->input->get_post('value');
		if (is_numeric($id))
		{
			if (!is_numeric($kilo) || !is_numeric($value))
			{
				// Se ha enviado como columna = valor
				for($i = 1; $i <= 20; ++$i)
				{
					$value = $this->input->get_post('fV' . $i);
					if (is_numeric($value))
					{
						$kilo = $i;
						break;
					}
				}
			}
			if (is_numeric($kilo) && is_numeric($value))
			{
				if (!$this->reg->update($id, array('fV' . $kilo => $value)))
				{
					$this->out->error($this->reg->error_message());					
				}
				$this->out->success(sprintf($this->lang->line('tarifasenvio-set-tarifas-ok'), $id));	
			}
		}

		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Clona un modo de envío en un nuevo modo de envío
	 *
	 * @param int $id Id del modo de envío
	 * @return DATA
	 */
	function clonar($id = null)
	{
		$this->userauth->roleCheck(($this->auth . '.clonar'));
		
		$id 	= isset($id)?$id:$this->input->get_post('id');
		if (is_numeric($id))
		{
			$this->load->model('ventas/m_modoenvio');
			$tf = $this->m_modoenvio->load($id);
			unset($tf['nIdTipo']);
			$tf['cNombre'] .= $this->lang->line('tarifaenvio-copia');
			$this->db->trans_begin();
			$id_n = $this->m_modoenvio->insert($tf);
			if ($id_n < 0)
			{
				$this->db->trans_rollback();
				$this->out->error($this->m_modoenvio->error_message());									
			}
			if (!$this->reg->clonar($id, $id_n))
			{
				$this->db->trans_rollback();
				$this->out->error($this->reg->error_message());					
			}
			$this->db->trans_commit();
			$res = array(
				'success' => TRUE,
				'message' => sprintf($this->lang->line('tarifasenvio-clonar-ok'), $tf['cNombre']),
				'id' => $id_n
			);
			$this->out->send($res);	
		}

		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}
}

/* End of file tarifasenvio.php */
/* Location: ./system/application/controllers/ventas/tarifasenvio.php */
