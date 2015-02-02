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
 * Regulación de stock
 *
 */
class Arreglostock extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Arreglostock
	 */
	function __construct()
	{
		parent::__construct('stocks.arreglostock', 'stocks/M_arreglostock', TRUE, 'stocks/arreglostock.js');
	}

	/**
	 * Consulta los ajustes de stock en un periodo dado  
	 * @param int $idt Id del Tipo de ajuste
	 * @param int $idt2 Id del Tipo de ajuste segundo
	 * @param date $desde Fecha inicio
	 * @param date $hasta Fecha final
	 * @param int $cantidad Cantidad mínima de la regulación
	 * @return HTML_FILE
	 */
	function consultar($idt = null, $idt2 = null, $desde = null, $hasta = null, $cantidad = null)
	{
		$this->userauth->roleCheck($this->auth . '.get_list');

		$idt 	= isset($idt)?$idt:$this->input->get_post('idt');
		$idt2 	= isset($idt2)?$idt2:$this->input->get_post('idt2');
		$desde	= isset($desde)?$desde:$this->input->get_post('desde');
		$hasta	= isset($hasta)?$hasta:$this->input->get_post('hasta');
		$cantidad = isset($cantidad)?$cantidad:$this->input->get_post('cantidad');

		if (is_numeric($idt) || $desde !== FALSE || $hasta !== FALSE)
		{
			$where = array();
			if ($desde !== FALSE)
			{
				$desde = format_mssql_date(to_date(($desde)));
				$where[] = "dCreacion >= {$desde}";
			}
			if ($hasta !== FALSE)
			{
				$hasta = format_mssql_date(to_date(($hasta)));
				$where[] = "dCreacion < " . $this->db->dateadd('d', 1, $hasta);
			}

			$tipos = array();			
			if (is_numeric($idt)) $tipos[] = $idt;
			if (is_numeric($idt2)) $tipos[] = $idt2;
			if (count($tipos)>0) $where[] = 'nIdMotivo IN (' . implode(',', $tipos) . ')';
			if (is_numeric($cantidad)) $where[] = "(nCantidadFirme + nCantidadDeposito) >= {$cantidad}";
			$where = implode(' AND ', $where);
			$order = (count($tipos) == 2)?'cSeccion, cTitulo, nCantidadFirme DESC, nCantidadDeposito DESC':'cSeccion, nCantidadFirme DESC, nCantidadDeposito DESC';
			$data = $this->reg->get(null, null, $order, null, $where);
			if (count($data) == 0)
			{
				$this->out->success($this->lang->line('no-hay-documentos'));
			}
			
			$data2 = array();
			foreach($data as $d)
			{
				$data2[$d['cSeccion']][] = $d;
			}
			/*foreach($data2 as $k => $v)
			 {
				sksort($data2[$k], 'nCantidadFirme', FALSE);
				}*/
			#asort($data2);
			$data['secciones'] = $data2;
				
			$message = $this->load->view('stocks/ajustes', $data, TRUE);
			$this->out->html_file($message, 'Ajustes de stock', 'iconoReportTab');
		}
		else
		{
			$this->_show_js('get_list', 'stocks/buscararreglos.js');
				
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Ajusta el stock
	 * @param int $id Id del artículo-sección
	 * @param int $firme Stock en firme real
	 * @param int $deposito Stock en depósito real
	 * @param int $motivomas Id del motivo de la regulación si positivo
	 * @param int $motivomenos Id del motivo de la regulación si negativo
	 */
	function arreglar($id = null, $firme = null, $deposito = null, $motivomas = null, $motivomenos = null)
	{
		$this->userauth->roleCheck($this->auth . '.add');

		$id = isset($id)?$id:$this->input->get_post('id');
		$firme = isset($firme)?$firme:$this->input->get_post('firme');
		$deposito = isset($deposito)?$deposito:$this->input->get_post('deposito');
		$motivomas = isset($motivomas)?$motivomas:$this->input->get_post('motivomas');
		$motivomenos = isset($motivomenos)?$motivomenos:$this->input->get_post('motivomenos');

		$res = $this->reg->arreglar($id, $firme, $deposito, $motivomas, $motivomenos);
		if ($res === FALSE)
			$this->out->error($this->reg->error_message());
		$msg = '';
		if ($res['df'] != 0)
		{
			$msg .= sprintf($this->lang->line('arreglostock-firme'), $res['fold'], $res['fnew']);
		}
		if ($res['dd'] != 0)
		{
			$msg .= sprintf($this->lang->line('arreglostock-deposito'), $res['dold'], $res['dnew']);
		}
		$this->out->success($msg);
	}
}

/* End of file Arreglostock.php */
/* Location: ./system/application/controllers/stocks/Arreglostock.php */