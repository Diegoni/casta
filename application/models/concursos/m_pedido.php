<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	concursos
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Pedidos Concurso
 *
 */
class M_pedido extends MY_Model
{
	/**
	 * Prefijo de la base de datos
	 * @var string
	 */
	var $prefix = '';

	/**
	 * Constructor
	 * @return M_Pedido
	 */
	function __construct()
	{
		$data_model = array(
			'cPedido'		=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE),
			'dEntrada'		=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATE, DATA_MODEL_DEFAULT => TRUE),		
			'nBiblioteca'	=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'concursos/biblioteca2/search')),
			'nSala'			=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'concursos/sala2/search')),
		);

		//CI
		$obj =& get_instance();
		$this->prefix = $obj->config->item('bp.concursos.database');
		parent::__construct($this->prefix . 'Diba_Pedidos', 'nIdPedido', 'cPedido', 'cPedido', $data_model);

		$this->_relations['lineas'] = array (
			'ref'	=> 'concursos/m_lineapedido',
			'type'	=> DATA_MODEL_RELATION_1N,
			'fk'	=> 'nIdPedido');		
	}

	/**
	 * Devuelve toda la información de las líneas de pedido en albarán
	 * @param int $id Id del cliente
	 */
	function enalbaran($id = null)
	{
		$obj = get_instance();
		$obj->load->model('concursos/m_configuracion');
		$data = $obj->m_configuracion->get();
		$configuracion = $data[0];
		$this->db->flush_cache();
		$this->db->select("Doc_PedidosCliente.nIdCliente, {$this->prefix}Diba_Pedidos.cPedido, {$this->prefix}Diba_Albaranes.nIdAlbaran")
		->from("{$this->prefix}Diba_Albaranes")
		->join("{$this->prefix}Diba_Pedidos", "{$this->prefix}Diba_Albaranes.nIdPedido = {$this->prefix}Diba_Pedidos.nIdPedido")
		->join("{$this->prefix}Diba_Salas", "{$this->prefix}Diba_Pedidos.nSala = {$this->prefix}Diba_Salas.nIdSala")
		->join("Doc_PedidosCliente", "Doc_PedidosCliente.nIdPedido = " . $this->db->int("{$this->prefix}Diba_Salas.cSala"));

		if (isset($id))
		{
			$this->db->where("Doc_PedidosCliente.nIdCliente = {$id}");
		}

		$query = $this->db->get();
		$data = $this->_get_results($query);
		$totales = array();
		$obj->load->model('concursos/m_albaran');
		foreach ($data as $albaran)
		{
			$type = (strpos($albaran['cPedido'], 'NARRATIVA') > 0)?'n':'g';
			$importes = $obj->m_albaran->importe($albaran['nIdAlbaran'], $configuracion);
			#var_dump($importes);
			if (!isset($totales[$type][$albaran['nIdCliente']])) $totales[$type][$albaran['nIdCliente']] = 0;
			$totales[$type][$albaran['nIdCliente']] += $importes['fTotal'];
		}
		return $totales;
	}

	/**
	 * Devuelce el estado de las líneas de pedido pendientes
	 * @param int $id Id del cliente
	 */
	function pendiente($id = null)
	{
		$obj = get_instance();
		$obj->load->model('concursos/m_configuracion');
		$data = $obj->m_configuracion->get();
		$configuracion = $data[0];
		$this->db->flush_cache();
		$this->db->select("Doc_PedidosCliente.nIdCliente, {$this->prefix}Diba_Pedidos.cPedido")
		->select_sum('fPrecio', 'fTotal')
		->from("{$this->prefix}Diba_LineasPedido")
		->join("{$this->prefix}Diba_Pedidos", "{$this->prefix}Diba_LineasPedido.nIdPedido = {$this->prefix}Diba_Pedidos.nIdPedido")
		->join("{$this->prefix}Diba_Salas", "{$this->prefix}Diba_Pedidos.nSala = {$this->prefix}Diba_Salas.nIdSala")
		->join("Doc_PedidosCliente", "Doc_PedidosCliente.nIdPedido = " . $this->db->int("{$this->prefix}Diba_Salas.cSala"))
		->where("{$this->prefix}Diba_LineasPedido.nIdEstado IN (1, 2, 5, 17)")
		->group_by("Doc_PedidosCliente.nIdCliente, {$this->prefix}Diba_Pedidos.cPedido");

		if (isset($id))
		{
			$this->db->where("Doc_PedidosCliente.nIdCliente = {$id}");
		}

		$query = $this->db->get();
		$data = $this->_get_results($query);
		$totales = array();
		foreach ($data as $albaran)
		{
			$type = (strpos($albaran['cPedido'], 'NARRATIVA') > 0)?'n':'g';
			$importes = format_calculate_importes(array(
				'fPrecio' 		=> format_quitar_iva($albaran['fTotal'], 4),
				'nCantidad' 	=> 1, 
				'fRecargo'		=> 0, 
				'fIVA' 			=> 4, 
				'fDescuento' 	=> $configuracion['fDescuento']
			));
			//$importes = $this->importe($albaran['nIdAlbaran'], $configuracion);
			#var_dump($importes);
			if (!isset($totales[$type][$albaran['nIdCliente']])) $totales[$type][$albaran['nIdCliente']] = 0;
			$totales[$type][$albaran['nIdCliente']] += $importes['fTotal'];
		}
		return $totales;
	}

	/**
	 * Libros a catalogar por biblioteca
	 * @return array
	 */
	function enestado($estado)
	{
		$this->db->flush_cache();
		$this->db->select('Doc_PedidosCliente.nIdCliente, cBiblioteca')
		->select('COUNT(*) nLibros')
		->select("ISNULL(" .
			$this->db->numeric("SUM(fPrecio * (1 - ISNULL({$this->prefix}Diba_Configuracion.fDescuento, 0) / 100.0))") . 
			", 0) fImporte")
		->from("{$this->prefix}Diba_Configuracion")
		->from("{$this->prefix}Diba_LineasPedido")
		->join("{$this->prefix}Diba_Pedidos", "{$this->prefix}Diba_Pedidos.nIdPedido = {$this->prefix}Diba_LineasPedido.nIdPedido")
		->join("{$this->prefix}Diba_Bibliotecas", "{$this->prefix}Diba_Bibliotecas.nIdBiblioteca = {$this->prefix}Diba_Pedidos.nBiblioteca")
		->join("{$this->prefix}Diba_Salas", "{$this->prefix}Diba_Pedidos.nSala = {$this->prefix}Diba_Salas.nIdSala")
		->join("Doc_PedidosCliente", "Doc_PedidosCliente.nIdPedido = " . $this->db->int("{$this->prefix}Diba_Salas.cSala"))
		->where("{$this->prefix}Diba_LineasPedido.nIdEstado = {$estado}")
		->group_by('Doc_PedidosCliente.nIdCliente, cBiblioteca')
		->order_by('cBiblioteca');

		$query = $this->db->get();
		$data = $this->_get_results($query);

		$totales = array();
		foreach ($data as $albaran)
		{
			$type = (strpos($albaran['cBiblioteca'], 'NARRATIVA') > 0)?'n':'g';
			$totales[$type][$albaran['nIdCliente']] = $albaran['fImporte'];
		}

		return $totales;
	}

	/**
	 * Devuelve los pedidos de un cliente
	 * @param int $id Id del cliente
	 * @return multitype:unknown Ambigous <number, array:, multitype:>
	 */
	function get_by_cliente($id)
	{
		$obj = get_instance();
		$obj->load->model('concursos/m_configuracion');
		$data = $obj->m_configuracion->get();
		$configuracion = $data[0];

		$this->db->flush_cache();
		$this->db->select("{$this->prefix}Diba_Salas.cSala, {$this->prefix}Diba_Pedidos.cPedido")
		->select($this->_date_field("{$this->prefix}Diba_Pedidos.dEntrada", 'dEntrada'))
		->select("{$this->prefix}Diba_Pedidos.nIdPedido")
		->select_sum('fPrecio', 'fTotal')
		->select('COUNT(*) nProductos')
		->from("{$this->prefix}Diba_LineasPedido")
		->join("{$this->prefix}Diba_Pedidos", "{$this->prefix}Diba_LineasPedido.nIdPedido = {$this->prefix}Diba_Pedidos.nIdPedido")
		->join("{$this->prefix}Diba_Salas", "{$this->prefix}Diba_Pedidos.nSala = {$this->prefix}Diba_Salas.nIdSala")
		->join("Doc_PedidosCliente", "Doc_PedidosCliente.nIdPedido = " . $this->db->int("{$this->prefix}Diba_Salas.cSala"))
		->where("Doc_PedidosCliente.nIdCliente = {$id}")
		->where('ISNULL(bMostrarWeb, 1) = 1')
		->group_by("{$this->prefix}Diba_Salas.cSala, {$this->prefix}Diba_Pedidos.nIdPedido, {$this->prefix}Diba_Pedidos.cPedido, {$this->prefix}Diba_Pedidos.dEntrada");

		$query = $this->db->get();
		$data = $this->_get_results($query);
		#var_dump($data);
		$totales = array();
		foreach ($data as $k => $albaran)
		{
			$type = (strpos($albaran['cPedido'], 'NARRATIVA') > 0)?'n':'g';

			$importes = format_calculate_importes(array(
				'fPrecio' 		=> format_quitar_iva($albaran['fTotal'], 4),
				'nCantidad' 	=> 1, 
				'fRecargo'		=> 0, 
				'fIVA' 			=> 4, 
				'fDescuento' 	=> $configuracion['fDescuento']
			));
			$data[$k]['fTotal'] = $importes['fTotal'];
			$totales[$type] = isset($totales[$type])?($totales[$type] + $importes['fTotal']):$importes['fTotal'];
		}
		return array(
			'pedidos'	=> $data,
			'totales'	=> $totales
		);
	}
}

/* End of file M_pedido.php */
/* Location: ./system/application/models/concursos/M_pedido.php */