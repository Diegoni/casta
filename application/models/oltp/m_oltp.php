<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	oltp
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @filesource
 */
/**
 * Funciones OLTP
 *
 */
class M_Oltp extends MY_Model
{
	/**
	 * Base de datos OLTP
	 * @var string
	 */
	private $_prefix = '';
	/**
	 * Constructor
	 *
	 * @return M_Oltp
	 */
	function __construct()
	{
		parent::__construct();
		$this->load->library('cache');
		$this->_prefix = $this->config->item('bp.oltp.database');
	}

	/**
	 * Completa un array para que sean resultados por meses
	 *
	 * @param array $datos Datos
	 * @return array
	 */
	private function _meses_datos($datos, $field ='importe')
	{
		$res = array();
		for($i = 0; $i < 12; $i++)
		{
			$res[$i] = 0;
		}
		foreach($datos as $mes)
		{
			$res[$mes['mes'] - 1] = $mes[$field];
		}
		return $res;
	}

	/**
	 * Ventas por meses y secciones
	 *
	 * @param int $anno Año
	 * @param int $id Id de la sección
	 * @param date $fecha Fecha límite
	 * @return array
	 */
	function ventas_meses($anno, $id =null, $fecha =null)
	{
		$this->db->flush_cache();
		$this->db->select('MONTH(Doc_Facturas.dFecha) mes')
		->select_sum($this->db->numeric('Doc_LineasAlbaranesSalida.fPrecio * Doc_LineasAlbaranesSalida.nCantidad') . ' - ' .
			$this->db->numeric('Doc_LineasAlbaranesSalida.fPrecio * Doc_LineasAlbaranesSalida.nCantidad * (ISNULL(Doc_LineasAlbaranesSalida.fDescuento,0)/100)'), 'importe')
		->select_sum('ISNULL(Doc_LineasAlbaranesSalida.fCoste*ABS(Doc_LineasAlbaranesSalida.nCantidad), 0)', 'coste')
		->from("Doc_Facturas")
		->join('Doc_AlbaranesSalida', 'Doc_AlbaranesSalida.nIdFactura = Doc_Facturas.nIdFactura')
		->join('Doc_LineasAlbaranesSalida', 'Doc_LineasAlbaranesSalida.nIdAlbaran = Doc_AlbaranesSalida.nIdAlbaran')
		->join('Cat_Secciones', "Cat_Secciones.nIdSeccion = Doc_LineasAlbaranesSalida.nIdSeccion")
		->where('YEAR(Doc_Facturas.dFecha)', (int)$anno)
		->where('Doc_Facturas.nIdEstado IN (2,3)')
		->group_by('MONTH(Doc_Facturas.dFecha)');
		if(isset($id) && (is_numeric($id)))
		{
			$this->db->join('Cat_Secciones s3', 's3.nIdSeccion = ' . $id);
			$this->db->where("(Cat_Secciones.cCodigo LIKE ". 
				$this->db->concat(array($this->db->varchar('s3.cCodigo'), "'.%'")) . "
			OR Cat_Secciones.nIdSeccion = {$id})");
		}
		if(isset($fecha) && $fecha != '')
		{
			$fecha = format_mssql_date($fecha);
			$this->db->where("Doc_Facturas.dFecha < " . $this->db->dateadd('d', 1, $fecha));
		}
		$query = $this->db->get();
		$data = $this->_get_results($query);
		$ventas = $this->obj->utils->meses_datos($data);
		$coste = $this->obj->utils->meses_datos($data, 'coste');
		$this->_count = count($ventas);
		$this->db->flush_cache();
		$this->db->select('MONTH(Doc_AlbaranesSalida.dCreacion) mes')
		->select_sum($this->db->numeric('Doc_LineasAlbaranesSalida.fPrecio * Doc_LineasAlbaranesSalida.nCantidad') . ' - ' .
			$this->db->numeric('Doc_LineasAlbaranesSalida.fPrecio * Doc_LineasAlbaranesSalida.nCantidad * (ISNULL(Doc_LineasAlbaranesSalida.fDescuento,0)/100)'), 'importe')
		->select_sum('ISNULL(Doc_LineasAlbaranesSalida.fCoste*ABS(Doc_LineasAlbaranesSalida.nCantidad), 0)', 'coste')
		->from('Doc_AlbaranesSalida')
		->join('Doc_LineasAlbaranesSalida', 'Doc_LineasAlbaranesSalida.nIdAlbaran = Doc_AlbaranesSalida.nIdAlbaran')
		->join('Cat_Secciones', "Cat_Secciones.nIdSeccion = Doc_LineasAlbaranesSalida.nIdSeccion")
		->where("YEAR(Doc_AlbaranesSalida.dCreacion) = {$anno}")
		->where('Doc_AlbaranesSalida.nIdFactura IS NULL')
		->where('Doc_AlbaranesSalida.nIdEstado = 2')
		->group_by('MONTH(Doc_AlbaranesSalida.dCreacion)');
		if(isset($id) && (is_numeric($id)))
		{
			$this->db->join('Cat_Secciones s3', 's3.nIdSeccion = ' . $id);
			$this->db->where("(Cat_Secciones.cCodigo LIKE ".
				$this->db->concat(array($this->db->varchar('s3.cCodigo'), "'.%'")) . "
			OR Cat_Secciones.nIdSeccion = {$id})");
		}
		if(isset($fecha) && $fecha != '')
		{
			$this->db->where("Doc_AlbaranesSalida.dCreacion < " . $this->db->dateadd('d', 1, $fecha));
		}
		$query = $this->db->get();
		$data = $this->_get_results($query);
		$albaranes = $this->obj->utils->meses_datos($data, 'coste');
		return array(
			'ventas' 	=> $ventas,
			'coste' 	=> $coste,
			'albaranes' => $albaranes);
	}

	/**
	 * Ventas por meses y materias
	 *
	 * @param int $anno Año
	 * @param int $id Id de la Materia
	 * @param date $fecha Fecha límite
	 * @return array
	 */
	function ventas_meses_materias($anno, $id =null, $fecha =null)
	{
		$this->db->flush_cache();
		$this->db->select('MONTH(Doc_AlbaranesSalida.dCreacion) mes')
		->select_sum($this->db->numeric('Doc_LineasAlbaranesSalida.fPrecio * Doc_LineasAlbaranesSalida.nCantidad') . ' - ' .
			$this->db->numeric('Doc_LineasAlbaranesSalida.fPrecio * Doc_LineasAlbaranesSalida.nCantidad * (ISNULL(Doc_LineasAlbaranesSalida.fDescuento,0)/100)'), 'importe')
		->select_sum('ISNULL(Doc_LineasAlbaranesSalida.fCoste*ABS(Doc_LineasAlbaranesSalida.nCantidad), 0)', 'coste')
		->from('Doc_AlbaranesSalida')
		->join('Doc_LineasAlbaranesSalida', 'Doc_LineasAlbaranesSalida.nIdAlbaran = Doc_AlbaranesSalida.nIdAlbaran')
		->join('Cat_Fondo', 'Cat_Fondo.nIdLibro=Doc_LineasAlbaranesSalida.nIdLibro')
		->join('Cat_Libros_Materias', 'Cat_Fondo.nIdLibro=Cat_Libros_Materias.nIdLibro')
		->join('Cat_Materias', "Cat_Materias.nIdMateria = Cat_Libros_Materias.nIdMateria")
		->where("YEAR(Doc_AlbaranesSalida.dCreacion) = {$anno}")
		->where('Doc_AlbaranesSalida.nIdEstado = 2')
		->group_by('MONTH(Doc_AlbaranesSalida.dCreacion)');
		if(isset($id) && (is_numeric($id)))
		{
			$this->db->join('Cat_Materias s3', 's3.nIdMateria = ' . $id)
			->where("(Cat_Materias.cCodMateria LIKE ".  
				$this->db->concat(array($this->db->varchar('s3.cCodMateria'), "'.%'")) . 
				" OR Cat_Materias.nIdMateria = {$id})");
		}
		if(isset($fecha) && $fecha != '')
		{
			$fecha = format_mssql_date($fecha);
			$this->db->where("Doc_AlbaranesSalida.dCreacion < " . $this->db->dateadd('d', 1, $fecha));
		}
		$query = $this->db->get();
		$data = $this->_get_results($query);
		#echo array_pop($this->db->queries);
		return $this->obj->utils->meses_datos($data);
	}

	/**
	 * Compras por meses y series
	 *
	 * @param int $anno Año
	 * @param int $id Id de la sección
	 * @param date $fecha Fecha límite
	 * @return array
	 */
	function compras_meses($anno, $id =null, $fecha =null)
	{
		$this->db->flush_cache();
		$this->db->select('MONTH(Doc_AlbaranesEntrada.dCierre) mes')
		->select_sum($this->db->numeric('(Doc_LineasPedidosRecibidas.nCantidad * Doc_LineasAlbaranesEntrada.fPrecio) * (1 - Doc_LineasAlbaranesEntrada.fDescuento / 100.0)'),
			'importe')
		->from("Doc_LineasPedidosRecibidas")
		->join('Doc_LineasAlbaranesEntrada', 'Doc_LineasAlbaranesEntrada.nIdLinea = Doc_LineasPedidosRecibidas.nIdLineaAlbaran')
		->join('Doc_AlbaranesEntrada', 'Doc_LineasAlbaranesEntrada.nIdAlbaran = Doc_AlbaranesEntrada.nIdAlbaran')
		->join('Doc_LineasPedidoProveedor', 'Doc_LineasPedidoProveedor.nIdLinea = Doc_LineasPedidosRecibidas.nIdLineaPedido')
		->join('Cat_Secciones', "Cat_Secciones.nIdSeccion = Doc_LineasPedidoProveedor.nIdSeccion")
		->where("YEAR(Doc_AlbaranesEntrada.dCierre)={$anno}")
		->where('Doc_AlbaranesEntrada.nIdEstado IN (2, 3, 4)')
		->group_by('MONTH(Doc_AlbaranesEntrada.dCierre)');
		if(isset($id) && (is_numeric($id)))
		{
			$this->db->join('Cat_Secciones s3', 's3.nIdSeccion = ' . $id);
			$this->db->where("(Cat_Secciones.cCodigo LIKE " .
				$this->db->concat(array($this->db->varchar('s3.cCodigo'), "'.%'")) . "
				OR Cat_Secciones.nIdSeccion = {$id})");
		}
		if(isset($fecha) && $fecha != '')
		{
			$fecha = format_mssql_date($fecha);
			$this->db->where("Doc_AlbaranesEntrada.dCierre < " . $this->db->dateadd('d', 1, $fecha));
		}
		$query = $this->db->get();
		$data = $this->_get_results($query);
		$data = $this->obj->utils->meses_datos($data);
		$this->_count = count($data);
		return $data;
	}

	/**
	 * Devoluciones por meses y series
	 *
	 * @param int $anno Año
	 * @param int $id Id de la sección
	 * @param date $fecha Fecha límite
	 * @return array
	 */
	function devoluciones_meses($anno, $id =null, $fecha =null)
	{
		$this->db->flush_cache();
		$this->db->select('MONTH(Doc_Devoluciones.dCierre) mes')
		->select_sum('ISNULL(Doc_LineasDevolucion.fCoste*Doc_LineasDevolucion.nCantidad, 0)', 'importe')
		->from("Doc_Devoluciones")
		->join('Doc_LineasDevolucion', 'Doc_LineasDevolucion.nIdDevolucion = Doc_Devoluciones.nIdDevolucion')
		->join('Cat_Secciones', "Cat_Secciones.nIdSeccion = Doc_LineasDevolucion.nIdSeccion")
		->where('YEAR(Doc_Devoluciones.dCierre)', (int)$anno)
		->group_by('MONTH(Doc_Devoluciones.dCierre)');
		if(isset($id) && (is_numeric($id)))
		{
			$this->db->join('Cat_Secciones s3', 's3.nIdSeccion = ' . $id);
			$this->db->where("(Cat_Secciones.cCodigo LIKE  " .
				$this->db->concat(array($this->db->varchar('s3.cCodigo'), "'.%'")) . "
			OR Cat_Secciones.nIdSeccion = {$id})");
		}
		if(isset($fecha) && $fecha != '')
		{
			$fecha = format_mssql_date($fecha);
			$this->db->where("Doc_Devoluciones.dCierre < " . $this->db->dateadd('d', 1, $fecha));
		}
		$query = $this->db->get();
		$data = $this->_get_results($query);
		$data = $this->obj->utils->meses_datos($data);
		$this->_count = count($data);
		#var_dump($data);
		return $data;
	}

	/**
	 * Movimientos entre secciones
	 *
	 * @param int $anno Año
	 * @param int $id Id de la sección
	 * @param date $fecha Fecha límite
	 * @param string $field Nombre del campo (origen o destino)
	 * @return array
	 */
	protected function _movimientos_meses($anno, $id =null, $fecha =null, $field)
	{
		$this->db->flush_cache();
		$this->db->select('MONTH(Doc_Movimientos.dCreacion) mes')
		->select_sum('Doc_Movimientos.nCantidad * Doc_Movimientos.fCoste', 'importe')
		->from("Doc_Movimientos")
		->join('Cat_Secciones', "Cat_Secciones.nIdSeccion = Doc_Movimientos.{$field}")
		->where("YEAR(Doc_Movimientos.dCreacion)=$anno")
		->group_by('MONTH(Doc_Movimientos.dCreacion)');
		if(isset($id) && (is_numeric($id)))
		{
			$this->db->join('Cat_Secciones s3', 's3.nIdSeccion = ' . $id);
			$this->db->where("(Cat_Secciones.cCodigo LIKE " .
				$this->db->concat(array($this->db->varchar('s3.cCodigo'), "'.%'")) . "
			OR Cat_Secciones.nIdSeccion = {$id})");
		}
		if(isset($fecha) && $fecha != '')
		{
			$fecha = format_mssql_date($fecha);
			$this->db->where("Doc_Movimientos.dCreacion < " . $this->db->dateadd('d', 1, $fecha));
		}
		$query = $this->db->get();
		$data = $this->_get_results($query);
		$data = $this->obj->utils->meses_datos($data);
		$this->_count = count($data);
		#var_dump($data);
		return $data;
	}

	/**
	 * Movimientos de origen por meses y series
	 *
	 * @param int $anno Año
	 * @param int $id Id de la sección
	 * @param date $fecha Fecha límite
	 * @return array
	 */
	function movimientos_origen_meses($anno, $id =null, $fecha =null)
	{
		return $this->_movimientos_meses($anno, $id, $fecha, 'nIdSeccionOrigen');
	}

	/**
	 * Movimientos destino por meses y series
	 *
	 * @param int $anno Año
	 * @param int $id Id de la sección
	 * @param date $fecha Fecha límite
	 * @return array
	 */
	function movimientos_destino_meses($anno, $id =null, $fecha =null)
	{
		return $this->_movimientos_meses($anno, $id, $fecha, 'nIdSeccionDestino');
	}

	/**
	 * Ventas por series/meses
	 * @param int $year Año a calcular
	 * @return array
	 */
	function ventas_series($year)
	{
		set_time_limit(0);
		$this->db->flush_cache();
		$this->db->select('nIdSerie, cDescripcion, nNumero')->from('Doc_Series')->order_by('cDescripcion');
		$query = $this->db->get();
		$series = $this->_get_results($query);
		foreach($series as $serie)
		{
			$this->db->flush_cache();
			$this->db->select('MONTH(dFecha) nMes')
			->select_sum($this->db->numeric('la.fPrecio * la.nCantidad') . ' - ' .
				$this->db->numeric('la.fPrecio * la.nCantidad * (ISNULL(la.fDescuento,0)/100)'), 'fImporte')
			->from('Doc_Facturas f')
			->join('Doc_AlbaranesSalida a', 'f.nIdFactura = a.nIdFactura')
			->join('Doc_LineasAlbaranesSalida la', 'la.nIdAlbaran = a.nIdAlbaran')
			->where("YEAR(f.dFecha) = {$year}")
			->where('f.nIdEstado IN (2,3)')			
			->where("f.nIdSerie = {$serie['nIdSerie']}")
			->group_by('MONTH(dFecha)');
			$query = $this->db->get();
			$ventas = $this->_get_results($query);
			foreach($ventas as $d)
			{
				$data[$serie['cDescripcion']]['data'][$d['nMes']] = $d['fImporte'];
			}
			$data[$serie['cDescripcion']]['serie'] = $serie;
		}
		#print '<pre>'; print_r($data); echo '</pre>'; die();
		return $data;
	}

	/**
	 * Comparativa de las ventas a 2 fechas
	 *
	 * @param date $fecha1 Fecha inicial
	 * @param date $fecha2 Fecha final
	 * @return array
	 */
	function comparativa_ventas($fecha1, $fecha2)
	{
		$c_id = 'comparativa_ventas' . $fecha1 . $fecha2;
		// Caché
		if(($this->config->item('bp.cache.data')) && ($r = $this->cache->get($c_id, 'oltpdata')))
		{
			echo $r;
			return ;
		}
		set_time_limit(0);
		//Ventas
		$mes1 = $this->_comparativa_ventas($fecha1, true);
		$mes2 = $this->_comparativa_ventas($fecha2, true);
		$anno1 = $this->_comparativa_ventas($fecha1, false);
		$anno2 = $this->_comparativa_ventas($fecha2, false);
		// Áreas
		$this->db->flush_cache();
		$this->db->select('nIdArea, cNombre Area')->from("Gen_Areas Lineas")->order_by('cNombre');
		$query = $this->db->get();
		$areas = $this->_get_results($query);
		$data = array();
		$f_mes1 = 0;
		$f_anno1 = 0;
		$f_mes2 = 0;
		$f_anno2 = 0;
		foreach($areas as $area)
		{
			// Series
			$this->db->flush_cache();
			$this->db->select('s.nIdSerie, s.cDescripcion Serie, nNumero')
			->from("Doc_Series s")
			->join("Gen_AreasSerie t", 's.nIdSerie = t.nIdSerie')
			->where('t.nIdArea', (int)$area['nIdArea'])
			->order_by('s.cDescripcion');
			$query = $this->db->get();
			$series = $this->_get_results($query);
			$s2 = array();
			$t_mes1 = 0;
			$t_anno1 = 0;
			$t_mes2 = 0;
			$t_anno2 = 0;
			foreach($series as $s)
			{
				$id = $s['nIdSerie'];
				$s['mes1'] = (isset($mes1[$id]) ? $mes1[$id] : 0);
				$s['mes2'] = (isset($mes2[$id]) ? $mes2[$id] : 0);
				$s['m_diff'] = $s['mes1']['fImporte'] - $s['mes2']['fImporte'];
				$s['anno1'] = (isset($anno1[$id]) ? $anno1[$id] : 0);
				$s['anno2'] = (isset($anno2[$id]) ? $anno2[$id] : 0);
				$s['a_diff'] = $s['anno1']['fImporte'] - $s['anno2']['fImporte'];
				$s2[] = $s;
				$t_mes1 += $s['mes1']['fImporte'];
				$t_mes2 += $s['mes2']['fImporte'];
				$t_anno1 += $s['anno1']['fImporte'];
				$t_anno2 += $s['anno2']['fImporte'];
			}
			//var_dump($s2);die();
			$area['series'] = $s2;
			$area['mes1'] = $t_mes1;
			$area['anno1'] = $t_anno1;
			$area['mes2'] = $t_mes2;
			$area['anno2'] = $t_anno2;
			$area['m_diff'] = $t_mes1 - $t_mes2;
			$area['a_diff'] = $t_anno1 - $t_anno2;
			$data[] = $area;
			$f_mes1 += $t_mes1;
			$f_mes2 += $t_mes2;
			$f_anno1 += $t_anno1;
			$f_anno2 += $t_anno2;
		}
		$final['areas'] = $data;
		$final['mes1'] = $f_mes1;
		$final['anno1'] = $f_anno1;
		$final['mes2'] = $f_mes2;
		$final['anno2'] = $f_anno2;
		$final['m_diff'] = $f_mes1 - $f_mes2;
		$final['a_diff'] = $f_anno1 - $f_anno2;
		//Caché
		if($this->config->item('bp.cache.data'))
		{
			$this->cache->save($c_id, $final, 'oltpdata', CACHE_DAY);
		}
		return $final;
	}

	/**
	 * Genera la comparativa de las ventas a una fecha
	 *
	 * @param date $fecha Fecha
	 * @param bool $mes true: agrupa por mes, false: agrupa por año
	 * @return array
	 */
	private function _comparativa_ventas($fecha, $mes =false)
	{
		$m = date('n', $fecha);
		$y = date('Y', $fecha);
		$fecha = format_mssql_date($fecha);
		$this->db->flush_cache();
		$this->db->select('f.nIdSerie')
		->select_sum('la.nCantidad', 'nLibros')
		->select_sum($this->db->numeric('la.fPrecio * la.nCantidad') . ' - ' .
			$this->db->numeric('la.fPrecio * la.nCantidad * (ISNULL(la.fDescuento,0)/100)'), 'fImporte')
		->from('Doc_Facturas f')
		->join('Doc_AlbaranesSalida a', 'f.nIdFactura = a.nIdFactura')
		->join('Doc_LineasAlbaranesSalida la', 'la.nIdAlbaran = a.nIdAlbaran')
		->where("YEAR(f.dFecha) = {$y}")
		->where("f.dFecha < " . $this->db->dateadd('d', 1, $fecha))
		->where('f.nIdEstado IN (2,3)')
		->group_by('f.nIdSerie');
		if($mes)
			$this->db->where("Month(f.dFecha) = {$m}");
		$query = $this->db->get();
		$data = $this->_get_results($query);
		// Índice la serie
		$data2 = array();
		foreach($data as $d)
		{
			$data2[$d['nIdSerie']] = $d;
		}
		return $data2;
	}

	/**
	 * Antigüedad del stock de una sección a una fecha dada
	 *
	 * @param int $id Id de la sección
	 * @param date $fecha Fecha
	 * @return array
	 */
	function antiguedad_seccion($id, $fecha)
	{
		$c_id = 'antiguedad_seccion' . $id . $fecha;
		// Caché
		if(($this->config->item('bp.cache.data')) && ($r = $this->cache->get($c_id, 'oltpdata')))
		{
			echo $r;
			return ;
		}
		set_time_limit(0);
		//@todo Falta comprobar que sale al indicar una sección
		$fecha = format_mssql_date($fecha);
		$this->db->flush_cache();
		$this->db->select_max('nIdVolcado')
		->from("{$this->_prefix}Ext_AntiguedadStockVolcados")
		->where("dCreacion >= {$fecha}")
		->where("dCreacion < " . $this->db->dateadd('d', 1, $fecha));
		$query = $this->db->get();

		if($query)
		{
			$volcado = $query->row_array();
			$volcado = $volcado['nIdVolcado'];
			if(isset($volcado))
			{
				if(is_numeric($id))
				{
					$this->obj->load->model('generico/m_seccion');
					$sec = $this->obj->m_seccion->load($id);
					$cod = $sec['cCodigo'];

					$this->db->select('s3.nIdSeccion, s3.cNombre cSeccion,
					SUM(nStockFirme) StockTotal,
					SUM(fImporte1) + SUM(fImporte2) + SUM(fImporte3) + SUM(fImporte4) ImporteTotal,
					SUM(fFirme1) Firme1,
					SUM(fImporte1) Importe1,
					SUM(fFirme2) Firme2,
					SUM(fImporte2) Importe2,
					SUM(fFirme3) Firme3,
					SUM(fImporte3) Importe3,
					SUM(fFirme4) Firme4,
					SUM(fImporte4) Importe4')
					->from("{$this->_prefix}Ext_AntiguedadStockSecciones s")
					->join('Cat_Secciones s3', "s.nIdSeccion = s3.nIdSeccion")
					->where('s.nIdVolcado', (int)$volcado)
					->where("(s3.cCodigo LIKE '{$cod}.%' OR s3.nIdSeccion = {$id})")
					->group_by('s3.nIdSeccion, s3.cNombre, s.nIdVolcado')
					->order_by('s3.cNombre');
				}
				else
				{
					$this->db->select('s2.nIdSeccion, s2.cNombre cSeccion,
					SUM(nStockFirme) StockTotal,
					SUM(fImporte1) + SUM(fImporte2) + SUM(fImporte3) + SUM(fImporte4) ImporteTotal,
					SUM(fFirme1) Firme1,
					SUM(fImporte1) Importe1,
					SUM(fFirme2) Firme2,
					SUM(fImporte2) Importe2,
					SUM(fFirme3) Firme3,
					SUM(fImporte3) Importe3,
					SUM(fFirme4) Firme4,
					SUM(fImporte4) Importe4')
					->from("{$this->_prefix}Ext_AntiguedadStockSecciones s")
					->join('Cat_Secciones s3', "s.nIdSeccion = s3.nIdSeccion")
					->join('Cat_Secciones s2', 's2.nIdSeccionPadre IS NULL')
					->where('s.nIdVolcado', (int)$volcado)
					->where("(s3.cCodigo LIKE " . $this->db->concat(array($this->db->varchar('s2.cCodigo'), "'.%'")) . ")")
					->group_by('s2.nIdSeccion, s2.cNombre, s.nIdVolcado')
					->order_by('s2.cNombre');
				}
				$query = $this->db->get();
				$data = $this->_get_results($query);
				//Caché
				if($this->config->item('bp.cache.data'))
				{
					$this->cache->save($c_id, $data, 'oltpdata', CACHE_DAY);
				}
				return $data;
			}
		}
		return null;
	}

	/**
	 * Antigüedad del stock de una sección a una fecha dada
	 *
	 * @param int $id Id de la sección
	 * @param date $fecha Fecha
	 * @return array
	 */
	function antiguedad_seccion_desglose($id, $fecha, $order, $tipo)
	{
		set_time_limit(0);

		$fecha = format_mssql_date($fecha);
		$this->db->flush_cache();
		$this->db->select_max('nIdVolcado')
		->from("{$this->_prefix}Ext_AntiguedadStockVolcados")
		->where("dCreacion >= {$fecha}")
		->where("dCreacion < " . $this->db->dateadd('d', 1, $fecha));
		$query = $this->db->get();
		if($query)
		{
			$volcado = $query->row_array();
			$volcado = $volcado['nIdVolcado'];
			$order = str_replace(array('cSeccion','cProveedor'), array('s.cSeccion', 'Prv_Proveedores.cEmpresa, Prv_Proveedores.cNombre, Prv_Proveedores.cApellido'), $order);
			if(isset($volcado))
			{
				if(is_numeric($id))
				{
					$this->obj->load->model('generico/m_seccion');
					$sec = $this->obj->m_seccion->load($id);
					$cod = $sec['cCodigo'];

					$this->db->select('s.nIdSeccion, s.cSeccion cSeccion,
					s.nStockFirme StockTotal,
					fImporte1 + fImporte2 + fImporte3 + fImporte4 ImporteTotal,
					fFirme1 Firme1,
					fImporte1 Importe1,
					fFirme2 Firme2,
					fImporte2 Importe2,
					fFirme3 Firme3,
					fImporte3 Importe3,
					fFirme4 Firme4,
					fImporte4 Importe4')
					->select('Cat_Fondo.cISBN, Cat_Fondo.nIdLibro, Cat_Fondo.cTitulo, Cat_Fondo.cAutores')
					->select('Prv_Proveedores.cNombre, Prv_Proveedores.cApellido, Prv_Proveedores.cEmpresa')
					->from("{$this->_prefix}Ext_AntiguedadStockSecciones s")
					->join('Cat_Secciones s3', "s.nIdSeccion = s3.nIdSeccion")
					->join('Cat_Fondo', 'Cat_Fondo.nIdLibro=s.nIdLibro')
					->join('Cat_Editoriales', 'Cat_Fondo.nIdEditorial=Cat_Editoriales.nIdEditorial', 'left')
					->join('Prv_Proveedores', 'Prv_Proveedores.nIdProveedor=ISNULL(Cat_Fondo.nIdProveedor, Cat_Editoriales.nIdProveedor)')
					->where('s.nIdVolcado', (int)$volcado)
					->where("(s3.cCodigo LIKE '{$cod}.%' OR s3.nIdSeccion = {$id})")
					->order_by($order);
				}
				else
				{
					$this->db->select('s2.nIdSeccion, s2.cNombre cSeccion,
					(s.nStockFirme) StockTotal,
					(fImporte1) + (fImporte2) + (fImporte3) + (fImporte4) ImporteTotal,
					(fFirme1) Firme1,
					(fImporte1) Importe1,
					(fFirme2) Firme2,
					(fImporte2) Importe2,
					(fFirme3) Firme3,
					(fImporte3) Importe3,
					(fFirme4) Firme4,
					(fImporte4) Importe4')
					->select('Cat_Fondo.cISBN, Cat_Fondo.nIdLibro, Cat_Fondo.cTitulo, Cat_Fondo.cAutores')
					->select('Prv_Proveedores.cNombre, Prv_Proveedores.cApellido, Prv_Proveedores.cEmpresa')
					->from("{$this->_prefix}Ext_AntiguedadStockSecciones s")
					->join('Cat_Secciones s3', "s.nIdSeccion = s3.nIdSeccion")
					->join('Cat_Secciones s2', 's2.nIdSeccionPadre IS NULL')
					->join('Cat_Fondo', 'Cat_Fondo.nIdLibro=s.nIdLibro')
					->join('Cat_Editoriales', 'Cat_Fondo.nIdEditorial=Cat_Editoriales.nIdEditorial', 'left')
					->join('Prv_Proveedores', 'Prv_Proveedores.nIdProveedor=ISNULL(Cat_Fondo.nIdProveedor, Cat_Editoriales.nIdProveedor)')
					->where('s.nIdVolcado', (int)$volcado)
					->where("(s3.cCodigo LIKE " . $this->db->concat(array($this->db->varchar('s2.cCodigo'), "'.%'")) . ")")
					->group_by('s2.nIdSeccion, s2.cNombre, s.nIdVolcado')
					->order_by($order);
				}
				switch ($tipo) {
					case 1:
						$this->db->where('fFirme2>0');
						break;
					case 2:
						$this->db->where('fFirme3>0');
						break;
					case 3:
						$this->db->where('fFirme4>0');
						break;
				}
				$query = $this->db->get();
				$data = $this->_get_results($query);

				return $data;
			}
		}
		return null;
	}

	/**
	 * Calcula el valor de depreación de los importes indicados
	 *
	 * @param float $v1 Importe de 0 a 1 año
	 * @param float $v2 Importe de 1 a 2 años
	 * @param float $v3 Importe de 2 a 3 años
	 * @param float $v4 Importe de 3 o más años
	 * @return float
	 */
	function depreciar($v1, $v2, $v3, $v4)
	{
		return $v1 * $this->config->item('bp.oltp.depreciacion1') + $v2 * $this->config->item('bp.oltp.depreciacion2') + $v3 * $this->config->item('bp.oltp.depreciacion3') + $v4 * $this->config->item('bp.oltp.depreciacion4');
	}

	/**
	 * Genera los cobros de por caja, día y modo de pago
	 *
	 * @param datatime $fecha1 Fecha inicial
	 * @param datatime $fecha2 Fecha final
	 * @param int $caja Id de la caja
	 * @return array
	 */
	function caja_dia_modo($fecha1, $fecha2, $caja =null, $modo =null)
	{
		set_time_limit(0);
		$fecha1 = format_mssql_date($fecha1);
		$fecha2 = format_mssql_date($fecha2);
		$this->db->flush_cache();
		$this->db->from('Doc_FacturasModosPago')
		->join('Doc_Facturas', 'Doc_Facturas.nIdFactura = Doc_FacturasModosPago.nIdFactura')
		->join('Gen_Cajas', 'Gen_Cajas.nIdCaja = Doc_FacturasModosPago.nIdCaja')
		->join('Gen_ModosPago', 'Doc_FacturasModosPago.nIdModoPago = Gen_ModosPago.nIdModoPago')
		->where("Doc_FacturasModosPago.dFecha >= {$fecha1}")
		->where("Doc_Facturas.dFecha < " . $this->db->dateadd('d', 1, $fecha2))
		->where("Doc_Facturas.nIdEstado IN (2, 3)");
		if(isset($caja))
			$this->db->where('Doc_FacturasModosPago.nIdCaja', (int)$caja);
		if(isset($modo))
		{
			$this->db->select($this->_date_field('Doc_FacturasModosPago.dFecha', 'dDia'))
			->select('Gen_ModosPago.cDescripcion, Gen_Cajas.cDescripcion cCaja, Doc_FacturasModosPago.fImporte')
			->select('Doc_FacturasModosPago.nIdFactura')
			->select('Doc_Facturas.nNumero, Doc_Series.nNumero nNumeroSerie')
			->select('Cli_Clientes.nIdCliente, Cli_Clientes.cNombre, Cli_Clientes.cApellido, Cli_Clientes.cEmpresa')
			->join('Cli_Clientes', 'Doc_Facturas.nIdCliente = Cli_Clientes.nIdCliente')
			->join('Doc_Series', 'Doc_Facturas.nIdSerie = Doc_Series.nIdSerie')
			->where('Doc_FacturasModosPago.nIdModoPago', (int)$modo)
			->order_by('Doc_FacturasModosPago.dFecha');
		}
		else
		{
			$this->db->select($this->_date_field($this->db->date('Doc_FacturasModosPago.dFecha') , ' dDia'))
			->select('Gen_ModosPago.cDescripcion, Gen_Cajas.cDescripcion cCaja')
			->select('Gen_ModosPago.nIdModoPago, Gen_Cajas.nIdCaja')
			->select_sum('Doc_FacturasModosPago.fImporte', 'fImporte')
			->group_by($this->_date_field($this->db->date('Doc_FacturasModosPago.dFecha')) . ', Gen_ModosPago.cDescripcion, Gen_Cajas.cDescripcion')
			->group_by('Gen_ModosPago.nIdModoPago, Gen_Cajas.nIdCaja')
			->order_by($this->_date_field($this->db->date('Doc_FacturasModosPago.dFecha')) . ', Gen_Cajas.cDescripcion');
		}
		$query = $this->db->get();
		$data = $this->_get_results($query);
		#echo '<pre>'; print_r($this->db->queries); echo '</pre>'; die();
		if(isset($modo))
			return $data;
		$data2 = array();
		foreach($data as $d)
		{
			$data2['data'][$d['dDia']]['data'][$d['cDescripcion']][$d['cCaja']] = $d['fImporte'];
			$data2['data'][$d['dDia']]['cajas'][$d['cCaja']] = TRUE;
			$data2['modos'][$d['cDescripcion']] = $d['nIdModoPago'];
			$data2['cajas'][$d['cCaja']] = $d['nIdCaja'];
		}
		return $data2;
	}

	/**
	 * Genera los cobros de por caja, día y modo de pago
	 *
	 * @param datatime $fecha1 Fecha inicial
	 * @param datatime $fecha2 Fecha final
	 * @param int $caja Id de la caja
	 * @return array
	 */
	function caja_dia_modo2($fecha1, $fecha2, $caja =null, $modo =null)
	{
		set_time_limit(0);
		$fecha1 = format_mssql_date($fecha1);
		$fecha2 = format_mssql_date($fecha2);
		$this->db->flush_cache();
		$this->db->from('Doc_FacturasModosPago2')
		->join('Doc_Facturas2', 'Doc_Facturas2.nIdFactura = Doc_FacturasModosPago2.nIdFactura')
		->join('Gen_Cajas', 'Gen_Cajas.nIdCaja = Doc_FacturasModosPago2.nIdCaja')
		->join('Gen_ModosPago', 'Doc_FacturasModosPago2.nIdModoPago = Gen_ModosPago.nIdModoPago')
		->where("Doc_FacturasModosPago2.dFecha >= {$fecha1}")
		->where("Doc_Facturas2.dFecha < " . $this->db->dateadd('d', 1, $fecha2))
		->where("Doc_Facturas2.nIdEstado IN (2, 3)");
		if(isset($caja))
			$this->db->where('Doc_FacturasModosPago2.nIdCaja', (int)$caja);
		if(isset($modo))
		{
			$this->db->select($this->_date_field('Doc_FacturasModosPago2.dFecha', 'dDia'))
			->select('Gen_ModosPago.cDescripcion, Gen_Cajas.cDescripcion cCaja, Doc_FacturasModosPago2.fImporte')
			->select('Doc_FacturasModosPago2.nIdFactura')
			->select('Doc_Facturas2.nNumero, Doc_Series.nNumero nNumeroSerie')
			->select('Cli_Clientes.nIdCliente, Cli_Clientes.cNombre, Cli_Clientes.cApellido, Cli_Clientes.cEmpresa')
			->join('Cli_Clientes', 'Doc_Facturas2.nIdCliente = Cli_Clientes.nIdCliente')
			->join('Doc_Series', 'Doc_Facturas2.nIdSerie = Doc_Series.nIdSerie')
			->where('Doc_FacturasModosPago2.nIdModoPago', (int)$modo)
			->order_by('Doc_FacturasModosPago2.dFecha');
		}
		else
		{
			$this->db->select($this->_date_field($this->db->date('Doc_FacturasModosPago2.dFecha') , ' dDia'))
			->select('Gen_ModosPago.cDescripcion, Gen_Cajas.cDescripcion cCaja')
			->select('Gen_ModosPago.nIdModoPago, Gen_Cajas.nIdCaja')
			->select_sum('Doc_FacturasModosPago2.fImporte', 'fImporte')
			->group_by($this->_date_field($this->db->date('Doc_FacturasModosPago2.dFecha')) . ', Gen_ModosPago.cDescripcion, Gen_Cajas.cDescripcion')
			->group_by('Gen_ModosPago.nIdModoPago, Gen_Cajas.nIdCaja')
			->order_by($this->_date_field($this->db->date('Doc_FacturasModosPago2.dFecha')) . ', Gen_Cajas.cDescripcion');
		}
		$query = $this->db->get();
		$data = $this->_get_results($query);
		#echo '<pre>'; print_r($this->db->queries); echo '</pre>'; die();
		if(isset($modo))
			return $data;
		$data2 = array();
		foreach($data as $d)
		{
			$data2['data'][$d['dDia']]['data'][$d['cDescripcion']][$d['cCaja']] = $d['fImporte'];
			$data2['data'][$d['dDia']]['cajas'][$d['cCaja']] = TRUE;
			$data2['modos'][$d['cDescripcion']] = $d['nIdModoPago'];
			$data2['cajas'][$d['cCaja']] = $d['nIdCaja'];
		}
		return $data2;
	}

	/**
	 * Ventas en un periodo muestra secciones
	 * Usa caché
	 *
	 * @param datatime $fecha1 Fecha inicial
	 * @param datatime $fecha2 Fecha final
	 * @param int $idserie Id de la serie
	 * @param int $idseccion Id de la sección
	 * @return array
	 */
	function ventas_periodo_libros($fecha1, $fecha2, $idserie =null, $idseccion =null, $idarea =null, $cmpdto =null, $dto =null, $cmpmargen =null, $margen =null, $idcliente =null)
	{
		#die('ventas periodo libros');
		$c_id = 'ventas_periodo' . $fecha1 . $fecha2 . $idserie . $idseccion . $cmpdto . $cmpmargen . $dto . $margen . $idcliente;
		// Caché
		if(($this->config->item('bp.cache.data')) && ($r = $this->cache->get($c_id, 'oltpdata')))
		{
			echo $r;
			return ;
		}
		set_time_limit(0);
		//Filtro fecha
		$fecha1 = format_mssql_date($fecha1);
		$fecha2 = format_mssql_date($fecha2);
		$this->db->flush_cache();
		$this->db->select('a.nIdArea, a.cNombre cArea, s.nIdSerie, s.cDescripcion cSerie')
		->select('la.fDescuento, la.nIdLibro, ABS(la.nCantidad) * la.fCoste fCoste, la.nCantidad nLibros')
		->select($this->db->numeric('la.fPrecio * la.nCantidad') . ' - ' .
			$this->db->numeric('la.fPrecio * la.nCantidad * (ISNULL(la.fDescuento,0)/100)'), 'fImporte')
		->select('fd.cTitulo, f.nNumero, f.dFecha, s.nNumero nNumeroSerie, f.nIdFactura')
		->select($this->_date_field('f.dFecha', 'dFecha'))
		->select('cl.cNombre, cl.cApellido, cl.cEmpresa, cl.nIdCliente')
		->from('Doc_Facturas f')
		->join('Cli_Clientes cl', 'cl.nIdCliente = f.nIdCliente')
		->join('Doc_AlbaranesSalida alb', 'f.nIdFactura = alb.nIdFactura')
		->join('Doc_LineasAlbaranesSalida la', 'la.nIdAlbaran = alb.nIdAlbaran')
		->join('Cat_Fondo fd', 'fd.nIdLibro = la.nIdLibro')
		->join('Doc_Series s', 's.nIdSerie = f.nIdSerie')
		->join('Gen_AreasSerie t', 's.nIdSerie = t.nIdSerie')
		->join('Gen_Areas a', 'a.nIdArea = t.nIdArea')
		->where("f.dFecha >= {$fecha1}")
		->where("f.dFecha < " . $this->db->dateadd('d', 1, $fecha2))
		->where("f.nIdEstado <> 1")
		->order_by('a.cNombre, s.cDescripcion');
		if(isset($idseccion))
		{
			$this->db->select('s2.cNombre cSeccion, s2.nIdSeccion')
			->join('Cat_Secciones s2', 's2.nIdSeccion = la.nIdSeccion')
			->join('Cat_Secciones s3', "s3.nIdSeccion ={$idseccion} AND (s2.nIdSeccion = s3.nIdSeccion  OR s2.cCodigo LIKE " .
				$this->db->concat(array($this->db->varchar('s3.cCodigo'), "'.%'")) . ")")
			->order_by('s2.cNombre');
		}
		else
		{
			$this->db->select('s3.cNombre cSeccion, s3.nIdSeccion')
			->join('Cat_Secciones s2', 's2.nIdSeccion = la.nIdSeccion')
			->join('Cat_Secciones s3', 's3.nIdSeccion = s2.nIdSeccion AND (s3.nIdSeccion = s2.nIdSeccion OR s2.cCodigo LIKE ' .
				$this->db->concat(array($this->db->varchar('s3.cCodigo'), "'.%'")) . ')')
			->order_by('s3.cNombre');
		}
		if(isset($idserie))
			$this->db->where('s.nIdSerie', (int)$idserie);
		if(isset($idarea))
			$this->db->where('a.nIdArea', (int)$idarea);
		if(isset($idcliente))
			$this->db->where('f.nIdCliente', (int)$idcliente);
		$this->db->order_by('f.dFecha, f.nNumero, fd.cTitulo');
		// Filtro Descuento
		if(isset($dto) && (isset($cmpdto)) && ($cmpdto != ''))
		{
			$this->db->where("la.fDescuento {$cmpdto} {$dto}");
		}
		// Filtro margen
		if(isset($margen) && (isset($cmpmargen)) && ($cmpmargen != ''))
		{
			$this->db->where("(1 - (la.fCoste/(la.fPrecio * (la.fDescuento/100)))*100){$cmpmargen} {$margen}");
		}
		$query = $this->db->get();
		$data = $this->_get_results($query);
		//Crea el array
		$data2 = array();
		foreach($data as $d)
		{
			$data2[$d['cArea']][$d['cSerie']][$d['cSeccion']][] = $d;
		}
		//Caché
		if($this->config->item('bp.cache.data'))
		{
			$this->cache->save($c_id, $data2, 'oltpdata', CACHE_DAY);
		}
		#echo '<pre>'; print_r($data2); echo '</pre>';
		return $data2;
	}

	/**
	 * Compreas en un periodo secciones
	 * Usa caché
	 *
	 * @param datatime $fecha1 Fecha inicial
	 * @param datatime $fecha2 Fecha final
	 * @param int $idseccion Id de la sección
	 * @return array
	 */
	function compras_periodo_secciones($fecha1, $fecha2, $idseccion =null, $idproveedor =null)
	{
		$c_id = 'ventas_periodo_secciones' . $fecha1 . $fecha2 .  $idseccion . $idproveedor;
		// Caché
		/*
		 if (($this->config->item('bp.cache.data')) && ($r = $this->cache->get($c_id,
		 'oltpdata')))
		 {
		 echo $r;
		 return;
		 }*/
		set_time_limit(0);
		$fecha1 = format_mssql_date($fecha1);
		$fecha2 = format_mssql_date($fecha2);
		if(isset($idseccion))
		{
			$this->obj->load->model('generico/m_seccion');
			$sec = $this->obj->m_seccion->load($idseccion);
			$cod = $sec['cCodigo'];
		}
		$this->db->flush_cache();
		$this->db
		->select_sum('Doc_LineasAlbaranesEntrada.nCantidad', 'nLibros')
		->select_sum('ABS(Doc_LineasAlbaranesEntrada.nCantidad) * Doc_LineasAlbaranesEntrada.fCoste', 'fCoste')
		->select('AVG(Doc_LineasAlbaranesEntrada.fDescuento) fDescuento')
		->from('Doc_AlbaranesEntrada')
		->join('Doc_LineasAlbaranesEntrada', 'Doc_LineasAlbaranesEntrada.nIdAlbaran = Doc_AlbaranesEntrada.nIdAlbaran')
		->join('Doc_LineasPedidosRecibidas', 'Doc_LineasPedidosRecibidas.nIdLineaAlbaran = Doc_LineasAlbaranesEntrada.nIdLinea')
		->join('Doc_LineasPedidoProveedor', 'Doc_LineasPedidosRecibidas.nIdLineaPedido = Doc_LineasPedidoProveedor.nIdLinea')
		->where("Doc_AlbaranesEntrada.dCierre >= {$fecha1}")
		->where("Doc_AlbaranesEntrada.dCierre < " . $this->db->dateadd('d', 1, $fecha2))
		->where("Doc_AlbaranesEntrada.nIdEstado <> 1");
		
		if(isset($idseccion))
		{
			$this->db->select('s2.cNombre cSeccion, s2.nIdSeccion')
			->join('Cat_Secciones s2', 's2.nIdSeccion = Doc_LineasPedidoProveedor.nIdSeccion')
			->join('Cat_Secciones s3', "s2.nIdSeccion = s3.nIdSeccion")
			->where("(s3.cCodigo LIKE '{$cod}.%' OR s3.nIdSeccion = {$idseccion})")
			->group_by('s2.cNombre, s2.nIdSeccion')
			->order_by('s2.cNombre');
		}
		else
		{
			$this->db->select('s3.cNombre cSeccion, s3.nIdSeccion')
			->join('Cat_Secciones s3', 's3.nIdSeccion = Doc_LineasPedidoProveedor.nIdSeccion')
			->group_by('s3.cNombre, s3.nIdSeccion')
			->order_by('s3.cNombre');
		}
		if(isset($idproveedor))
			$this->db->where('Doc_AlbaranesEntrada.nIdProveedor', (int)$idproveedor);
		$query = $this->db->get();
		$data = $this->_get_results($query);
		//Crea el array
		/*$data2 = array();
		foreach($data as $d)
		{
			if ($d['fImporte'] != 0) $data2[$d['cArea']][$d['cSerie']][$d['cSeccion']][] = $d;
		}*/
		//Caché
		/*if ($this->config->item('bp.cache.data'))
		 {
		 $this->cache->save($c_id, $data2, 'oltpdata', CACHE_DAY);
		 }*/
		#echo '<pre>'; print_r($data); echo '</pre>'; 
		#echo '<pre>'; print_r($data2); echo '</pre>'; die();
		return $data;
	}

	/**
	 * Compras en un periodo secciones
	 * Usa caché
	 *
	 * @param datatime $fecha1 Fecha inicial
	 * @param datatime $fecha2 Fecha final
	 * @param int $idseccion Id de la sección
	 * @return array
	 */
	function compras_periodo_libros($fecha1, $fecha2, $idseccion =null, $idproveedor =null)
	{
		$c_id = 'ventas_periodo_libros' . $fecha1 . $fecha2 .  $idseccion . $idproveedor;
		// Caché
		/*
		 if (($this->config->item('bp.cache.data')) && ($r = $this->cache->get($c_id,
		 'oltpdata')))
		 {
		 echo $r;
		 return;
		 }*/
		set_time_limit(0);
		$fecha1 = format_mssql_date($fecha1);
		$fecha2 = format_mssql_date($fecha2);
		if(isset($idseccion))
		{
			$this->obj->load->model('generico/m_seccion');
			$sec = $this->obj->m_seccion->load($idseccion);
			$cod = $sec['cCodigo'];
		}
		$this->db->flush_cache();
		$this->db
		->select('Doc_LineasAlbaranesEntrada.nCantidad nLibros')
		->select('ABS(Doc_LineasAlbaranesEntrada.nCantidad) * Doc_LineasAlbaranesEntrada.fCoste fCoste')
		->select('Doc_LineasAlbaranesEntrada.fDescuento fDescuento')
		->select('Cat_Fondo.nIdLibro, Cat_Fondo.cTitulo, Cat_Fondo.cISBN')
		->select('Doc_AlbaranesEntrada.nIdAlbaran')
		->select($this->_date_field('Doc_AlbaranesEntrada.dCierre', 'dCierre'))
		->select('Prv_Proveedores.nIdProveedor,Prv_Proveedores.cNombre,Prv_Proveedores.cApellido,Prv_Proveedores.cEmpresa')
		->select('Cat_Editoriales.cNombre cEditorial')
		->from('Doc_AlbaranesEntrada')
		->join('Doc_LineasAlbaranesEntrada', 'Doc_LineasAlbaranesEntrada.nIdAlbaran = Doc_AlbaranesEntrada.nIdAlbaran')
		->join('Doc_LineasPedidosRecibidas', 'Doc_LineasPedidosRecibidas.nIdLineaAlbaran = Doc_LineasAlbaranesEntrada.nIdLinea')
		->join('Doc_LineasPedidoProveedor', 'Doc_LineasPedidosRecibidas.nIdLineaPedido = Doc_LineasPedidoProveedor.nIdLinea')
		->join('Cat_Fondo', 'Cat_Fondo.nIdLibro = Doc_LineasPedidoProveedor.nIdLibro')
		->join('Prv_Proveedores', 'Prv_Proveedores.nIdProveedor = Doc_AlbaranesEntrada.nIdProveedor')
		->join('Cat_Editoriales', 'Cat_Editoriales.nIdEditorial=Cat_Fondo.nIdEditorial', 'left')
		->where("Doc_AlbaranesEntrada.dCierre >= {$fecha1}")
		->where("Doc_AlbaranesEntrada.dCierre < " . $this->db->dateadd('d', 1, $fecha2))
		->where("Doc_AlbaranesEntrada.nIdEstado <> 1");
		
		if(isset($idseccion))
		{
			$this->db->select('s2.cNombre cSeccion, s2.nIdSeccion')
			->join('Cat_Secciones s2', 's2.nIdSeccion = Doc_LineasPedidoProveedor.nIdSeccion')
			->join('Cat_Secciones s3', "s2.nIdSeccion = s3.nIdSeccion")
			->where("(s3.cCodigo LIKE '{$cod}.%' OR s3.nIdSeccion = {$idseccion})")
			->order_by('s2.cNombre');
		}
		else
		{
			$this->db->select('s3.cNombre cSeccion, s3.nIdSeccion')
			->join('Cat_Secciones s3', 's3.nIdSeccion = Doc_LineasPedidoProveedor.nIdSeccion')
			->order_by('s3.cNombre');
		}
		if(isset($idproveedor))
			$this->db->where('Doc_AlbaranesEntrada.nIdProveedor', (int)$idproveedor);
		$query = $this->db->get();
		$data = $this->_get_results($query);
		//Caché
		/*if ($this->config->item('bp.cache.data'))
		 {
		 $this->cache->save($c_id, $data2, 'oltpdata', CACHE_DAY);
		 }*/
		return $data;
	}

	/**
	 * Compras en un periodo secciones sin albarán
	 *
	 * @param datatime $fecha1 Fecha inicial
	 * @param datatime $fecha2 Fecha final
	 * @param int $idseccion Id de la sección
	 * @return array
	 */
	function compras_periodo_libros_sin($fecha1, $fecha2, $idseccion =null, $idproveedor =null)
	{
		$c_id = 'ventas_periodo_libros' . $fecha1 . $fecha2 .  $idseccion . $idproveedor;

		set_time_limit(0);
		$fecha1 = format_mssql_date($fecha1);
		$fecha2 = format_mssql_date($fecha2);
		if(isset($idseccion))
		{
			$this->obj->load->model('generico/m_seccion');
			$sec = $this->obj->m_seccion->load($idseccion);
			$cod = $sec['cCodigo'];
		}
		$this->db->flush_cache();
		$this->db
		->select('SUM(Doc_LineasAlbaranesEntrada.nCantidad) nLibros')
		->select('Cat_Fondo.nIdLibro, Cat_Fondo.cTitulo, Cat_Fondo.cISBN')
		->select('Prv_Proveedores.nIdProveedor,Prv_Proveedores.cNombre,Prv_Proveedores.cApellido,Prv_Proveedores.cEmpresa')
		->select('Cat_Editoriales.cNombre cEditorial')
		->from('Doc_AlbaranesEntrada')
		->join('Doc_LineasAlbaranesEntrada', 'Doc_LineasAlbaranesEntrada.nIdAlbaran = Doc_AlbaranesEntrada.nIdAlbaran')
		->join('Doc_LineasPedidosRecibidas', 'Doc_LineasPedidosRecibidas.nIdLineaAlbaran = Doc_LineasAlbaranesEntrada.nIdLinea')
		->join('Doc_LineasPedidoProveedor', 'Doc_LineasPedidosRecibidas.nIdLineaPedido = Doc_LineasPedidoProveedor.nIdLinea')
		->join('Cat_Fondo', 'Cat_Fondo.nIdLibro = Doc_LineasPedidoProveedor.nIdLibro')
		->join('Prv_Proveedores', 'Prv_Proveedores.nIdProveedor = Doc_AlbaranesEntrada.nIdProveedor')
		->join('Cat_Editoriales', 'Cat_Editoriales.nIdEditorial=Cat_Fondo.nIdEditorial', 'left')
		->where("Doc_AlbaranesEntrada.dCierre >= {$fecha1}")
		->where("Doc_AlbaranesEntrada.dCierre < " . $this->db->dateadd('d', 1, $fecha2))
		->where("Doc_AlbaranesEntrada.nIdEstado <> 1")
		->order_by('Cat_Fondo.cTitulo')
		->group_by('Cat_Fondo.nIdLibro, Cat_Fondo.cTitulo, Cat_Fondo.cISBN, Cat_Editoriales.cNombre')
		->group_by('Prv_Proveedores.nIdProveedor,Prv_Proveedores.cNombre,Prv_Proveedores.cApellido,Prv_Proveedores.cEmpresa');

		if(isset($idseccion))
		{
			$this->db #->select('s2.cNombre cSeccion, s2.nIdSeccion')
			->join('Cat_Secciones s2', 's2.nIdSeccion = Doc_LineasPedidoProveedor.nIdSeccion')
			->join('Cat_Secciones s3', "s2.nIdSeccion = s3.nIdSeccion")
			->where("(s3.cCodigo LIKE '{$cod}.%' OR s3.nIdSeccion = {$idseccion})");
			#->order_by('s2.cNombre');
		}
		/*else
		{
			$this->db->select('s3.cNombre cSeccion, s3.nIdSeccion')
			->join('Cat_Secciones s3', 's3.nIdSeccion = Doc_LineasPedidoProveedor.nIdSeccion')
			->order_by('s3.cNombre');
		}*/
		if(isset($idproveedor))
			$this->db->where('Doc_AlbaranesEntrada.nIdProveedor', (int)$idproveedor);
		$query = $this->db->get();
		$data = $this->_get_results($query);

		return $data;
	}
	
	/**
	 * Ventas en un periodo secciones
	 * Usa caché
	 *
	 * @param datatime $fecha1 Fecha inicial
	 * @param datatime $fecha2 Fecha final
	 * @param int $idserie Id de la serie
	 * @param int $idseccion Id de la sección
	 * @return array
	 */
	function ventas_periodo_secciones($fecha1, $fecha2, $idserie =null, $idseccion =null, $idarea =null, $idcliente =null)
	{
		$c_id = 'ventas_periodo_secciones' . $fecha1 . $fecha2 . $idserie . $idseccion . $idcliente;
		// Caché
		/*
		 if (($this->config->item('bp.cache.data')) && ($r = $this->cache->get($c_id,
		 'oltpdata')))
		 {
		 echo $r;
		 return;
		 }*/
		set_time_limit(0);
		$fecha1 = format_mssql_date($fecha1);
		$fecha2 = format_mssql_date($fecha2);
		$this->db->flush_cache();
		$this->db->select('a.nIdArea, a.cNombre cArea, s.nIdSerie, s.cDescripcion cSerie')
		->select_sum('la.nCantidad', 'nLibros')
		->select_sum($this->db->numeric('la.fPrecio * la.nCantidad') . ' - ' .
			$this->db->numeric('la.fPrecio * la.nCantidad * (ISNULL(la.fDescuento,0)/100)'), 'fImporte')
		->select_sum('ABS(la.nCantidad) * la.fCoste', 'fCoste')
		->from('Doc_Facturas f')
		->join('Doc_AlbaranesSalida alb', 'f.nIdFactura = alb.nIdFactura')
		->join('Doc_LineasAlbaranesSalida la', 'la.nIdAlbaran = alb.nIdAlbaran')
		->join('Doc_Series s', 's.nIdSerie = f.nIdSerie')
		->join('Gen_AreasSerie t', 's.nIdSerie = t.nIdSerie')
		->join('Gen_Areas a', 'a.nIdArea = t.nIdArea')
		->where("f.dFecha >= {$fecha1}")
		->where("f.dFecha < " . $this->db->dateadd('d', 1, $fecha2))
		->where("f.nIdEstado <> 1")
		->group_by('a.nIdArea, a.cNombre, s.nIdSerie, s.cDescripcion')
		->order_by('a.cNombre, s.cDescripcion');
		if(isset($idseccion))
		{
			$this->db->select('s2.cNombre cSeccion, s2.nIdSeccion')
			->join('Cat_Secciones s2', 's2.nIdSeccion = la.nIdSeccion AND s2.nIdSeccion = ' . $idseccion)
			->group_by('s2.cNombre, s2.nIdSeccion')
			->order_by('s2.cNombre');
		}
		else
		{
			$this->db->select('s3.cNombre cSeccion, s3.nIdSeccion')
			->join('Cat_Secciones s3', 's3.nIdSeccion = la.nIdSeccion')
			->group_by('s3.cNombre, s3.nIdSeccion')
			->order_by('s3.cNombre');
		}
		if(isset($idserie))
			$this->db->where('s.nIdSerie', (int)$idserie);
		if(isset($idarea))
			$this->db->where('a.nIdArea', (int)$idarea);
		if(isset($idcliente))
			$this->db->where('f.nIdCliente', (int)$idcliente);
		$query = $this->db->get();
		$data = $this->_get_results($query);
		//Crea el array
		$data2 = array();
		foreach($data as $d)
		{
			if ($d['fImporte'] != 0) $data2[$d['cArea']][$d['cSerie']][$d['cSeccion']][] = $d;
		}
		//Caché
		/*if ($this->config->item('bp.cache.data'))
		 {
		 $this->cache->save($c_id, $data2, 'oltpdata', CACHE_DAY);
		 }*/
		#echo '<pre>'; print_r($data); echo '</pre>'; 
		#echo '<pre>'; print_r($data2); echo '</pre>'; die();
		return $data2;
	}

	/**
	 * Ventas en un periodo por series
	 * Usa caché
	 *
	 * @param datatime $fecha1 Fecha inicial
	 * @param datatime $fecha2 Fecha final
	 * @return array
	 */
	function ventas_series_periodo($fecha1, $fecha2)
	{
		set_time_limit(0);
		$fecha1 = format_mssql_date($fecha1);
		$fecha2 = format_mssql_date($fecha2);
		$this->db->flush_cache();
		/* (convert(numeric(10,2),([fPrecio] * [nCantidad] 
		- convert(numeric(10,2),([fPrecio] * [nCantidad] * isnull([fDescuento],0) / 100)))))
		*/
		$this->db->select('s.nNumero NumeroSerie, s.cDescripcion Serie')
		->select_sum($this->db->numeric('la.fPrecio * la.nCantidad') . ' - ' .
			$this->db->numeric('la.fPrecio * la.nCantidad * (ISNULL(la.fDescuento,0)/100)'), 'fBase')
		->select_sum('(' . $this->db->numeric('la.fPrecio * la.nCantidad') . ' - ' .
			$this->db->numeric('la.fPrecio * la.nCantidad * (ISNULL(la.fDescuento,0)/100)') .
		 	') * (la.fIVA / 100)', 'fIVA')
		->select_sum('la.fCoste', 'fCoste')
		->from('Doc_Facturas f')
		->join('Doc_AlbaranesSalida a', 'f.nIdFactura = a.nIdFactura')
		->join('Doc_LineasAlbaranesSalida la', 'a.nIdAlbaran = la.nIdAlbaran')
		->join('Doc_Series s', 's.nIdSerie = f.nIdSerie')
		->where("f.dFecha >= {$fecha1}")
		->where("f.dFecha < " . $this->db->dateadd('d', 1, $fecha2))
		->where('f.nIdEstado IN (2,3)')
		->group_by('s.nNumero, s.cDescripcion')
		->order_by('s.cDescripcion');
		 
		$query = $this->db->get();
		$data = $this->_get_results($query);
		return $data;
	}

	/**
	 * Ventas sin IVA
	 * @param date $fecha1 Desde
	 * @param date $fecha2 Hasta
	 * @return array
	 */
	function ventas_sin_iva($fecha1, $fecha2)
	{
		$c_id = 'ventas_sin_iva2' . $fecha1 . $fecha2;
		// Caché
		if(($this->config->item('bp.cache.data')) && ($r = $this->cache->get($c_id, 'oltpdata')))
		{
			echo $r;
			return ;
		}
		set_time_limit(0);
		$fecha1 = format_mssql_date($fecha1);
		$fecha2 = format_mssql_date($fecha2);
		$this->db->flush_cache();
		$this->db->select('i.cDescripcion, i.nIdGrupoIva')
		->select_sum('f2.fBaseImponible', 'base')
		->from('Doc_Facturas f')
		->join('v_Facturas f2', 'f.nIdFactura = f2.nIdFactura')
		->join('Cli_Direcciones d', 'f.nIdDireccion = d.nIdDireccion', 'left')
		->join('Gen_Regiones r', 'd.nIdRegion = r.nIdRegion', 'left')
		->join('Gen_Paises p', 'p.nIdPais = r.nIdPais', 'left')
		->join('Cli_GruposIva i', 'i.nIdGrupoIva = ISNULL(r.nIdGrupoIVA, p.nIdGrupoIVA)', 'left')
		->where("f.dFecha >= {$fecha1}")
		->where("f.dFecha < " . $this->db->dateadd('d', 1, $fecha2))
		->where('f.nIdEstado IN (2,3)')
		->where('f2.fIVA', 0)
		->where('i.cDescripcion IS NOT NULL')
		->group_by('i.cDescripcion, i.nIdGrupoIva')
		->order_by('i.cDescripcion');
		$query = $this->db->get();
		$data = $this->_get_results($query);
		$data2 = array();
		foreach($data as $d)
		{
			$data2[$d['cDescripcion']] = $d;
		}
		$this->db->flush_cache();
		$this->db->select_sum('f.fPortes', 'base')
		->from('Doc_Facturas f')
		->where('f.nIdEstado IN (2,3)')
		->where("f.dFecha >= {$fecha1}")
		->where("f.dFecha < " . $this->db->dateadd('d', 1, $fecha2));
		$query = $this->db->get();
		$data = $query->row_array();
		$data2['portes'] = array('cDescripcion' => 'Portes',
			'nIdGrupoIva' => null,
			'base' => $data['base']);
		//Caché
		if($this->config->item('bp.cache.data'))
		{
			$this->cache->save($c_id, $data2, 'oltpdata', CACHE_DAY);
		}
		return $data2;
	}

	/**
	 * Ventas sin IVA desglosadas
	 * @param date $fecha1 Desde
	 * @param date $fecha2 Hasta
	 * @param int $idtipo Id del tipo de impuesto
	 * @return array
	 */
	function ventas_sin_iva_desglose($fecha1, $fecha2, $idtipo =null)
	{
		$c_id = 'ventas_sin_iva2' . $fecha1 . $fecha2 . $idtipo;
		// Caché
		if(($this->config->item('bp.cache.data')) && ($r = $this->cache->get($c_id, 'oltpdata')))
		{
			echo $r;
			return ;
		}
		set_time_limit(0);
		$fecha1 = format_mssql_date($fecha1);
		$fecha2 = format_mssql_date($fecha2);
		$this->db->flush_cache();
		$this->db->select('f.nNumero,
				f.dFecha dFecha2,
				s.nNumero nSerie, ' .
				$this->db->date('f.dFecha') . ' dFecha,
				r.cNombre cRegion,
				p.cNombre cPais,
				c.cEmpresa, c.cNombre, c.cApellido,  
				f2.*')
				->from('Doc_Facturas f')
				->join('v_Facturas f2', 'f.nIdFactura = f2.nIdFactura')
				->join('Doc_Series s', 'f.nIdSerie = s.nIdSerie')
				->join('Cli_Clientes c', 'f.nIdCliente = c.nIdCliente')
				->join('Cli_Direcciones d', 'f.nIdDireccion = d.nIdDireccion', 'left')
				->join('Gen_Regiones r', 'd.nIdRegion = r.nIdRegion', 'left')
				->join('Gen_Paises p', 'p.nIdPais = r.nIdPais', 'left')
				->join('Cli_GruposIva i', 'i.nIdGrupoIva = ISNULL(r.nIdGrupoIVA, p.nIdGrupoIVA)', 'left')
				->where('f.nIdEstado IN (2,3)')
				->where("f.dFecha >= {$fecha1}")
				->where("f.dFecha < " . $this->db->dateadd('d', 1, $fecha2))
				->where('f2.fIVA', 0)
				->where('ISNULL(f2.fBaseImponible, 0) <> 0')
				->where('i.nIdGrupoIva', (int)$idtipo)
				->order_by('p.cNombre, r.cNombre, f.dFecha');
		$query = $this->db->get();
		$data2 = $this->_get_results($query);
		foreach ($date2 as $k => $v)
		{
			$date2[$k]['cCliente'] = format_name($v['cNombre'], $v['cApellido'], $v['cEmpresa']);
		}
		//Caché
		if($this->config->item('bp.cache.data'))
		{
			$this->cache->save($c_id, $data2, 'oltpdata', CACHE_DAY);
		}
		return $data2;
	}

	/**
	 * Listado de ventas por títulos en un periodo dado
	 * @param date $fecha1 Desde
	 * @param date $fecha2 Hasta
	 * @param int $min Unidades mínimas de venta
	 * @param int $id Id de la sección
	 * @return array
	 */
	function ventas_titulos($fecha1, $fecha2, $min, $id =null)
	{
		$c_id = 'ventas_titulo' . $fecha1 . $fecha2 . $id . $min;
		// Caché
		if(($this->config->item('bp.cache.data')) && ($r = $this->cache->get($c_id, 'oltpdata')))
		{
			echo $r;
			return ;
		}
		set_time_limit(0);
		$fecha1 = format_mssql_date($fecha1);
		$fecha2 = format_mssql_date($fecha2);
		$this->db->flush_cache();
		$this->db->select('fd.nIdLibro, fd.cAutores, fd.cTitulo, ed.cNombre')
		->select('fd.fPrecio, t.fIVA')
		->select_sum('nCantidad', 'nCantidad')
		->from('Doc_Facturas f')
		->join('Doc_AlbaranesSalida a', 'f.nIdFactura = a.nIdFactura')
		->join('Doc_LineasAlbaranesSalida al', 'a.nIdAlbaran = al.nIdAlbaran')
		->join('Cat_Fondo fd', 'fd.nIdLibro = al.nIdLibro')
		->join('Cat_Tipos t', 'fd.nIdTipo = t.nIdTipo')
		->join('Cat_Editoriales ed', 'fd.nIdEditorial = ed.nIdEditorial', 'left')
		->join('Cat_Secciones s2', "al.nIdSeccion = s2.nIdSeccion")
		->where("f.dFecha >= {$fecha1}")
		->where("f.dFecha < " . $this->db->dateadd('d', 1, $fecha2))
		->where('f.nIdEstado IN (2,3)')
		->group_by('fd.nIdLibro, fd.cAutores, fd.cTitulo, ed.cNombre,fd.fPrecio, t.fIVA')
		->having('SUM(nCantidad) >= ' . $min)
		->order_by('SUM(nCantidad) DESC');
		if($id)
		{
			$this->db->join('Cat_Secciones s3', 's3.nIdSeccion = ' . $id);
			$this->db->where("(s2.cCodigo LIKE " .
				$this->db->concat(array($this->db->varchar('s3.cCodigo'), "'.%'")) . "
				OR s2.nIdSeccion = {$id})");
			$this->db->where("s3.nIdSeccion = {$id}");
		}
		$query = $this->db->get();
		$data2 = $this->_get_results($query);
		//Caché		
		if($this->config->item('bp.cache.data'))
		{
			$this->cache->save($c_id, $data2, 'oltpdata', CACHE_DAY);
		}
		return $data2;
	}

	function simulacion_ventas($year)
	{
		set_time_limit(0);
	}

	/**
	 * Antigüedad del stock de una sección a una fecha dada
	 *
	 * @param int $id Id de la sección
	 * @param date $fecha Fecha de la antigüedad
	 * @return array
	 */
	function rotacion_stock($id, $fecha)
	{
		$c_id = 'antiguedad_seccion' . $id . $fecha;
		// Caché
		if(($this->config->item('bp.cache.data')) && ($r = $this->cache->get($c_id, 'oltpdata')))
		{
			echo $r;
			return ;
		}
		set_time_limit(0);
		//@todo Falta comprobar que sale al indicar una sección
		$fecha = format_mssql_date($fecha);
		$this->db->flush_cache();
		$this->db->select_max('nIdVolcado')
		->from("{$this->_prefix}Ext_AntiguedadStockVolcados")
		->where("dCreacion >= {$fecha}")
		->where("dCreacion < " . $this->db->dateadd('d', 1, $fecha));
		$query = $this->db->get();
		if($query)
		{
			$volcado = $query->row_array();
			$volcado = $volcado['nIdVolcado'];
			if(isset($volcado))
			{
				if(is_numeric($id))
				{
					$this->obj->load->model('generico/m_seccion');
					$sec = $this->obj->m_seccion->load($id);
					$cod = $sec['cCodigo'];

					$this->db->select('s3.nIdSeccion, s3.cNombre cSeccion,
					SUM(nStockFirme) StockTotal,
					SUM(fImporte1) + SUM(fImporte2) + SUM(fImporte3) + SUM(fImporte4) ImporteTotal,
					SUM(fFirme1) Firme1,
					SUM(fImporte1) Importe1,
					SUM(fFirme2) Firme2,
					SUM(fImporte2) Importe2,
					SUM(fFirme3) Firme3,
					SUM(fImporte3) Importe3,
					SUM(fFirme4) Firme4,
					SUM(fImporte4) Importe4');
					$this->db->from("{$this->_prefix}Ext_AntiguedadStockSecciones s");
					$this->db->join('Cat_Secciones s3', "s.nIdSeccion = s3.nIdSeccion");
					$this->db->where('s.nIdVolcado', (int)$volcado);
					$this->db->where("(s3.cCodigo LIKE '{$cod}.%' OR s3.nIdSeccion = {$id})");
					$this->db->group_by('s3.nIdSeccion, s3.cNombre, s.nIdVolcado');
					$this->db->order_by('s3.cNombre');
				}
				else
				{
					$this->db->select('s2.nIdSeccion, s2.cNombre cSeccion,
					SUM(nStockFirme) StockTotal,
					SUM(fImporte1) + SUM(fImporte2) + SUM(fImporte3) + SUM(fImporte4) ImporteTotal,
					SUM(fFirme1) Firme1,
					SUM(fImporte1) Importe1,
					SUM(fFirme2) Firme2,
					SUM(fImporte2) Importe2,
					SUM(fFirme3) Firme3,
					SUM(fImporte3) Importe3,
					SUM(fFirme4) Firme4,
					SUM(fImporte4) Importe4');
					$this->db->from("{$this->_prefix}Ext_AntiguedadStockSecciones s");
					$this->db->join('Cat_Secciones s3', "s.nIdSeccion = s3.nIdSeccion");
					$this->db->join('Cat_Secciones s2', 's2.nIdSeccionPadre IS NULL');
					$this->db->where('s.nIdVolcado', (int)$volcado);
					$this->db->where("(s3.cCodigo LIKE " . $this->db->concat(array($this->db->varchar('s2.cCodigo'), '.%')) .")");
					$this->db->group_by('s2.nIdSeccion, s2.cNombre, s.nIdVolcado');
					$this->db->order_by('s2.cNombre');
				}
				$query = $this->db->get();
				$data = $this->_get_results($query);
				//Caché
				if($this->config->item('bp.cache.data'))
				{
					$this->cache->save($c_id, $data, 'oltpdata', CACHE_DAY);
				}
				return $data;
			}
		}
		return null;
	}

	/**
	 * Ventas por horas y días
	 * @param int $seccion Id de la sección
	 * @param date $fecha1 Fecha inicial
	 * @param date $fecha2 Fecha final
	 * @param bool $sj TRUE: no tiene en cuenta SANT JORDI
	 * @param string $multi IDs de secciones separadas por espacio, comas (,) o puntocoma (;)
	 * @return array
	 */
	function ventas_horas(&$seccion, $fecha1, $fecha2, $sj = TRUE, &$multi = null)
	{
		set_time_limit(0);
		$fecha1 = format_mssql_date($fecha1);
		$fecha2 = format_mssql_date($fecha2);

		$where = null;
		$multi2 = array();

		if (is_numeric($seccion))
		{
			$this->obj->load->model('generico/m_seccion');
			$seccion = $this->obj->m_seccion->load($seccion);
		}
		elseif (count($multi) > 0)
		{
			$this->obj->load->model('generico/m_seccion');
			$where = array();
			foreach ($multi as $value) 
			{
				if (is_numeric($value))
				{
					$sec = $this->obj->m_seccion->load($value);	
					if ($sec)
					{
						$where[] = "(Cat_Secciones.cCodigo LIKE '{$sec['cCodigo']}.%' OR Cat_Secciones.nIdSeccion = {$sec['nIdSeccion']})";
						$multi2[] = $sec['cNombre'];
					}
				}
			}
		}

		$this->db->flush_cache();
		$this->db->select('DAY(Doc_AlbaranesSalida.dCreacion) dd')
		->select('YEAR(Doc_AlbaranesSalida.dCreacion) yy')
		->select('MONTH(Doc_AlbaranesSalida.dCreacion) mm')
		->select('DATEPART(hh, Doc_AlbaranesSalida.dCreacion) hh')
		->select('DATEPART(dw, Doc_AlbaranesSalida.dCreacion) dw')
		->select('DATEPART(wk, Doc_AlbaranesSalida.dCreacion) wk')
		->select_sum('(Doc_LineasAlbaranesSalida.fPrecio * (1 - Doc_LineasAlbaranesSalida.fDescuento/100) * Doc_LineasAlbaranesSalida.nCantidad)', 'vv')
		->from('Doc_Facturas')
		->join('Doc_AlbaranesSalida', 'Doc_Facturas.nIdFactura = Doc_AlbaranesSalida.nIdFactura')
		->join('Doc_LineasAlbaranesSalida', 'Doc_AlbaranesSalida.nIdAlbaran = Doc_LineasAlbaranesSalida.nIdAlbaran')
		->join('Cat_Secciones', "Doc_LineasAlbaranesSalida.nIdSeccion = Cat_Secciones.nIdSeccion")
		->where("Doc_AlbaranesSalida.dCreacion >= {$fecha1}")
		->where("Doc_AlbaranesSalida.dCreacion < " . $this->db->dateadd('d', 1, $fecha2))
		->where('Doc_Facturas.nIdEstado IN (2,3)')
		->group_by('YEAR(Doc_AlbaranesSalida.dCreacion), DAY(Doc_AlbaranesSalida.dCreacion), MONTH(Doc_AlbaranesSalida.dCreacion), DATEPART(hh, Doc_AlbaranesSalida.dCreacion), DATEPART(dw, Doc_AlbaranesSalida.dCreacion), DATEPART(wk, Doc_AlbaranesSalida.dCreacion)')
		->order_by('YEAR(Doc_AlbaranesSalida.dCreacion), MONTH(Doc_AlbaranesSalida.dCreacion), DAY(Doc_AlbaranesSalida.dCreacion), DATEPART(hh, Doc_AlbaranesSalida.dCreacion)');

		if (!$sj)
		{
			$this->db->where('NOT (DAY(Doc_AlbaranesSalida.dCreacion) IN (22, 23) AND MONTH(Doc_AlbaranesSalida.dCreacion)=4)');
		}
		if ($seccion)
		{
			$this->db->where("(Cat_Secciones.cCodigo LIKE '{$seccion['cCodigo']}.%' OR Cat_Secciones.nIdSeccion = {$seccion['nIdSeccion']})");
			$seccion = $seccion['cNombre'];
		}
		if (!empty($where))
		{
			$this->db->where('(' . implode(' OR ', $where) . ')');
			$multi = $multi2;
		}

		$query = $this->db->get();
		$data = $this->_get_results($query);
		return $data;
	}

}

/* End of file M_oltp.php */
/* Location: ./system/application/models/M_oltp.php */
