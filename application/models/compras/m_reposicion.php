<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	compras
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Reposición
 *
 */
class M_Reposicion extends MY_Model
{
	/**
	 * Constructor
	 * @return M_Reposicion
	 */
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Crea la base de SQL para la consulta de libros
	 * @param int $ids Id de la sección
	 * @param int $idp Id del proveedor
	 * @param int $idm Id de la materia
	 * @param int $ide Id de la editorial
	 * @param int $idl Id del libro
	 */
	private function _basic($ids = null, $idp = null, $idm = null, $ide = null, $idl = null)
	{

		if (isset($idm) && is_numeric($idm))
		{
			$obj = get_instance();
			$obj->load->model('catalogo/m_materia');
			$codigo = $obj->m_materia->load($idm);
			$codigo_m = $codigo['cCodMateria'];
		}
		if (isset($ids) && is_numeric($ids))
		{
			$obj = get_instance();
			$obj->load->model('generico/m_seccion');
			$codigo = $obj->m_seccion->load($ids);
			$codigo_s = $codigo['cCodigo'];
		}

		$this->db->flush_cache();
		$this->db->select('l.nIdLibro, l.cTitulo')
		->select('sl.nStockFirme Firme,
			sl.nStockDeposito Deposito,
			sl.nStockReservado Reservado,
			sl.nStockRecibir Recibir,
			sl.nStockAPedir APedir,
			sl.nStockServir Servir,
			sl.nStockADevolver ADevolver,
			sl.nStockAExamen AExamen,
			sl.nStockMinimo Minimo,
			sl.nIdSeccionLibro,
			sc.cNombre,
			sc.nIdSeccion')
		->from('Cat_Fondo l')
		->join('Cat_Secciones_Libros sl', 'l.nIdLibro = sl.nIdlibro')
		->join('Cat_Secciones sc', "sl.nIdSeccion = sc.nIdSeccion")
		->where("ISNULL(l.nIdEstado,0) <> 16")
		->group_by('l.nIdLibro, l.cTitulo, sl.nStockFirme,
			sl.nStockDeposito,
			sl.nStockReservado,
			sl.nStockRecibir,
			sl.nStockAPedir,
			sl.nStockServir,
			sl.nStockADevolver,
			sl.nStockAExamen,
			sl.nStockMinimo,
			sl.nIdSeccionLibro,
			sc.cNombre,
			sc.nIdSeccion');
			
		// Filtro materia
		if (isset($idm) && is_numeric($idm))
		{
			$this->db->join('Cat_Libros_Materias lm', 'lm.nIdLibro = l.nIdLibro')
			->join('Cat_Materias m', 'm.nIdMateria = lm.nIdMateria')
			->where("(m.cCodMateria LIKE '{$codigo_m}.%' OR m.nIdMateria = {$idm})");
		}
		// Filtro libro
		if (isset($idl) && is_numeric($idl))
		{
			$this->db->where("l.nIdLibro = {$idl}");
		}

		// Filtro Sección
		if (isset($ids) && is_numeric($ids))
		{
			$this->db/*->join('Cat_Secciones s', 's.nIdSeccion = sl.nIdSeccion')*/
			->where("(sc.cCodigo LIKE '{$codigo_s}.%' OR sc.nIdSeccion = {$ids})");
		}
		// Filtro proveedor
		if (isset($idp) && is_numeric($idp))
		{
			$this->db->where("l.nIdProveedor = {$idp}");
		}

		// Filtro editorial
		if (isset($ide) && is_numeric($ide))
		{
			$this->db->where("l.nIdEditorial = {$ide}");
		}
	}

	/**
	 * Ejecuta la instrucción SQL y genera los resultados
	 * @param array $libros Array de libros
	 */
	private function _do(&$libros)
	{
		$query = $this->db->get();
		$data = $this->_get_results($query);

		foreach($data as $l)
		{
			$id = $l['nIdLibro'];
			$t1 = 0;
			$t2 = 0;
			if (isset($libros[$id]))
			{
				$l2 = $libros[$id];
				$t1 = $l2['Tratados'];
				$t2 = $l2['NoTratados'];
			}

			foreach($l as $k => $v)
			{
				$libros[$id][$k] = $v;
			}
			$libros[$id]['Tratados'] = $t1 + $l['Tratados'];
			$libros[$id]['NoTratados'] = $t2 + $l['NoTratados'];
		}
	}

	/**
	 * Marca los albaranes y líneas de movimiento como vistas
	 * @param int $idl Id del libro
	 * @param int $ids Id de la sección
	 * @param int $minmov Id del primer movimiento
	 * @param int $maxmov Id del último movimiento
	 * @param int $minalb Id de la primera línea de albarán
	 * @param int $maxalb Id de la última línea de albarán
	 * @return int Número de registros modificados
	 */
	function marcar($idl, $ids, $minmov = null, $maxmov = null, $minalb = null, $maxalb = null)
	{
		$data['bReposicion'] = 1;
		$count = 0;
		if (is_numeric($minmov) && is_numeric($maxmov))
		{
			$this->db->flush_cache();
			$this->db->where("nIdMovimiento >= {$minmov} AND nIdMovimiento <= {$maxmov}");
			$this->db->where("nIdSeccionOrigen = {$ids} AND nIdLibro = {$idl}");
			$this->db->update('Doc_Movimientos', $data);
			$count += $this->db->affected_rows();
		}
		if ($minalb && $maxalb)
		{
			$this->db->where("nIdLineaAlbaran >= {$minalb} AND nIdLineaAlbaran <= {$maxalb}");
			$this->db->where("nIdSeccion = {$ids} AND nIdLibro = {$idl}");
			$this->db->update('Doc_LineasAlbaranesSalida', $data);
			$count += $this->db->affected_rows();
		}
		return $count;
	}

	/**
	 * Obtiene los libros que deben ser repuestos
	 * @param date $d Fecha desde
	 * @param date $h Fecha hasta
	 * @param int $ids Id de la sección
	 * @param int $idp Id del proveedor
	 * @param int $idm Id de la materia
	 * @param int $ide Id de la editorial
	 * @param int $idl Id del libro
	 * @return array Listado de libros
	 */
	function get_libros($d, $h, $ids = null, $idp = null, $idm = null, $ide = null, $idl = null)
	{
		set_time_limit(0);

		$obj = get_instance();

		$desde	= format_mssql_date($d);
		$hasta	= format_mssql_date($h);

		$libros = array();

		// Albaranes de salida
		$this->_basic($ids, $idp, $idm, $ide, $idl);
		$this->db->select_sum('la.nCantidad', 'Vendidos')
		->select_sum('la.nCantidad * la.bReposicion', 'Tratados')
		->select_sum('la.nCantidad * (1 - la.bReposicion)', 'NoTratados')
		->select_max('nIdLineaAlbaran', 'MaxLineaAlbaran')
		->select_min('nIdLineaAlbaran', 'MinLineaAlbaran')
		->join('Doc_LineasAlbaranesSalida la', "sl.nIdLibro = la.nIdLibro AND sl.nIdSeccion = la.nIdSeccion")
		->join('Doc_AlbaranesSalida a', "a.nIdAlbaran = la.nIdAlbaran")
		->where('a.nIdEstado = 2')
		->where("a.dCreacion >= {$desde} AND a.dCreacion < " . $this->db->dateadd('d', 1, $hasta));

		$this->_do($libros);

		// Movimientos
		$this->_basic($ids, $idp, $idm, $ide, $idl);
		$this->db->select_sum('la.nCantidad', 'Movidos')
		->select_sum('la.nCantidad * la.bReposicion', 'Tratados')
		->select_sum('la.nCantidad * (1 - la.bReposicion)', 'NoTratados')
		->select_max('nIdMovimiento', 'MaxLineaMovimiento')
		->select_min('nIdMovimiento', 'MinLineaMovimiento')
		->join('Doc_Movimientos la', "sl.nIdLibro = la.nIdLibro AND sl.nIdSeccion = la.nIdSeccionOrigen")
		->where("la.dCreacion >= {$desde} AND la.dCreacion <= " . $this->db->dateadd('d', 1, $hasta));

		$this->_do($libros);

		// Convierte los datos en un array normal
		$data = array();
		foreach ($libros as $l)
		{
			if (!isset($l['Movidos']))
			{
				$l['Movidos'] = 0;
			}
			if (!isset($l['Vendidos']))
			{
				$l['Vendidos'] = 0;
			}
			$data[] = $l;
		}

		$this->_count = count($data);
		return $data;
	}

	/**
	 * Calcula el número de ventas en un perido
	 * @param int $id Id del libro
	 * @param int $unidades Número de unidades del periodo indicado por $periodo
	 * @param string $periodo m: mes, d: días, y: años
	 * @param int $ids Id de la sección
	 * @return int Número de unidades vendidas
	 */
	function get_ventas($id, $unidades, $periodo='m', $ids = null)
	{
		$this->db->flush_cache();
		$this->db->select_sum('la.nCantidad', 'nCantidad')
		->from('Doc_LineasAlbaranesSalida la')
		->join('Doc_AlbaranesSalida a' ,'la.nIdAlbaran = a.nIdAlbaran')
		#->join('Doc_Facturas f', 'a.nIdFactura = f.nIdFactura', 'left')
		->where("la.nIdLibro = {$id}")
		->where(/*'(f.dFecha IS NOT NULL 
			AND f.dFecha >= ' . $this->db->dateadd($periodo, -$unidades, 'GETDATE()') .'
			AND f.nIdEstado IN (2, 3)) 
		OR 
		(f.dFecha IS NULL AND */ '( 
			a.dCreacion >= ' . $this->db->dateadd($periodo, -$unidades, 'GETDATE()') .'
			AND a.nIdEstado = 2
		)');

		if (isset($ids))  $this->db->where('la.nIdSeccion', (int) $ids);

		$query = $this->db->get();
		if ($query)
		{
			$row = $query->row_array();
			if (isset($row['nCantidad']))
			{
				return $row['nCantidad'];
			}
		}

		return 0;
	}

	/**
	 * Calcula el número de ventas en un perido
	 * @param int $id Id del libro
	 * @param date $desde Fecha inicial
	 * @param date $hasta Fecha final
	 * @param int $ids Id de la sección
	 * @return int Número de unidades vendidas
	 */
	function get_ventasperiodo($id, $desde, $hasta, $ids = null)
	{
		$fecha1 = format_mssql_date($desde);
		$fecha2 = format_mssql_date($hasta);
		$this->db->flush_cache();
		$this->db->select_sum('la.nCantidad', 'nCantidad')
		->from('Doc_LineasAlbaranesSalida la')
		->join('Doc_AlbaranesSalida a' ,'la.nIdAlbaran = a.nIdAlbaran', 'inner')
		->join('Doc_Facturas f', 'a.nIdFactura = f.nIdFactura', 'inner')
		->where("la.nIdLibro = {$id}")
		->where("f.dFecha >= {$fecha1}")
		->where("f.dFecha < " . $this->db->dateadd('d', 1, $fecha2))
		->where_in('f.nIdEstado',array(2, 3));

		if (isset($ids))  $this->db->where('la.nIdSeccion', (int) $ids);

		$query = $this->db->get();
		if ($query)
		{
			$row = $query->row_array();
			if (isset($row['nCantidad']))
			{
				return $row['nCantidad'];
			}
		}

		return 0;
	}

	/**
	 * Devuelve los pedidos pendientes del proveedor
	 * @param int $id Id del proveedor
	 * @param bool $deposito TRUE: pedidos en depósito, FALSE: en firme
	 * @return array Pedidos
	 */
	function get_pedidos($id, $deposito)
	{
		$this->db->select('pd.nIdPedido,
			p.cEmpresa, 
			p.cNombre, 
			p.cApellido, 
			pd.cRefProveedor, 
			pd.cRefInterna')
		->select($this->db->date_field('pd.dCreacion', 'dCreacion'))
		->from('Doc_PedidosProveedor pd')
		->join('Doc_LineasPedidoProveedor lp' ,'pd.nIdPedido = lp.nIdPedido', 'inner')
		->join('Prv_Proveedores p', 'p.nIdProveedor = pd.nIdProveedor', 'inner')
		->join('Prv_Proveedores_Cat_Fondo pv', 'pv.nIdProveedor = p.nIdProveedor', 'inner')
		->where('pd.nIdEstado',(int) 1)
		->where('pv.nIdLibro',(int) $id)
		->where('pd.bDeposito', (bool) $deposito)
		->group_by('pd.nIdPedido, '. $f);
		$query = $this->db->get();
		$data = $this->_get_results($query);
		foreach ($data as $k => $v)
		{
			$data[$k]['text'] = $v['nIdPedido'] . ' / ' . format_name($v['cNombre'], $v['cApellido'], $v['cEmpresa']) .
			$v['cRefProveedor'] . ' - ' . $v['cRefInterna'];
		}
		$this->count = count($data);
		return $data;
	}
}

/* End of file M_reposicion.php */
/* Location: ./system/application/models/compras/M_reposicion.php */
