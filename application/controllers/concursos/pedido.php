<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	concursos
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * pedidos
 *
 */
class Pedido extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return Pedido
	 */
	function __construct()
	{
		parent::__construct('concursos.pedido', 'concursos/M_pedido', TRUE, null, 'Pedidos');
	}

	/**
	 * Devuelve los pedidos de un cliente
	 * @param int $id Id del cliente
	 * @return JSON
	 */
	function get_by_cliente($id = null)
	{
		$id = isset($id) ? $id : $this->input->get_post('id');

		$res = $this->reg->get_by_cliente($id);
		$this->out->success($res);
	}

	/**
	 * Estado de los pedidos
	 * @param int $id Id del cliente
	 */
	function status($id = null)
	{
		$id = isset($id) ? $id : $this->input->get_post('id');
		$res = $this->get_data();
		if(is_numeric($id))
		{
			$valores = null;
			foreach($res as $k => $v)
			{
				if($v['nIdCliente'] == $id)
					$valores = $res[$k];
			}
			$res = $valores;
		}

		$this->out->success($res);
	}

	/**
	 * Albaranes devueltos
	 * @param int $id Id del cliente
	 * @return array 'n' -> Importe narrativa, 'g' -> Importe general
	 */
	private function _devuelto($id)
	{
		$this->load->model('ventas/m_albaransalida');
		$total['n'][$id] = 0;
		$total['g'][$id] = 0;
		$regs = $this->m_albaransalida->get(null, null, null, null, "nIdCliente = {$id} AND ISNULL(bMostrarWeb, 1) = 1");
		foreach($regs as $reg)
		{
			$al = $this->m_albaransalida->load($reg['nIdAlbaran']);
			$tipo = ($al['cRefInterna'] == 'g') ? 'g' : 'n';
			$importes = $this->m_albaransalida->importes($reg['nIdAlbaran']);
			$total[$tipo][$id] += $importes['totales']['fTotal'];
		}

		return $total;
	}

	/**
	 * Estado del pedido
	 * @return array
	 */
	private function get_data()
	{
		$status['albaran'] = $this->reg->enalbaran();
		$status['pendiente'] = $this->reg->pendiente();
		$status['acatalogar'] = $this->reg->enestado(2);
		$status['catalogado'] = $this->reg->enestado(17);
		$idgrupo = $this->config->item('bp.concursos.idgrupo');
		$this->load->model('clientes/m_cliente');
		set_time_limit(0);
		$r = $this->m_cliente->get(null, null, 'cEmpresa', null, 'Cli_Clientes.nIdGrupoCliente=' . $idgrupo);
		foreach($r as $k => $reg)
		{
			$total = $this->_devuelto($reg['nIdCliente']);

			$r[$k]['fAbonoN'] = $total['n'][$reg['nIdCliente']];
			$r[$k]['fAbonoG'] = $total['g'][$reg['nIdCliente']];
			$r[$k]['fAlbaranN'] = isset($status['albaran']['n'][$reg['nIdCliente']]) ? $status['albaran']['n'][$reg['nIdCliente']] : 0;
			$r[$k]['fAlbaranG'] = isset($status['albaran']['g'][$reg['nIdCliente']]) ? $status['albaran']['g'][$reg['nIdCliente']] : 0;
			$r[$k]['fPendienteN'] = isset($status['pendiente']['n'][$reg['nIdCliente']]) ? $status['pendiente']['n'][$reg['nIdCliente']] : 0;
			$r[$k]['fPendienteG'] = isset($status['pendiente']['g'][$reg['nIdCliente']]) ? $status['pendiente']['g'][$reg['nIdCliente']] : 0;
			$r[$k]['fACatalogarN'] = isset($status['acatalogar']['n'][$reg['nIdCliente']]) ? $status['acatalogar']['n'][$reg['nIdCliente']] : 0;
			$r[$k]['fACatalogarG'] = isset($status['acatalogar']['g'][$reg['nIdCliente']]) ? $status['acatalogar']['g'][$reg['nIdCliente']] : 0;
			$r[$k]['fCatalogadoN'] = isset($status['catalogado']['n'][$reg['nIdCliente']]) ? $status['catalogado']['n'][$reg['nIdCliente']] : 0;
			$r[$k]['fCatalogadoG'] = isset($status['catalogado']['g'][$reg['nIdCliente']]) ? $status['catalogado']['g'][$reg['nIdCliente']] : 0;
		}
		return $r;
	}

	/**
	 * Estado del pedido
	 * @return HTML_FILE
	 */
	function estado()
	{
		$r = $this->get_data();
		$message = $this->load->view('concursos/estado', array('datos' => $r), TRUE);
		$this->out->html_file($message, $this->lang->line('Estado del pedido'), 'iconoReportTab');
	}

	/**
	 * Libros a catalogar
	 * @return HTML_FILE
	 */
	function acatalogar()
	{
		$acatalogar = $this->reg->acatalogar();
		$albaran = $this->reg->enalbaran();
		foreach($r as $k => $reg)
		{
			$total = $this->_devuelto($reg['nIdCliente']);
			$r[$k]['fAbonoN'] = $total['n'];
			$r[$k]['fAbonoG'] = $total['g'];
			if(strpos($reg['cBiblioteca'], 'GENERAL') > 0)
			{
				$r[$k]['fAlbaran'] = isset($albaran['g'][$reg['nIdCliente']]) ? $albaran['g'][$reg['nIdCliente']] : 0;
				$r[$k]['fAlbaran'] -= $total['g'];
			}
			else
			{
				$r[$k]['fAlbaran'] = isset($albaran['n'][$reg['nIdCliente']]) ? $albaran['n'][$reg['nIdCliente']] : 0;
				$r[$k]['fAlbaran'] -= $total['n'];
			}
		}

		$message = $this->load->view('concursos/acatalogar', array('datos' => $status), TRUE);
		echo $message;
		#$this->out->html_file($message, $this->lang->line('Libros a catalogar'),
		# 'iconoReportTab');
	}

}

/* End of file pedido.php */
/* Location: ./system/application/controllers/concursos/pedido.php */
