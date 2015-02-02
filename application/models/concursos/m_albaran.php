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
 * Albaranes  Concurso
 *
 */
class M_albaran extends MY_Model
{
	/**
	 * Prefijo de la base de datos
	 * @var string
	 */
	var $prefix = '';

	/**
	 * Costructor
	 * @return M_albaran
	 */
	function __construct()
	{
		$data_model = array(
			'nIdPedido' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'concursos/pedido/search')),
			'nIdFactura' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'concursos/facturaconcurso/search')),
			'nIdAlbaranAgrupado' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'concursos/albaranagrupado/search')),		
			'dEnvioForrar' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATETIME),
			'dEnvioCliente' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATETIME),
		);

		$obj =& get_instance();
		$this->prefix = $obj->config->item('bp.concursos.database');

		parent::__construct($this->prefix . 'Diba_Albaranes', 'nIdAlbaran', 'nIdAlbaran', 'nIdAlbaran', $data_model, TRUE);

		$this->_relations['lineas'] = array (
			'ref'	=> 'concursos/m_lineapedido',
			'type'	=> DATA_MODEL_RELATION_1N,
			'fk'	=> 'nIdAlbaran');

		$this->_alias = array(
			'nIdBiblioteca' 	=> array("{$this->prefix}Diba_Pedidos.nBiblioteca", DATA_MODEL_TYPE_INT),
		);
	}

	/**
	 * Devuelve las líneas de un albarán
	 * @param int $id Id del albarán
	 * @param bool $agrupado TRUE: Agrupa las líneas en cantidadas, FALSE: Cada línea por separado
	 * @return array
	 */
	function lineas($id, $agrupado = TRUE)
	{
		$this->db->flush_cache();
		$this->db->select("{$this->prefix}Diba_LineasPedido.cISBN, {$this->prefix}Diba_LineasPedido.cAutores, {$this->prefix}Diba_LineasPedido.cTitulo")
		->select("{$this->prefix}Diba_LineasAlbaran.fPrecio")
		->from("{$this->prefix}Diba_Albaranes")
		->join("{$this->prefix}Diba_LineasAlbaran" , "{$this->prefix}Diba_LineasAlbaran.nIdAlbaran = {$this->prefix}Diba_Albaranes.nIdAlbaran")
		->join("{$this->prefix}Diba_LineasPedido" , "{$this->prefix}Diba_LineasPedido.nIdLibro = {$this->prefix}Diba_LineasAlbaran.nIdLibro")
		->where("{$this->prefix}Diba_Albaranes.nIdAlbaran = {$id}")
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

	/**
	 * Devuelve los importes de un albarán
	 * @param int $id Id del albarán
	 * @param array $configuracion Configuración del concurso
	 */
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
	}

	/**
	 * Albaranes de proveedor del concurso
	 * @param string $concurso Concurso
	 * @return array
	 */
	function get_albaranes($concurso)
	{
		$this->db->flush_cache();
		$this->db->select("nIdAlbaran, fImporte, cDescripcion, cProveedor")
		->select($this->_date_field('dFechaEntrada', 'dFechaEntrada'))
		->select($this->_date_field('dFechaProceso', 'dFechaProceso'))
		->from("{$concurso}..Diba_AlbaranesProveedor")
		->order_by("{$concurso}..Diba_AlbaranesProveedor.cProveedor, {$concurso}..Diba_AlbaranesProveedor.dFechaEntrada");

		$query = $this->db->get();
		$data = $this->_get_results($query);
		return $data;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeSelect($id, $sort, $dir, $where)
	 */
	protected function onBeforeSelect($id = null, &$sort = null, &$dir = null, &$where = null)
	{
		if (parent::onBeforeSelect($id, $sort, $dir, $where))
		{
			$this->db->select("{$this->prefix}Diba_Pedidos.cPedido, {$this->prefix}Diba_Pedidos.nBiblioteca nIdBiblioteca");
			$this->db->join($this->prefix . 'Diba_Pedidos', "{$this->prefix}Diba_Pedidos.nIdPedido = {$this->prefix}Diba_Albaranes.nIdPedido");
			return TRUE;
		}
		return FALSE;
	}

}

/* End of file M_albaran.php */
/* Location: ./system/application/models/concursos/M_albaran.php */