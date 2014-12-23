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
 * Códigos de barra
 *
 */
class Codebar extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Codebar
	 */
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Ejecuta de nuevo el comando indicado
	 * @param int $id Id del comando a ejecutar
	 * @return JSON
	 */
	function out($code = null, $height = null, $type = null)
	{
		$code = isset($code)?$code:$this->input->get_post('code');
		$type = isset($type)?$type:$this->input->get_post('type');
		$height = isset($height)?$height:$this->input->get_post('height');
		if (!empty($code))
		{
			$this->load->library('CodebarLib');
			if (!is_numeric($type)) $type = $this->obj->config->item('codebar.default');
			$this->codebarlib->out($code, $type, is_numeric($height)?$height:null);
			return;
		}
	}

	/**
	 * Genera la etiqueta del paquete
	 * @param int $id Id del envío
	 * @return MSG
	 */
	function etiqueta($idetq = null)
	{
		$idetq = isset($idetq)?$idetq:$this->input->get_post('idetq');
		
		if (!empty($idetq))
		{
			$this->load->library('ASM');

			$res = $this->asm->etiqueta($idetq);
			if (!$res)
				$this->out->error($this->asm->get_error());

			$this->load->library('HtmlFile');
			$url = $this->htmlfile->url($res);

			$this->out->url($url, $this->lang->line('Enviar por courier'), 'iconoCourierTab');
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Listado de envíos Courier por fechas
	 * @param  date $fecha1 Fecha inicio
	 * @param  date $fecha2 Fecha final
	 * @return HTML
	 */
	function envios($fecha1 = null, $fecha2 = null)
	{
		$fecha1		= isset($fecha1)?$fecha1:$this->input->get_post('fecha1');
		$fecha2		= isset($fecha2)?$fecha2:$this->input->get_post('fecha2');

		if ($fecha1 && $fecha2)
		{
			$this->load->model('generico/m_nota');
			$fecha1 = to_date($fecha1);
			$fecha2 = to_date($fecha2);
			$f1 = $fecha1;
			$f2 = $fecha2;
			$fecha1 = format_mssql_date($fecha1);
			$fecha2 = format_mssql_date($fecha2);
			$text = $this->lang->line('pedidocliente-courier-search');
			$envios = $this->m_nota->get(null, null, 'dCreacion', null, "tObservacion LIKE '{$text}' AND dCreacion > {$fecha1} AND dCreacion <= " . $this->db->dateadd('d', 1, $fecha2));
			$data['envios'] = $envios;
			$data['desde'] = $f1;
			$data['hasta'] = $f2;
			$body = $this->load->view('sys/courierlist', $data, TRUE);
			$this->out->html_file($body, $this->lang->line('Envios Courier'), 'iconoCourierTab');
		}
		else
		{
			$data['url'] = site_url('sys/codebar/envios');
			$data['title'] = $this->lang->line('Envios Courier');
			$this->_show_js(null, 'oltp/ventassiniva.js', $data);
		}
	}
}
/* End of file codebar.php */
/* Location: ./system/application/controllers/sys/codebar.php */