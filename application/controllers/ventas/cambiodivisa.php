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
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Cambio de divisas
 *
 */
class CambioDivisa extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return CambioDivisa
	 */
	function __construct()
	{
		parent::__construct('ventas', null, TRUE, null, 'Cambio Divisa');
	}

	/**
	 * Calcula el cambio entre dos divisas, pasando siempre por la divisa por defecto
	 * @param int $divisa1 ID divisa 1
	 * @param float $precio Importe
	 * @param int $divisa2 ID divisa 2
	 */
	function cambio($divisa1 = null, $precio = null, $divisa2 = null)
	{
		$this->userauth->roleCheck($this->auth .'.cambiodivisa');

		$divisa1	= isset($divisa1)?$divisa1:$this->input->get_post('divisa1');
		$divisa2	= isset($divisa2)?$divisa2:$this->input->get_post('divisa2');
		$precio		= isset($precio)?$precio:$this->input->get_post('precio');
		if ($divisa1 && $divisa2 && $precio)
		{
			$this->load->library('Tarifas');
			$data['importe1'] = $precio;
			$cambio = $this->tarifas->cambiar($precio, $divisa1, $divisa2);
			$data['importe2'] = $cambio['importe'];
			$data['cambio'] = $cambio['cambio'];
			$data['divisa1'] = $cambio['divisa1'];
			$data['divisa2'] = $cambio['divisa2'];
				
			$message = $this->load->view('ventas/cambiodivisa', $data, TRUE);
			#echo $message; die();

			$this->out->html_file($message, $this->lang->line('Cambio Divisa'), 'iconoReportTab');
		}
		elseif ($divisa1 || $precio || $divisa2 )
		{
			$this->out->error($this->lang->line('mensaje_faltan_datos'));
		}
		else
		{
			$this->userauth->roleCheck($this->auth .'.cambiodivisa');
			$this->_show_js('cambiodivisa', 'ventas/cambiodivisa.js');
		}
	}

	/**
	 * Calcula las tarifas de venta de un artículo
	 * @param float $precio Precio original en divisa
	 * @param int $divisa ID de la divisa
	 * @param float $portes Importe de los portes
	 * @param float $dto Descuento (en %)
	 * @param int $tipo ID del tipo de artículo
	 */
	function tarifas($precio = null, $divisa = null, $portes = null, $dto = null, $tipo = null)
	{
		$this->userauth->roleCheck($this->auth .'.cambiodivisa');

		$divisa		= isset($divisa)?$divisa:$this->input->get_post('divisa');
		$precio		= isset($precio)?$precio:$this->input->get_post('precio');
		$portes		= isset($portes)?$portes:$this->input->get_post('portes');
		$dto		= isset($dto)?$dto:$this->input->get_post('dto');
		$tipo		= isset($tipo)?$tipo:$this->input->get_post('tipo');

		if ($divisa && $precio && $tipo)
		{
			$portes = (float)format_tofloat($portes);
			#$this->load->model('generico/m_divisa');
			$this->load->library('Tarifas');

			$tarifas = $this->tarifas->get_tarifas($precio, $divisa, $dto, $portes, $tipo);

			$tarifas['importe_divisa'] = $precio;
			$tarifas['portes'] = $portes;
			$tarifas['dto'] = $dto;
			//print '<pre>'; print_r($tarifas); print '</pre>'; die();
				
			$message = $this->load->view('ventas/tarifas', $tarifas, TRUE);
			$res = array(
				'success' => TRUE,
				'info' => $message
				);
			$this->out->send($res);
			//echo $message; return;

			$this->out->html_file($message, $this->lang->line('Cálculo Tarifas'), 'iconoReportTab');
		}
		elseif ($divisa || $precio || $tipo)
		{
			$this->out->error($this->lang->line('mensaje_faltan_datos'));
		}
		else
		{
			$this->userauth->roleCheck($this->auth .'.cambiodivisa');
			$this->_show_form('cambiodivisa', 'ventas/tarifas2.js', $this->lang->line('Cálculo Tarifas'));
		}
	}
}

/* End of file cambiodivisa.php */
/* Location: ./system/application/controllers/ventas/cambiodivisa.php */