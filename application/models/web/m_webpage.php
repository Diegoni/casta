<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	web
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Funciones para la página Web
 *
 */
class M_Webpage extends MY_Model
{
	/**
	 * Constructor
	 *
	 * @return M_Webpage
	 */
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Pedidos realizados en Internet
	 *
	 * @return array
	 */
	function pedidos_realizados()
	{
		$this->db->flush_cache();
		$this->db->select('YEAR(dCreacion) anno, MONTH(dCreacion) mes, COUNT(*) count')->from('Doc_PedidosCliente')->where('nIdTipoOrigen', (int)5)->group_by('YEAR(dCreacion), MONTH(dCreacion)')->order_by('YEAR(dCreacion), MONTH(dCreacion)');

		$query = $this->db->get();
		$data = $this->_get_results($query);
		$data2 = array();
		foreach ($data as $d)
		{
			$data2['datos'][$d['anno']][$d['mes']] = $d['count'];
			$data2['years'][$d['anno']] = TRUE;
		}
		return $data2;
	}

	/**
	 * Facturación de los pedidos de Internet por series
	 *
	 * @return array
	 */
	function pedidos_series($year = null, $month = null)
	{
		$data2 = array();
		// Consulta según el antiguo
		$this->db->flush_cache();
		$this->db->select('YEAR(f.dFecha) [anno],MONTH(f.dFecha) [mes],s.cDescripcion serie')->select_sum('lae.nCantidad * (lae.fPrecio * (1 - lae.fDescuento/100.0))', 'total')->from('Doc_PedidosCliente pp')->join('Doc_LineasPedidoCliente lpp', 'pp.nIdPedido = lpp.nIdPedido')->join('Doc_LineasAlbaranesSalida lae', 'lae.nIdLineaPedido = lpp.nIdLinea')->join('Doc_AlbaranesSalida ae', 'lae.nIdAlbaran = ae.nIdAlbaran')->join('Doc_Facturas f', 'f.nIdFactura = ae.nIdFactura')->join('Doc_Series s', 's.nIdSerie = f.nIdSerie')->where('pp.nIdTipoOrigen = 5')->where('lpp.nIdAlbaranSal IS NULL')->group_by('YEAR(f.dFecha),MONTH(f.dFecha),s.cDescripcion')->order_by('YEAR(f.dFecha),MONTH(f.dFecha),s.cDescripcion');
		if (isset($year) && ($year != ''))
			$this->db->where('YEAR(f.dFecha)', (int)$year);
		if (isset($month) && ($month != ''))
			$this->db->where('MONTH(f.dFecha)', (int)$month);

		$query = $this->db->get();
		$data = $this->_get_results($query);
		foreach ($data as $d)
		{
			$data2['datos'][$d['anno']][$d['mes']][$d['serie']] = $d['total'];
			$data2['years'][$d['anno']][$d['serie']] = TRUE;
		}

		// Consulta según el nuevo sistema
		$this->db->flush_cache();
		$this->db->select('YEAR(f.dFecha) [anno],MONTH(f.dFecha) [mes],s.cDescripcion serie')->select_sum('lae.nCantidad * (lae.fPrecio * (1 - lae.fDescuento/100.0))', 'total')->from('Doc_PedidosCliente pp')->join('Doc_LineasPedidoCliente lpp', 'pp.nIdPedido = lpp.nIdPedido')->join('Doc_AlbaranesSalida ae', 'lpp.nIdAlbaranSal = ae.nIdAlbaran')->join('Doc_LineasAlbaranesSalida lae', 'ae.nIdAlbaran = lae.nIdAlbaran AND lae.nIdSeccion = lpp.nIdSeccion AND lae.nIdLibro = lpp.nIdLibro')->join('Doc_Facturas f', 'f.nIdFactura = ae.nIdFactura')->join('Doc_Series s', 's.nIdSerie = f.nIdSerie')->where('pp.nIdTipoOrigen', (int)5)->group_by('YEAR(f.dFecha),MONTH(f.dFecha),s.cDescripcion')->order_by('YEAR(f.dFecha),MONTH(f.dFecha),s.cDescripcion');
		if (isset($year) && ($year != ''))
			$this->db->where('YEAR(f.dFecha)', (int)$year);
		if (isset($month) && ($month != ''))
			$this->db->where('MONTH(f.dFecha)', (int)$month);

		$query = $this->db->get();
		$data = $this->_get_results($query);
		foreach ($data as $d)
		{
			if (isset($data2['datos'][$d['anno']][$d['mes']][$d['serie']]))
			{
				$data2['datos'][$d['anno']][$d['mes']][$d['serie']] += $d['total'];
			}
			else
			{
				$data2['datos'][$d['anno']][$d['mes']][$d['serie']] = $d['total'];
			}

			$data2['years'][$d['anno']][$d['serie']] = TRUE;
		}

		return $data2;
	}

	/**
	 * Los más vendidos
	 * @param int $dias Número de días de venta a tener en cuenta
	 * @return bool, TRUE: ha ido bien, FALSE: ha habido error
	 */
	function bestsellers($dias = null)
	{
		if (!isset($dias))
		{
			$dias = $this->config->item('bp.webpage.bestsellers.dias');
		}
		$notsec = $this->config->item('bp.webpage.bestsellers.notsec');

		// Transacción
		$this->db->trans_begin();

		// Borra lo anterior
		$this->db->flush_cache();
		if (!$this->db->where('1=1')->delete('OSC_BestSellers_Ventas'))
		{
			$this->db->trans_rollback();
			return FALSE;
		}
		$this->db->flush_cache();
		if (!$this->db->where('1=1')->delete('OSC_BestSellers'))
		{
			$this->db->trans_rollback();
			return FALSE;
		}

		// Ventas globales

		$sql = "INSERT INTO OSC_BestSellers_Ventas(nIdMateria, cCodMateria, cTitulo, nIdLibro, nVentas)
			SELECT m.nIdMateria, m.cCodMateria, f.cTitulo, lal.nIdLibro, SUM(nCantidad)
			FROM Doc_AlbaranesSalida al
			JOIN Doc_LineasAlbaranesSalida lal ON al.nIdAlbaran = lal.nIdAlbaran
			JOIN Cat_Secciones s ON s.nIdSeccion = lal.nIdSeccion AND ISNULL(s.nIdSeccionPadre, s.nIdSeccion) NOT IN (860, 800, 819, 861)
			JOIN Cat_Fondo f ON f.nIdLibro = lal.nIdLibro
			JOIN Cat_Libros_Materias sl ON f.nIdLibro = sl.nIdLibro
			JOIN Cat_Materias m ON m.nIdMateria = sl.nIdMateria
			WHERE (al.nIdEstado = 2 OR al.nIdEstado = 3)
			AND (" . $this->db->datediff('al.dCreacion', 'GETDATE()') . " < {$dias})
			GROUP BY lal.nIdLibro, m.cCodMateria, m.nIdMateria, f.cTitulo
			HAVING SUM(nCantidad) > 0";
		$this->db->query($sql);
		#echo '<pre>'; print_r($this->db->queries); echo '</pre>'; die();
		// Ventas por materias
		$this->db->flush_cache();
		$this->db->select('nIdMateria, cCodMateria, nIdMateriaPadre')
		->from('Cat_Materias');
		$query = $this->db->get();
		$data = $this->_get_results($query);
		$main = array();
		foreach ($data as $d)
		{
			$this->db->flush_cache();
			$this->db->select('nIdLibro, cTitulo, nVentas')
			->from('OSC_BestSellers_Ventas')
			->where("(cCodMateria LIKE '{$d['cCodMateria']}.%' OR cCodMateria = '{$d['cCodMateria']}')")
			->group_by('nIdLibro,cTitulo, nVentas')
			->order_by('nVentas DESC')
			->limit(20);
			$query = $this->db->get();
			$data2 = $this->_get_results($query);
			foreach ($data2 as $reg)
			{
				$this->db->flush_cache();
				$this->db->insert('OSC_BestSellers', array(
						'nIdMateria' => $d['nIdMateria'],
						'cTitulo' => $reg['cTitulo'],
						'nIdLibro' => $reg['nIdLibro'],
						'nCantidad' => $reg['nVentas']
				));
			}

			// Las ventas generales, 2 de cada
			if (isset($d['nIdMateriaPadre']))
			{
				if (count($data2) > 0)
				{
					if (!in_array($data2[0]['nIdLibro'], $main))
					{
						$this->db->flush_cache();
						$this->db->insert('OSC_BestSellers', array(
								'nIdMateria' => 0,
								'cTitulo' => $data2[0]['cTitulo'],
								'nIdLibro' => $data2[0]['nIdLibro'],
								'nCantidad' => $data2[0]['nVentas']
						));
						$main[] = $data2[0]['nIdLibro'];
					}
				}
				if (count($data2) > 1)
				{
					if (!in_array($data2[1]['nIdLibro'], $main))
					{
						$this->db->flush_cache();
						$this->db->insert('OSC_BestSellers', array(
								'nIdMateria' => 0,
								'cTitulo' => $data2[1]['cTitulo'],
								'nIdLibro' => $data2[1]['nIdLibro'],
								'nCantidad' => $data2[1]['nVentas']
						));
						$main[] = $data2[1]['nIdLibro'];
					}
				}
			}
		}
		$this->db->trans_commit();
		return TRUE;
	}

	/**
	 * Stock para mostrar en la Web
	 * @return array
	 */
	function stock($id = null)
	{
		$this->db->flush_cache();
		$this->db->select('Cat_Secciones_Libros.nIdLibro')
		->select_sum('Cat_Secciones_Libros.nStockFirme + Cat_Secciones_Libros.nStockDeposito - Cat_Secciones_Libros.nStockReservado - Cat_Secciones_Libros.nStockADevolver', 'nStock')
		->from('Cat_Secciones_Libros')
		->join('Cat_Secciones', 'Cat_Secciones_Libros.nIdSeccion = Cat_Secciones.nIdSeccion')
		->where('Cat_Secciones.bWeb = 1')
		->where('(Cat_Secciones_Libros.nStockFirme + Cat_Secciones_Libros.nStockDeposito - Cat_Secciones_Libros.nStockReservado - Cat_Secciones_Libros.nStockADevolver) > 0')
		->group_by('Cat_Secciones_Libros.nIdLibro');
		if (is_numeric($id)) $this->db->where('Cat_Secciones_Libros.nIdLibro = ' . $id);
		$query = $this->db->get();
		return $this->_get_results($query);
	}

	/**
	 * Status para mostrar en la Web
	 * @return array
	 */
	function status()
	{
		$this->db->flush_cache();
		$this->db->select('nIdLibro')
		->from('Cat_Fondo')
		->where('ISNULL(bMostrarWebManual, 1) <> 0 AND fPrecio > 0');
		$query = $this->db->get();
		return $this->_get_results($query);
	}

	/**
	 * Status para mostrar en la Web
	 * @return array
	 */
	function promociones($dias)
	{
		$inicio = format_mssql_date(time());
		$this->db->flush_cache();
		$this->db->select('nIdLibro')
		->select($this->_date_field('Sus_Boletines_Libros.dCreacion', 'dInicio'))
		->select($this->_date_field($this->db->dateadd('d', $dias, 'Sus_Boletines_Libros.dCreacion'), 'dFinal'))
		->from('Sus_Boletines')
		->join('Sus_Boletines_Libros', 'Sus_Boletines_Libros.nIdBoletin =  Sus_Boletines.nIdBoletin')
		->where('ISNULL(bWeb, 1) <> 0')
		->where("Sus_Boletines_Libros.dCreacion <= " . $this->db->dateadd('d', $dias, $inicio));
		$query = $this->db->get();
		return $this->_get_results($query);
	}

	/**
	 * Disponibilidad para mostrar en la Web
	 * @return array
	 */
	function disponibilidad($dias, $meses, $id = null)
	{
		$final = array();
		
		$this->db->flush_cache();
		$this->db->select('nIdLibro, ISNULL(nIdPlazoEnvioManual, nIdPlazoEnvio) nIdPlazoEnvio')
		->from('Cat_Fondo')
		->where('ISNULL(bMostrarWebManual, 1) = 1')
		->where('nIdEstado NOT IN (12, 17, 13, 14, 11, 4, 10, 9)')
		->where('ISNULL(nIdPlazoEnvioManual, nIdPlazoEnvio)<>1')
		->where('ISNULL(nIdPlazoEnvioManual, nIdPlazoEnvio) IS NOT NULL')
		->where('ISNULL(fPrecio, 0) > 0');
		if (is_numeric($id)) $this->db->where('Cat_Fondo.nIdLibro = ' . $id);
		$query = $this->db->get();
		$data = $this->_get_results($query);
		foreach($data as $k) $final[$k['nIdLibro']] = $k;
		#print_r($final);
		#$data = array();
		#echo '<pre>';
		# DESCATALOGADO
		$this->db->flush_cache();
		$this->db->select('nIdLibro, 5 nIdPlazoEnvio')
		->from('Cat_Fondo')
		->where('ISNULL(bMostrarWebManual, 1) <> 0')
		->where('nIdEstado IN (4)');
		if (is_numeric($id)) $this->db->where('Cat_Fondo.nIdLibro = ' . $id);
		$query = $this->db->get();
		$data = $this->_get_results($query);
		foreach($data as $k) $final[$k['nIdLibro']] = $k;
		#print_r($final);
		#$data = array_merge($data, $data2);

		# NO SE PUEDE SERVIR
		$this->db->flush_cache();
		$this->db->select('nIdLibro, 6 nIdPlazoEnvio')
		->from('Cat_Fondo')
		->where('ISNULL(bMostrarWebManual, 1) <> 0')
		->where('nIdEstado IN (5, 6, 7, 15, 11, 12, 10, 9)');
		if (is_numeric($id)) $this->db->where('Cat_Fondo.nIdLibro = ' . $id);
		$query = $this->db->get();
		$data = $this->_get_results($query);
		foreach($data as $k) $final[$k['nIdLibro']] = $k;
		#print_r($final);
		#print_r($data2);
		#$data = array_merge($data, $data2);

		# FUERA DE CATALOGO
		# Sin documentos en los últimos días
		$meses *= 30;
		$this->db->flush_cache();
		$this->db->select('nIdLibro, 7 nIdPlazoEnvio')
		->from('Cat_Fondo')
		->where('ISNULL(bMostrarWebManual, 1) <> 0')
		->where('nIdEstado NOT IN (4, 5, 6, 7, 15, 11, 12, 10, 9)')
		->where("((" . $this->db->datediff('dEdicion', 'GETDATE()') . " > {$meses}) OR dEdicion IS NULL)")
		->where("((" . $this->db->datediff('dCreacion', 'GETDATE()') . " > {$dias}) OR dCreacion IS NULL)")
		->where("((" . $this->db->datediff('dUltimaCompra', 'GETDATE()') . " > {$dias}) OR dUltimaCompra IS NULL)")
		->where("((" . $this->db->datediff('dUltimaVenta', 'GETDATE()') . " > {$dias}) OR dUltimaVenta IS NULL)");
		if (is_numeric($id)) $this->db->where('Cat_Fondo.nIdLibro = ' . $id);
		$query = $this->db->get();
		$data = $this->_get_results($query);
		#echo array_pop($this->db->queries); die();
		foreach($data as $k) $final[$k['nIdLibro']] = $k;
		#print_r($final);

		# TIENE STOCK
		$this->db->flush_cache();
		$this->db->select('nIdLibro, 1 nIdPlazoEnvio')
		->from('Cat_Fondo')
		->where('ISNULL(bMostrarWebManual, 1) <> 0')
		->where('nIdLibro IN (SELECT nIdLibro FROM Cat_Secciones_Libros WHERE nStockFirme+nStockDeposito>0)');
		if (is_numeric($id)) $this->db->where('Cat_Fondo.nIdLibro = ' . $id);
		$query = $this->db->get();
		$data = $this->_get_results($query);
		foreach($data as $k) 
		{
			#$final[$k['nIdLibro']] = $k;
			unset($final[$k['nIdLibro']]);
		}
		/*$stat = array();
		foreach ($final as $value) 
		{
			if (!isset($stat[$value['nIdPlazoEnvio']])) $stat[$value['nIdPlazoEnvio']] = 0;
			++$stat[$value['nIdPlazoEnvio']];
		}*/
		#var_dump($final); die();
		#var_dump($stat);
		#var_dump(count($final)); die();
		#print_r($final);
		return $final;
	}

	/**
	 * Devuelve el listado de bestsellers
	 * @return array
	 */
	function get_bestsellers()
	{
		$this->db->flush_cache();
		$this->db->select('*')->from('OSC_BestSellers');
		$query = $this->db->get();
		return $this->_get_results($query);
	}

	/**
	 * Devuelve el listado de ofertas
	 * @return array
	 */
	function get_ofertas()
	{
		$this->db->flush_cache();
		$this->db->select('Cat_Fondo.nIdLibro, Cat_Fondo.fPrecio')
		->from('Cat_Secciones_Libros')
		->join('Cat_Fondo', 'Cat_Fondo.nIdLibro = Cat_Secciones_Libros.nIdLibro')
		->join('Cat_Secciones', 'Cat_Secciones.nIdSeccion=Cat_Secciones_Libros.nIdSeccion')
		->where('Cat_Secciones.bWeb=1')
		->where('(Cat_Secciones_Libros.nStockDeposito + Cat_Secciones_Libros.nStockFirme) > 0')
		->where('Cat_Fondo.nIdOferta>0')
		->group_by('Cat_Fondo.nIdLibro, Cat_Fondo.fPrecio');
		$query = $this->db->get();
		#echo array_pop($this->db->queries); die();

		return $this->_get_results($query);
	}

	/**
	 * ID de todos los artículos del sistema
	 * @return array
	 */
	function articulos()
	{
		$this->db->flush_cache();
		$this->db->select('Cat_Fondo.nIdLibro')
		->from('Cat_Fondo')
		->order_by('Cat_Fondo.nIdLibro');
		$query = $this->db->get();

		return $this->_get_results($query);
	}

	/**
	 * ID de todos los clientes de Bibliopola que tienen cliente Web
	 * @return array
	 */
	function clientes()
	{
		$this->db->flush_cache();
		$this->db->select('Cli_Clientes.nIdCliente, Cli_Clientes.nIdWeb')
		->from('Cli_Clientes')
		->where('Cli_Clientes.nIdWeb IS NOT NULL');
		$query = $this->db->get();

		return $this->_get_results($query);
	}

	/**
	 * Devuelve el listado de ofertas
	 * @param int $last Timespam de la última consulta
	 * @param string $filter Filtro de la búsqueda
	 * @return array
	 */
	function get_pedidos_web($last = null, $filter = null)
	{
		$this->db->flush_cache();
		$this->db->select('Doc_PedidosCliente.nIdPedido')
		->select_max($this->_date_field('Doc_LineasPedidoCliente.dAct'), 'dAct')
		->from('Doc_PedidosCliente')
		->join('Cli_Clientes', 'Cli_Clientes.nIdCliente=Doc_PedidosCliente.nIdCliente')
		->join('Doc_LineasPedidoCliente', 'Doc_LineasPedidoCliente.nIdPedido=Doc_PedidosCliente.nIdPedido')
		->where('Cli_Clientes.nIdWeb IS NOT NULL')
		->where('(ISNULL(Doc_PedidosCliente.bMostrarWeb, 1) = 1 OR Doc_PedidosCliente.nIdWeb IS NOT NULL)')
		->order_by('Doc_LineasPedidoCliente.dAct')
		->group_by('Doc_PedidosCliente.nIdPedido');

		if (!empty($filter))
		{
			$this->db->where($filter);
		}
		if (!empty($last))
		{
			$last = format_mssql_date($last);
			$where = "((Doc_PedidosCliente.dAct > {$last} OR Doc_LineasPedidoCliente.dAct > {$last}) OR (Doc_PedidosCliente.nIdWeb IS NULL AND Cli_Clientes.nIdWeb IS NOT NULL))";

			$this->db->where($where);
		}
		$query = $this->db->get();

		#echo array_pop($this->db->queries); die();
		return $this->_get_results($query);
	}
}

/* End of file M_webpage.php */
/* Location: ./system/application/models/web/M_webpage.php */
