<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	catalogo
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Movimientos de sección
 *
 */
class Movimiento extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Movimiento
	 */
	function __construct()
	{
		parent::__construct('catalogo.movimiento', 'catalogo/M_movimiento', TRUE, 'catalogo/movimiento.js');
	}

	/**
	 * Mueve un artículo desde una sección a otra. Si hay firme y depósito se mueve primero el firme
	 * @param int $id Id del artículo
	 * @param int $ido Id de la sección de origen
	 * @param int $idd Id de la sección de destino
	 * @param int $cantidad Cantidad a mover
	 * @return MSG
	 */
	function mover($id = null, $ido = null, $idd = null, $cantidad = null)
	{
		$this->userauth->roleCheck($this->auth .'.add');

		$id 	= isset($id)?$id:$this->input->get_post('id');
		$ido 	= isset($ido)?$ido:$this->input->get_post('ido');
		$idd 	= isset($idd)?$idd:$this->input->get_post('idd');
		$cantidad	= isset($cantidad)?$cantidad:$this->input->get_post('cantidad');

		if (is_numeric($id) && is_numeric($ido) && is_numeric($idd))
		{

			$id_n = $this->reg->mover($id, $ido, $idd, $cantidad);
			#var_dump($id_n); die();
			if (!$id_n)
			{
				$this->out->error($this->reg->error_message());
			}
			$this->out->success(sprintf($this->lang->line('registro_generado'), $id_n));
		}
		$this->out->error($this->lang->line('mensaje_faltan_datos'));
	}

	/**
	 * Consulta los movimientos de stock
	 * @param int $ido Id de la sección de origen
	 * @param int $idd Id de la sección destiono
	 * @param date $desde Fecha inicial de los movimientos
	 * @param dara $hasta Fecha final de los movimientos
	 * @return HTML
	 */
	function consultar($ido = null, $idd = null, $desde = null, $hasta = null)
	{
		$this->userauth->roleCheck($this->auth . '.get_list');

		$ido 	= isset($ido)?$ido:$this->input->get_post('ido');
		$idd 	= isset($idd)?$idd:$this->input->get_post('idd');
		$desde	= isset($desde)?$desde:$this->input->get_post('desde');
		$hasta	= isset($hasta)?$hasta:$this->input->get_post('hasta');

		if (is_numeric($ido) || is_numeric($idd) || $desde !== FALSE || $hasta !== FALSE)
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
			if (is_numeric($ido)) $where[] = "nIdSeccionOrigen = {$ido}";
			if (is_numeric($idd)) $where[] = "nIdSeccionDestino = {$idd}";
			$where = implode(' AND ', $where);
			$data = $this->reg->get(null, null, null, null, $where);
			if (count($data) == 0)
			{
				$this->out->success($this->lang->line('no-hay-documentos'));
			}

			$data['movimientos'] = $data;

			$message = $this->load->view('catalogo/movimientos', $data, TRUE);
			$this->out->html_file($message, 'Consultar movimientos sección', 'iconoReportTab');
		}
		else
		{
			$this->_show_js('get_list', 'catalogo/buscararmovimientos.js');

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

		// Stock actual
		$this->load->model('catalogo/m_articuloseccion');
		$data = $this->m_articuloseccion->load($id);
		$firme = (is_numeric($firme))?$firme: $data['nStockFirme'];
		$deposito = (is_numeric($deposito))?$deposito : $data['nStockDeposito'];

		// Diferencias
		$df = $data['nStockFirme'] - $firme;
		$dd = $data['nStockDeposito'] - $deposito;
		if ($df == 0 && $dd == 0) $this->out->success($this->lang->line('regulacion-no-diff'));
		if (($df < 0 || $dd < 0) && $motivomas < 1) $this->out->error($this->lang->line('regulacion-no-motivomenos'));
		if (($df > 0 || $dd > 0) && $motivomenos < 1) $this->out->error($this->lang->line('regulacion-no-motivomas'));

		// Coste
		$this->load->model('catalogo/m_articulo');
		$art = $this->m_articulo->load($data['nIdLibro']);
			
		// Crea los movimientos
		$this->db->trans_begin();
		$reg['nIdSeccion'] = $data['nIdSeccion'];
		$reg['nIdLibro'] = $data['nIdLibro'];
		$reg['fCoste'] = $art['fPrecioCompra'];
		if ($df < 0)
		{
			$reg['nIdMotivo'] = $motivomas;
			$reg['nCantidadFirme'] = -$df;
			$reg['nCantidadDeposito'] = 0;
			$idr = $this->reg->insert($reg);
			if ($idr < 0)
			{
				$this->db->trans_rollback();
				$this->out->error($this->reg->error_message());
			}
		}
		if ($df > 0)
		{
			$reg['nIdMotivo'] = $motivomenos;
			$reg['nCantidadFirme'] = $df;
			$reg['nCantidadDeposito'] = 0;
			$idr = $this->reg->insert($reg);
			if ($idr < 0)
			{
				$this->db->trans_rollback();
				$this->out->error($this->reg->error_message());
			}
		}
		if ($dd < 0)
		{
			$reg['nIdMotivo'] = $motivomas;
			$reg['nCantidadFirme'] = 0;
			$reg['nCantidadDeposito'] = -$dd;
			$idr = $this->reg->insert($reg);
			if ($idr < 0)
			{
				$this->db->trans_rollback();
				$this->out->error($this->reg->error_message());
			}
		}
		if ($dd > 0)
		{
			$reg['nIdMotivo'] = $motivomenos;
			$reg['nCantidadFirme'] = 0;
			$reg['nCantidadDeposito'] = $dd;
			$idr = $this->reg->insert($reg);
			if ($idr < 0)
			{
				$this->db->trans_rollback();
				$this->out->error($this->reg->error_message());
			}
		}

		// Actualiza el stock
		$upd['nStockFirme'] = $data['nStockFirme'] - $df;
		$upd['nStockDeposito'] = $data['nStockDeposito'] - $dd;
		if (!$this->m_articuloseccion->update($id, $upd))
		{
			$this->db->trans_rollback();
			$this->out->error($this->m_articuloseccion->error_message);
		}
		$this->db->trans_commit();

		$msg = '';
		if ($df != 0)
		{
			$msg .= "Firme: {$data['nStockFirme']} -> {$upd['nStockFirme']}";
		}
		if ($dd != 0)
		{
			$msg .= " Depósito: {$data['nStockDeposito']} -> {$upd['nStockDeposito']}";
		}
		$this->out->success($msg);
	}

	/**
	 * Mueve un artículo desde una sección a otra. Si hay firme y depósito se mueve primero el firme
	 * @param int $id Id del artículo
	 * @param int $ido Id de la sección de origen
	 * @param int $idd Id de la sección de destino
	 * @param int $cantidad Cantidad a mover
	 * @return MSG
	 */
	function todo($ido = null, $idd = null)
	{
		$this->userauth->roleCheck($this->auth .'.todo');

		$ido 	= isset($ido)?$ido:$this->input->get_post('ido');
		$idd 	= isset($idd)?$idd:$this->input->get_post('idd');

		if (is_numeric($ido) && is_numeric($idd))
		{
			$this->load->model('catalogo/m_articuloseccion');
			$data = $this->m_articuloseccion->get(null, null, null, null,  "nIdSeccion={$ido} AND nStockFirme + nStockDeposito > 0");
			if (count($data) == 0)
			{
				$this->out->success($this->lang->line('no-hay-articulos-mover'));
			}
			$this->db->trans_begin();
			$count = 0;
			$count2 = 0;
			foreach ($data as $reg)
			{
				if (!$this->reg->mover($reg['nIdLibro'], $ido, $idd, $reg['nStockFirme'] + $reg['nStockDeposito']))
				{
					$this->db->trans_rollback();
					$this->out->error($this->reg->error_message());
				}
				++$count;
				$count2 += $reg['nStockFirme'] + $reg['nStockDeposito'];
			}
			$this->db->trans_commit();
			$this->out->success(sprintf($this->lang->line('mover-libros-todo-ok'), $count, $count2));

		}
		$this->_show_js('todo', 'catalogo/movertodo.js');
	}

}

/* End of file Movimiento.php */
/* Location: ./system/application/controllers/stocks/Movimiento.php */