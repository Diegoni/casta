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
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Albarán agrupado
 *
 */
class M_albaranagrupado extends MY_Model
{
	/**
	 * Prefijo de la base de datos
	 * @var string
	 */
	var $prefix = '';

	/**
	 * Constructor
	 * @return M_albaranagrupado
	 */
	function __construct()
	{
		$data_model = array(
			'nIdBiblioteca' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'concursos/biblioteca2/search')),
			'nIdFactura' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'concursos/facturaconcurso/search')),
		);

		//CI
		$obj =& get_instance();
		$this->prefix = $obj->config->item('bp.concursos.database');
		parent::__construct($this->prefix . 'Diba_AlbaranesAgrupados', 'nIdAlbaranAgrupado', 'nIdAlbaranAgrupado', 'nIdAlbaranAgrupado', $data_model, TRUE);

		$this->_relations['albaranes'] = array (
			'ref'	=> 'concursos/m_albaran',
			'type'	=> DATA_MODEL_RELATION_1N,
			'fk'	=> 'nIdAlbaranAgrupado');

		$this->_relations['biblioteca'] = array (
			'ref'	=> 'concursos/m_biblioteca2',
			'fk'	=> 'nIdBiblioteca');				
	}

	function importe($id, $configuracion)
	{
		$lineas = $this->lineas($id);
		$ivaimporte = $base = $total = $importe_pvp = 0 ;
		#$ivas = array();
		#$bases = array();
		#var_dump($lineas);
		$ejemplares = 0;
		$titulos = 0;
		$iva = 4;
		foreach($lineas as $linea)
		{
			$pvp = $linea['fPrecio'];
			$importes = format_calculate_importes(array(
				'fPrecio' 		=> format_quitar_iva($pvp, $iva),
				'nCantidad' 	=> $linea['nCantidad'], 
				'fRecargo'		=> 0, 
				'fIVA' 			=> $iva, 
				'fDescuento' 	=> $configuracion['fDescuento']
			));
			$base += $importes['fBase'];
			$importe_pvp += $importes['fTotal'];
			$ejemplares += $linea['nCantidad'];
			$titulos++;
		}
		$base = format_quitar_iva($importe_pvp, $iva);
		$ivaimporte = format_iva($base, $iva);
		return array(
				'fBase' 		=> $base,
				'fIVA' 			=> $iva,
				'fIVAImporte'	=> $ivaimporte,
				'fTotal' 		=> $base + $ivaimporte,
				'ejemplares'	=> $ejemplares,
				'titulos'		=> $titulos,
		);

		/*
		 $this->db->flush_cache();
		 $this->db->select_sum("{$this->prefix}Diba_LineasAlbaran.fPrecio", 'fPVP')
		 ->from("{$this->prefix}Diba_LineasAlbaran" , "{$this->prefix}Diba_LineasAlbaran.nIdAlbaran = {$this->prefix}.Diba_Albaran.nIdAlbaran")
		 ->join("{$this->prefix}Diba_Albaranes" , "{$this->prefix}Diba_Albaranes.nIdAlbaran = {$this->prefix}Diba_LineasAlbaran.nIdAlbaran")
		 ->where("{$this->prefix}Diba_Albaranes.nIdAlbaranAgrupado = {$id}");
		 $query = $this->db->get();
		 $data = $this->_get_results($query);
		 return $data[0]['fPVP'];
		 */
	}

	function lineas($id, $agrupado = TRUE)
	{
		$this->db->flush_cache();
		$this->db->select("{$this->prefix}Diba_LineasPedido.cISBN, {$this->prefix}Diba_LineasPedido.cAutores, {$this->prefix}Diba_LineasPedido.cTitulo")
		->select("{$this->prefix}Diba_LineasAlbaran.fPrecio")
		->from("{$this->prefix}Diba_Albaranes", "{$this->prefix}Diba_AlbaranesAgrupados.nIdAlbaranAgrupado = {$this->prefix}Diba_Albaranes.nIdAlbaranAgrupado")
		->join("{$this->prefix}Diba_LineasAlbaran" , "{$this->prefix}Diba_LineasAlbaran.nIdAlbaran = {$this->prefix}Diba_Albaranes.nIdAlbaran")
		->join("{$this->prefix}Diba_LineasPedido" , "{$this->prefix}Diba_LineasPedido.nIdLibro = {$this->prefix}Diba_LineasAlbaran.nIdLibro")
		->where("{$this->prefix}Diba_Albaranes.nIdAlbaranAgrupado = {$id}")
		->order_by("{$this->prefix}Diba_LineasPedido.cTitulo");

		if ($agrupado)
		{
			$this->db->select('COUNT(*) nCantidad')
			->group_by("{$this->prefix}Diba_LineasPedido.cISBN, {$this->prefix}Diba_LineasPedido.cAutores, {$this->prefix}Diba_LineasPedido.cTitulo,{$this->prefix}Diba_LineasAlbaran.fPrecio");
		}
		else
		{
			$this->db->select("1 nCantidad");
		}
		$query = $this->db->get();
		$data = $this->_get_results($query);
		return $data;
	}

	function bibliotecas()
	{
		$obj = get_instance();
		$data = $obj->m_configuracion->get();
		$configuracion = $data[0];
		$this->db->flush_cache();
		$this->db->select("Doc_PedidosCliente.nIdCliente, {$this->prefix}Diba_Bibliotecas.cBiblioteca")
		->from("{$this->prefix}Diba_Albaranes")
		->join("{$this->prefix}Diba_Pedidos", "{$this->prefix}Diba_Albaranes.nIdPedido = {$this->prefix}Diba_Pedidos.nIdPedido")
		->join("{$this->prefix}Diba_Bibliotecas", "{$this->prefix}Diba_Pedidos.nBiblioteca = {$this->prefix}Diba_Bibliotecas.nIdBiblioteca")
		->join("{$this->prefix}Diba_Salas", "{$this->prefix}Diba_Pedidos.nSala = {$this->prefix}Diba_Salas.nIdSala")
		->join("Doc_PedidosCliente", "Doc_PedidosCliente.nIdPedido = " . $this->db->int("{$this->prefix}Diba_Salas.cSala"))
		->group_by("Doc_PedidosCliente.nIdCliente, {$this->prefix}Diba_Bibliotecas.cBiblioteca");

		$query = $this->db->get();
		$data = $this->_get_results($query);
		$bibliotecas = array();
		foreach ($data as $b)
		{
			$bibliotecas[$b['cBiblioteca']] = $b['nIdCliente'];
		}
		return $bibliotecas;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeSelect($id, $sort, $dir, $where)
	 */
	protected function onBeforeSelect($id = null, &$sort = null, &$dir = null, &$where = null)
	{
		if (parent::onBeforeSelect($id, $sort, $dir, $where))
		{
			$this->db->select("{$this->prefix}Diba_Bibliotecas.cBiblioteca");
			$this->db->join($this->prefix . 'Diba_Bibliotecas', "{$this->prefix}Diba_Bibliotecas.nIdBiblioteca = {$this->prefix}Diba_AlbaranesAgrupados.nIdBiblioteca");
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onAfterUpdate($id, $data)
	 */
	protected function onAfterUpdate($id, &$data)
	{
		if (parent::onAfterUpdate($id, $data))
		{
			// Añade la factura a los albaranes
			if (isset($data['nIdFactura']))
			{
				$albaranes = $this->load($id, 'albaranes');
				$this->obj->load->model('concursos/m_albaran');
				foreach($albaranes['albaranes'] as $albaran)
				{
					if (!$this->obj->m_albaran->update($albaran['nIdAlbaran'], array('nIdFactura' => $data['nIdFactura'])))
					{
						return FALSE;
					}
				}
			}
			return TRUE;
		}
		return FALSE;

	}
}

/* End of file M_albaranagrupado.php */
/* Location: ./system/application/models/concursos/M_albaranagrupado.php */