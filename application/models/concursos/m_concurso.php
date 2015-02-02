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
 * Concursos
 *
 */
class M_concurso extends MY_Model
{
	/**
	 * Costructor 
	 * @return M_concurso
	 */
	function __construct()
	{
		$data_model = array(
			'cDescripcion'		=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE), 
			'fDescuento'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DOUBLE),
			'dDesde' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATETIME),
			'dHasta' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATETIME),
		);
		
		parent::__construct('Ext_Concursos', 'nIdConcurso', 'cDescripcion', 'cDescripcion', $data_model, TRUE);	
	}

	/**
	 * Estado del concurso por biblioteca
	 * @param  int $id Id del concurso
	 * @return array
	 */
	function estado($id)
	{
		$this->db->flush_cache();
		$this->db->select('Ext_EstadosConcurso.cDescripcion cEstado')
		->select('Ext_EstadosConcurso.bSuma')
		->select('Ext_Bibliotecas.cDescripcion cBiblioteca')
		->select('COUNT(*) nUnidades')
		->select('Ext_Bibliotecas.fImporte')
		->select('Ext_Concursos.fDescuento')
		->select_sum($this->db->isnull($this->db->numeric('Ext_LineasPedidoConcurso.fPrecio*(1 + (Cat_Tipos.fIVA/100))'), 0) ,'fVentaConIVA')
		->select_sum($this->db->isnull('Ext_LineasPedidoConcurso.fPrecio', 0) ,'fVentaSinIVA')
		->select_sum('ISNULL(Doc_LineasAlbaranesEntrada.fCoste, Cat_Fondo.fPrecioCompra)', 'fCoste')
		->from('Ext_LineasPedidoConcurso')
		->join('Ext_EstadosConcurso', "Ext_EstadosConcurso.nIdEstado = Ext_LineasPedidoConcurso.nIdEstado")
		->join('Ext_Bibliotecas', "Ext_LineasPedidoConcurso.nIdBiblioteca = Ext_Bibliotecas.nIdBiblioteca")
		->join('Ext_Concursos', "Ext_Concursos.nIdConcurso = Ext_Bibliotecas.nIdConcurso")
		->join('Cat_Fondo', 'Cat_Fondo.nIdLibro=Ext_LineasPedidoConcurso.nIdLibro', 'left')
		->join('Cat_Tipos', 'Cat_Fondo.nIdTipo=Cat_Tipos.nIdTipo', 'left')
		->join('Doc_LineasAlbaranesEntrada', 'Ext_LineasPedidoConcurso.nIdLineaAlbaranEntrada=Doc_LineasAlbaranesEntrada.nIdLinea', 'left')
		->where("Ext_Bibliotecas.nIdConcurso = {$id}")
		->group_by('Ext_EstadosConcurso.cDescripcion, Ext_EstadosConcurso.bSuma, Ext_Bibliotecas.cDescripcion, Ext_Bibliotecas.fImporte, Ext_Concursos.fDescuento')
		->order_by('Ext_Bibliotecas.cDescripcion, Ext_EstadosConcurso.cDescripcion');
		$query = $this->db->get();
		$data = $this->_get_results($query);
		return $data;
	}

	/**
	 * Editoriales que no tienen proveedor en algunos artículos, pero en otros si
	 * 
	 * @return array
	 */
	function editoriales()
	{
		$this->db->flush_cache();
		$this->db->select('LTRIM(RTRIM(cEditorial1a)) cEditorial')
		->select('Cat_Fondo.nIdProveedor, Cat_Editoriales.nIdProveedor')
		->select('Cat_Fondo.nIdEditorial,Cat_Editoriales.cNombre')
		->select('COUNT(*) nContador')
		->from('Ext_LineasPedidoConcurso')
		->join('Cat_Fondo', "Cat_Fondo.nIdLibro = Ext_LineasPedidoConcurso.nIdLibro", 'left')
		->join('Cat_Editoriales', "Cat_Fondo.nIdEditorial = Cat_Editoriales.nIdEditorial", 'left')
		->join('Prv_Proveedores', "Prv_Proveedores.nIdProveedor = " .$this->db->isnull('Cat_Fondo.nIdProveedor', 'Cat_Editoriales.nIdProveedor'), 'left')
		->where($this->db->isnull('Cat_Fondo.nIdProveedor', 'Cat_Editoriales.nIdProveedor') .' IS NOT NULL')
		->where('Ext_LineasPedidoConcurso.nIdEstado<>1')
		->group_by('LTRIM(RTRIM(cEditorial1a))')
		->group_by('Cat_Fondo.nIdProveedor, Cat_Editoriales.nIdProveedor,Cat_Editoriales.cNombre')
		->group_by('Cat_Fondo.nIdEditorial');
		$query = $this->db->get();
		$data = $this->_get_results($query);

		return $data;
	}

	/**
	 * Artículos sin proveedor vinculados al concurso con la editorial indicada
	 * @param  string $malo Editorial del concurso
	 * @return array
	 */
	function sin_proveedor($malo)
	{
		//var_dump($malo); die();
		#echo htmlspecialchars($malo);
		$malo = $this->db->escape($malo);
		$this->db->flush_cache();
		$this->db->select('Cat_Fondo.nIdLibro')
		->from('Ext_LineasPedidoConcurso')
		->join('Cat_Fondo', "Cat_Fondo.nIdLibro = Ext_LineasPedidoConcurso.nIdLibro", 'left')
		->join('Cat_Editoriales', "Cat_Fondo.nIdEditorial = Cat_Editoriales.nIdEditorial", 'left')
		->where($this->db->isnull('Cat_Fondo.nIdProveedor', 'Cat_Editoriales.nIdProveedor') .' IS NULL')
		->where('Ext_LineasPedidoConcurso.nIdEstado = 1')
		->where("LTRIM(RTRIM(cEditorial1a))={$malo}")
		->group_by('Cat_Fondo.nIdLibro');
		$query = $this->db->get();
		$data = $this->_get_results($query);
		#print_r($this->db->queries); #die();

		return $data;
	}

	/**
	 * Editoriales  sin proveedor
	 * @return array
	 */
	function sin_editorial()
	{
		$this->db->flush_cache();
		$this->db->select('LTRIM(RTRIM(cEditorial1a)) cEditorial')
		->select('count(*) nContador')
		->from('Ext_LineasPedidoConcurso')
		->join('Cat_Fondo', "Cat_Fondo.nIdLibro = Ext_LineasPedidoConcurso.nIdLibro", 'left')
		->join('Cat_Editoriales', "Cat_Fondo.nIdEditorial = Cat_Editoriales.nIdEditorial", 'left')
		//->join('Prv_Proveedores', "Prv_Proveedores.nIdProveedor = " .$this->db->isnull('Cat_Fondo.nIdProveedor', 'Cat_Editoriales.nIdProveedor'), 'left')
		->where($this->db->isnull('Cat_Fondo.nIdProveedor', 'Cat_Editoriales.nIdProveedor') .' IS NULL')
		->where('Ext_LineasPedidoConcurso.nIdEstado = 1')
		->group_by('LTRIM(RTRIM(cEditorial1a))');
		
		$query = $this->db->get();
		$data = $this->_get_results($query);

		return $data;
	}

	/**
	 * Artículos que son obras según los textos que acompañaban a los ISBNs
	 * @param  string $concurso Id del concurso
	 * @return array
	 */
	function obras($concurso)
	{
		$mandan = array_filter(explode("\n", file_get_contents(__DIR__ . DS . '..' . DS . '..' . DS . 'controllers' . DS . 'concursos' . DS . 'oc.txt')));
		$libros = array();
		foreach ($mandan as $value) 
		{
			#var_dump($value, utf8_decode($value), utf8_encode($value));
			$value = $this->db->escape("%{$value}%");
			$this->db->flush_cache();
			$this->db->select('Ext_LineasPedidoConcurso.nIdLineaPedidoConcurso')
			->from('Ext_LineasPedidoConcurso')
			->join('Ext_Bibliotecas', 'Ext_Bibliotecas.nIdBiblioteca=Ext_LineasPedidoConcurso.nIdBiblioteca')
			->where("cElxurro LIKE {$value}")
			->where("Ext_Bibliotecas.nIdConcurso={$concurso}")
			->where('nIdLineaPedidoConcurso NOT IN (SELECT nIdLineaPedidoConcurso FROM Ext_LineasPedidoConcursoAcciones WHERE nIdTipo=' . ACCION_OBRA_VISTA . ')');
			$query = $this->db->get();
			$data = $this->_get_results($query);
			foreach ($data as $reg) 
			{
				$libros[$reg['nIdLineaPedidoConcurso']] = $reg['nIdLineaPedidoConcurso'];
			}
		}
		return $libros;
	}

	/**
	 * Artículos que están descatalogados en Bibliopola
	 * @param  string $concurso Id del concurso
	 * @return array
	 */
	function descatalogados($concurso)
	{
		/*
			PEDIDO
			------
			22	A PEDIR
			1	EN PROCESO
			5	PEDIDO AL PROVEEDOR


			ARTICULO
			--------
			6	AGOTADO EN PROVEEDOR
			4	DESCATALOGADO
			7	EN REEDICION
			8	EN REIMPRESION
			13	NO LOCALIZADO
			14	NO PUBLICADO
			12	NO VENAL
			15	OBSEQUIO
			17	POD
		 */
		
		$this->db->flush_cache();
		$this->db->select('Ext_LineasPedidoConcurso.nIdLineaPedidoConcurso')
		->select('Cat_Fondo.nIdEstado nIdEstadoLibro, Cat_EstadosLibro.cDescripcion cEstadoLibro')
		->select('Ext_EstadosConcurso.cDescripcion cEstado')
		->from('Ext_LineasPedidoConcurso')
		->join('Ext_Bibliotecas', 'Ext_Bibliotecas.nIdBiblioteca=Ext_LineasPedidoConcurso.nIdBiblioteca')
		->join('Cat_Fondo', 'Cat_Fondo.nIdLibro=Ext_LineasPedidoConcurso.nIdLibro')
		->join('Cat_EstadosLibro', 'Cat_EstadosLibro.nIdEstado=Cat_Fondo.nIdEstado')
		->join('Ext_EstadosConcurso', 'Ext_EstadosConcurso.nIdEstado=Ext_LineasPedidoConcurso.nIdEstado')
		#->join('Ext_EstadosConcurso', 'Ext_EstadosConcurso.nIdEstado')
		->where('Cat_Fondo.nIdEstado IN (6, 4, 7, 8, 13, 14, 12, 15, 17)')
		->where('Ext_LineasPedidoConcurso.nIdEstado IN (22, 1, 5)')
		->where("Ext_Bibliotecas.nIdConcurso={$concurso}")
		->where('nIdLineaPedidoConcurso NOT IN (SELECT nIdLineaPedidoConcurso FROM Ext_LineasPedidoConcursoAcciones WHERE nIdTipo=' . ACCION_DESCATALOGADO_VISTO . ')');
		$query = $this->db->get();
		$data = $this->_get_results($query);
		return $data;
	}

	/**
	 * Libros con >$dias sin venta que tiene pedido en el concurso
	 * @param  int $concurso Id concurso
	 * @param  int $dias     Número de días sin ventas
	 * @return array
	 */
	function antiguos($concurso, $dias = 0, $estado = '1')
	{
		$this->db->flush_cache();
		$this->db->select('Ext_LineasPedidoConcurso.nIdLineaPedidoConcurso')
		->select('Cat_Fondo.cTitulo, Cat_Fondo.nIdLibro, Cat_Fondo.cAutores')
		->select('Cat_Editoriales.cNombre')
		->select('Cat_Secciones.cNombre')
		->select('Cat_Secciones_Libros.nStockDeposito, Cat_Secciones_Libros.nStockFirme')
		->select($this->_date_field('Cat_Fondo.dUltimaVenta', 'dUltimaVenta'))
		->from('Ext_LineasPedidoConcurso')
		->join('Ext_Bibliotecas', 'Ext_Bibliotecas.nIdBiblioteca=Ext_LineasPedidoConcurso.nIdBiblioteca')
		->join('Cat_Fondo', 'Cat_Fondo.nIdLibro=Ext_LineasPedidoConcurso.nIdLibro')
		->join('Cat_Editoriales', 'Cat_Editoriales.nIdEditorial=Cat_Fondo.nIdEditorial')
		->join('Cat_Secciones_Libros', 'Cat_Fondo.nIdLibro = Cat_Secciones_Libros.nIdLibro')
		->join('Cat_Secciones', 'Cat_Secciones.nIdSeccion=Cat_Secciones_Libros.nIdSeccion')
		->where('(Cat_Secciones_Libros.nStockDeposito + Cat_Secciones_Libros.nStockFirme) > 0')
		->where("Ext_LineasPedidoConcurso.nIdEstado IN ({$estado})")
		->where("Ext_Bibliotecas.nIdConcurso={$concurso}")
		->order_by('Cat_Secciones.cNombre, Cat_Fondo.cTitulo');
		if ($dias > 0)
		{
			$this->db->where("((" . $this->db->datediff('Cat_Fondo.dUltimaVenta', 'GETDATE()') . " > {$dias}) OR Cat_Fondo.dUltimaVenta IS NULL)")
			->where("((" . $this->db->datediff('Cat_Fondo.dCreacion', 'GETDATE()') . " > {$dias}) OR Cat_Fondo.dCreacion IS NULL)");
		}
		$query = $this->db->get();
		$data = $this->_get_results($query);
		return $data;
	}
}

/* End of file M_concurso.php */
/* Location: ./system/application/models/concursos/M_concurso.php */