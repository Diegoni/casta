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
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

define('ALBARAN_ENTRADA_STATUS_EN_PROCESO', 1);
define('ALBARAN_ENTRADA_STATUS_CERRADO', 2);
define('ALBARAN_ENTRADA_STATUS_FACTURADO', 3);
define('ALBARAN_ENTRADA_STATUS_ASIGNADO', 4);

define('DEFAULT_ALBARAN_ENTRADA_STATUS', ALBARAN_ENTRADA_STATUS_EN_PROCESO);

/**
 * Albaran Entrada
 *
 */
class M_albaranentrada extends MY_Model
{
	/**
	 * Constructor
	 * @return M_albaranentrada
	 */
	function __construct()
	{
		$data_model = array(
            'nIdProveedor' 		=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'proveedores/proveedor/search', 'cProveedor')),
            'nIdDireccion' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'proveedores/direccion/search')),
			'nIdEstado'			=> array(DATA_MODEL_DEFAULT_VALUE => DEFAULT_ALBARAN_ENTRADA_STATUS, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'compras/estadoalbaranentrada/search')),
			'cRefProveedor' 	=> array(), 
			'cRefInterna'		=> array(),
			'nIdDivisa'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'generico/divisa/search')),
			'fPrecioCambio'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT, DATA_MODEL_DEFAULT_VALUE => 1), 
			'bDeposito' 		=> array(DATA_MODEL_DEFAULT_VALUE => FALSE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),
			'dVencimiento'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATE),
			'dCierre'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATE),
			'dFecha'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATE),
			'cNumeroAlbaran'	=> array(),
			'bValorado' 		=> array(DATA_MODEL_DEFAULT_VALUE => FALSE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),
			'bAplicarGastosDefecto' 	=> array(DATA_MODEL_DEFAULT_VALUE => FALSE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),
			'bPrecioLibre' 		=> array(DATA_MODEL_DEFAULT_VALUE => FALSE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),

			'fImporteCamara'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT), 
			'nIdPais'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'perfiles/pais/search')), 
			'nIdDocumentoCamara'=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'compras/documentocamara/search', 'cNombre')),
			'nPeso'				=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT), 
			'nIdTipoMercancia'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'compras/tipomercancia/search', 'cDescripcion')),
			'fCambioCamara'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT), 
			'bExtranjero' 		=> array(DATA_MODEL_DEFAULT_VALUE => FALSE, DATA_MODEL_DEFAULT_VALUE => FALSE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),
			'bSuscripciones' 	=> array(DATA_MODEL_DEFAULT_VALUE => FALSE, DATA_MODEL_DEFAULT_VALUE => FALSE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),				
            'tNotasExternas' 	=> array(),
            'tNotasInternas' 	=> array(),
            'nLibros' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
            'fTotal' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_MONEY),
		);

		$this->_alias = array(
			'cProveedor' 	=> array('(Prv_Proveedores.cEmpresa + Prv_Proveedores.cNombre + Prv_Proveedores.cApellido)'),
		);

		parent::__construct('Doc_AlbaranesEntrada', 'nIdAlbaran', 'nIdAlbaran', array('cNumeroAlbaran', 'cRefProveedor', 'cRefInterna'), $data_model, TRUE);

		$this->_relations['lineas'] = array (
			'ref'		=> 'compras/m_albaranentradalinea',
            'cascade' 	=> TRUE,
			'type'		=> DATA_MODEL_RELATION_1N,
			'fk'		=> 'nIdAlbaran');

		$this->_relations['proveedor'] = array (
			'ref'	=> 'proveedores/m_proveedor',
			'fk'	=> 'nIdProveedor');

		$this->_relations['direccion'] = array (
			'ref'	=> 'proveedores/m_direccion',
			'fk'	=> 'nIdDireccion');

		$this->_relations['cargos'] = array (
			'ref'	=> 'compras/m_albaranentradacargo',
            'cascade' 	=> TRUE,
			'type'	=> DATA_MODEL_RELATION_1N,
			'fk'	=> 'nIdAlbaran');
	}

	/**
	 * Devuelve la asignación a pedidos de proveedor del albarán
	 * @param int $id Id del documento
	 * @param  int $idl Id del artículo
	 * @return array
	 */
	function get_asignacion($id, $idl = null)
	{
		$this->db->flush_cache();
		$this->db->select('Cat_Fondo.nIdLibro, Cat_Fondo.cTitulo')
		->select('Cat_Secciones.nIdSeccion, Cat_Secciones.cNombre cSeccion')
		->select('Doc_LineasPedidosRecibidas.nCantidad, Doc_LineasPedidosRecibidas.nIdLinea')
		->select('Doc_LineasPedidoProveedor.nIdPedido')
		->select('Doc_LineasAlbaranesEntrada.fPrecioVenta fPVP')
		->select($this->_date_field('Doc_LineasAlbaranesEntrada.dCreacion', 'dCreacion'))
		->select('Doc_LineasAlbaranesEntrada.nIdLinea')
		->select('Ext_Concursos.cDescripcion cConcurso, Ext_Concursos.nIdConcurso')
		->select('Ext_Bibliotecas.cDescripcion cBiblioteca')
		->select('Ext_LineasPedidoConcurso.nIdLineaPedidoConcurso')
		->select('Ext_Salas.cDescripcion cSala')
		->from('Doc_LineasAlbaranesEntrada')
		->join('Doc_LineasPedidosRecibidas', "Doc_LineasAlbaranesEntrada.nIdLinea = Doc_LineasPedidosRecibidas.nIdLineaAlbaran")
		->join('Doc_LineasPedidoProveedor', 'Doc_LineasPedidoProveedor.nIdLinea = Doc_LineasPedidosRecibidas.nIdLineaPedido')
		->join('Cat_Fondo', "Cat_Fondo.nIdLibro=Doc_LineasAlbaranesEntrada.nIdLibro")
		->join('Cat_Secciones', "Cat_Secciones.nIdSeccion=Doc_LineasPedidoProveedor.nIdSeccion")
		->join('Ext_LineasPedidoConcurso', 'Ext_LineasPedidoConcurso.nIdLineaPedidoProveedor=Doc_LineasPedidoProveedor.nIdLinea', 'left')
		->join('Ext_Bibliotecas', "Ext_Bibliotecas.nIdBiblioteca = Ext_LineasPedidoConcurso.nIdBiblioteca", 'left')
		->join('Ext_Salas', "Ext_Salas.nIdSala = Ext_LineasPedidoConcurso.nIdSala", 'left')
		->join('Ext_Concursos', "Ext_Concursos.nIdConcurso = Ext_Bibliotecas.nIdConcurso", 'left')
		->where("Doc_LineasAlbaranesEntrada.nIdAlbaran = {$id}");
		if (isset($idl))
			$this->db->where("Doc_LineasAlbaranesEntrada.nIdLibro = {$idl}");
		$query = $this->db->get();
		$data = $this->_get_results($query);
		#var_dump($this->db->queries); die();
		return $data;
	}

	/**
	 * Devuelve la suscripción vinculada al albarán de entrada
	 * @return int, NULL si no hay suscripción
	 */
	function get_suscripcion($id)
	{
		$this->db->flush_cache();
		$this->db->select('Sus_PedidosSuscripcion.nIdSuscripcion')
		->from('Doc_LineasAlbaranesEntrada')
		->join('Doc_LineasPedidosRecibidas', "Doc_LineasAlbaranesEntrada.nIdLinea = Doc_LineasPedidosRecibidas.nIdLineaAlbaran")
		->join('Doc_LineasPedidoProveedor', 'Doc_LineasPedidoProveedor.nIdLinea = Doc_LineasPedidosRecibidas.nIdLineaPedido')
		->join('Sus_PedidosSuscripcion', 'Doc_LineasPedidoProveedor.nIdPedido = Sus_PedidosSuscripcion.nIdPedido')
		->where("Doc_LineasAlbaranesEntrada.nIdAlbaran = {$id}");
		$query = $this->db->get();
		$data = $this->_get_results($query);
		
		return (count($data)>0)?$data[0]['nIdSuscripcion']:null;	
	}

	/**
	 * Cierra el albarán
	 * @param int $id Id del albarán
	 * @return bool, TRUE: cerrado correctamente, FALSE: no se ha cerrado
	 */
	function cerrar($id)
	{
		set_time_limit(0);
		$doc = $this->load($id, array('lineas', 'cargos'));
		if (empty($doc))
		{
			$this->_set_error_message($this->lang->line('registro_no_encontrado'));
			return FALSE;
		}	

		// Tiene que estar abierto
		if ($doc['nIdEstado'] != DEFAULT_ALBARAN_ENTRADA_STATUS)
		{
			$this->_set_error_message($this->lang->line('error-albaranentrada-cerrado'));
			return FALSE;
		}

		$lineas = $doc['lineas'];
		$cargos = $doc['cargos'];

		// Si no tiene líneas se elimina
		if (count($lineas) == 0 && count($cargos) == 0)
		{
			return $this->delete($id);
		}

		// Modelos de datos que se van a usar
		$this->obj->load->model('compras/m_albaranentradalinea');
		$this->obj->load->model('catalogo/m_articulo');
		$this->obj->load->model('catalogo/m_articuloseccion');

		// Cambio divisa
		$divisa_default = $this->config->item('bp.divisa.default');
		if (!isset($doc['nIdDivisa'])) $doc['nIdDivisa'] = $divisa_default;
		if (!isset($doc['fPrecioCambio']))
		{
			$this->obj->load->model('generico/m_divisa');
			$d = $this->obj->m_divisa->load($doc['nIdDivisa']);
			$doc['fPrecioCambio'] = $d['fCompra'];
		}
		if (!is_numeric($doc['fPrecioCambio']) || $doc['fPrecioCambio']<=0) $doc['fPrecioCambio'] = 1;
		//$margendivisa = ($doc['nIdDivisa'] != $divisa_default)?$this->config->item('bp.divisa.margenmoneda'):0;

		// Gastos
		$gastos = 0;
		foreach ($cargos as $reg)
		{
			$gastos += $reg['fImporte'];
		}

		// Actualiza coste de los artículos
		$total = 0;
		$libros = 0;
		$base = 0;
		$arts = array();
		#var_dump($lineas); 
		foreach ($lineas as $k => $reg)
		{
			// Unidades
			$libros += $reg['nCantidad'];

			// Precio en divisa local aplicando margen de divisa
			$lineas[$k]['fPrecio'] = format_decimals(($reg['fPrecio'] /  ($doc['fPrecioCambio'])));
			$lineas[$k]['fPrecioDivisa'] = $reg['fPrecio'];

			// Cálculo del precio de coste de la línea individual
			$coste = format_calculate_coste($lineas[$k]);
			#print "Coste {$reg['nIdLibro']} = {$coste}<br/>";
			$lineas[$k]['fCoste'] = $coste / $reg['nCantidad'];
			if (isset($arts[$reg['nIdLibro']]))
			{
				$arts[$reg['nIdLibro']]['cantidad'] += $reg['nCantidad'];
				$arts[$reg['nIdLibro']]['coste'] += $coste;
			}
			else
			{
				$arts[$reg['nIdLibro']] = array(
					'cantidad' 	=> $reg['nCantidad'],
					'coste'		=> $coste,
					'fPrecio'	=> $lineas[$k]['fPrecioDivisa']
				);
			}
			// Totales
			$totales = format_calculate_importes($reg);
			$base += $totales['fBase2'];
			$total += format_decimals($totales['fTotal2']);
		}

		// Actualiza las líneas del albarán con los costes y los artículos con el PMP
		$this->db->trans_begin();

		// Reparte los gastos según el precio
		foreach ($arts as $k => $l)
		{
			$arts[$k]['gastos'] = ($base!=0)?(($l['coste'] * $gastos) / $base):0;
			$arts[$k]['unitario'] = format_decimals(($arts[$k]['gastos'] + $arts[$k]['coste']) / $arts[$k]['cantidad']);
			#print "Coste {$k} = {$arts[$k]['unitario']}<br/>";
			// Calcula el precio de coste del artículo (PMP)
			$stocks = $this->obj->m_articuloseccion->stocks($k);
			$actual = $stocks['nStockFirme'] + $stocks['nStockDeposito'];
			if ($actual < 0) $actual = 0;
			$stk =  $actual - $arts[$k]['cantidad'];
			$a = $this->obj->m_articulo->load($k);
			if ($stk > 0)
			{
				$coste = $a['fPrecioCompra'] * $stk + ($arts[$k]['unitario'] * $arts[$k]['cantidad']);
				$coste /= $stocks['nStockFirme'] + $stocks['nStockDeposito'];
				$coste = format_decimals($coste);
			}
			else
			{
				$coste = $arts[$k]['unitario'];
			}

			// Actualiza el artículo
			$upd = array(
				'fPrecioCompra' 		=> $coste,
				'fPrecioProveedor'		=> $l['fPrecio'],
				'dFechaPrecioProveedor'	=> $doc['dFecha'],
				'bPrecioLibre'			=> $doc['bPrecioLibre'],
				'nIdDivisaProveedor'	=> $doc['nIdDivisa']			
			);

			if (!$this->obj->m_articulo->update($k, $upd))
			{
				$this->_set_error_message($this->obj->m_articulo->error_message());
				$this->db->trans_rollback();
				return FALSE;
			}
		}

		// Actualiza las líneas
		$data = array('nIdEstado' => ALBARAN_ENTRADA_LINEA_STATUS_CERRADO);
		foreach ($lineas as $reg)
		{
			$data['fCoste'] = $reg['fCoste'];
			$data['fGastos'] = ($base!=0)?(($reg['fCoste'] * $gastos) / $base):0;
			$data['fPrecio'] = $reg['fPrecio'];
			$data['fPrecioDivisa'] = $reg['fPrecioDivisa'];
			// Actualiza la línea de pedido
			if (!$this->obj->m_albaranentradalinea->update($reg['nIdLinea'], $data))
			{
				$this->_set_error_message($this->obj->m_albaranentradalinea->error_message());
				$this->db->trans_rollback();
				return FALSE;
			}
		}

		// Actualiza el estado del pedido
		if (!$this->update($id, array(
        	'nIdEstado' 	=> ALBARAN_ENTRADA_STATUS_CERRADO,
			'fTotal'		=> $total,
			'nLibros'		=> $libros,
			'nIdDivisa'		=> $doc['nIdDivisa'],
			'fPrecioCambio'	=> $doc['fPrecioCambio'],
			'dCierre' 		=> time()
		)))
		{
			$this->db->trans_rollback();
			return FALSE;
		}
		#$this->db->trans_rollback();
		$this->db->trans_commit();
		
		return TRUE;
	}

	/**
	 * Abre el albarán
	 * @param int $id Id del albarán
	 */
	function abrir($id)
	{
		$doc = $this->load($id, array('lineas'));
		if (empty($doc))
		{
			$this->_set_error_message($this->lang->line('registro_no_encontrado'));
			return FALSE;
		}		
		if ($doc['nIdEstado'] == ALBARAN_ENTRADA_STATUS_EN_PROCESO)
		{
			$this->_set_error_message($this->lang->line('error-albaranentrada-abierto'));
			return FALSE;
		}
		if ($doc['nIdEstado'] != ALBARAN_ENTRADA_STATUS_CERRADO)
		{
			$this->_set_error_message($this->lang->line('error-albaranentrada-asignado'));
			return FALSE;
		}

		$lineas = $doc['lineas'];
		#$divisa_default = $this->config->item('bp.divisa.default');
		#if (!isset($doc['nIdDivisa'])) $doc['nIdDivisa'] = $divisa_default;

		// Modelos de datos que se van a usar
		$this->obj->load->model('compras/m_albaranentradalinea');
		$this->obj->load->model('catalogo/m_articulo');
		$this->obj->load->model('catalogo/m_articuloseccion');

		// Se comprueba línea a línea
		$this->db->trans_begin();
		$total = 0;
		$libros = 0;
		$data['nIdEstado'] = ALBARAN_ENTRADA_LINEA_STATUS_EN_PROCESO;
		$arts = array();
		foreach ($lineas as $k => $reg)
		{
			if ($reg['nCantidadDevuelta'] > 0)
			{
				$this->_set_error_message(sprintf($this->lang->line('error-albaranentrada-devuelto'), $reg['cTitulo']));
				$this->db->trans_rollback();
				return FALSE;
			}
			
			$coste = format_decimals(($reg['fCoste'] + $reg['fGastos']) / $doc['fPrecioCambio']);
			
			if (isset($arts[$lineas[$k]['nIdLibro']]))
			{
				$arts[$lineas[$k]['nIdLibro']]['cantidad'] += $reg['nCantidad'];
				$arts[$lineas[$k]['nIdLibro']]['coste'] = $coste;
			}
			else
			{
				$arts[$lineas[$k]['nIdLibro']] = array(
					'cantidad' 	=> $reg['nCantidad'],
					'coste'		=> $coste
				);
			}
			// Actualiza la línea de pedido

			if (isset($reg['fPrecioDivisa']) && ($reg['fPrecioDivisa'] > 0)) 
			{
				$data['fPrecio'] = $reg['fPrecioDivisa'];
				$data['fPrecioDivisa'] = null;
			}
			if (!$this->obj->m_albaranentradalinea->update($reg['nIdLinea'], $data))
			{
				$this->_set_error_message($this->obj->m_albaranentradalinea->error_message());
				$this->db->trans_rollback();
				return FALSE;
			}
		}
		#print '<pre>'; print_r($arts); print '</pre>'; die();
		// Actualiza el coste del artículo
		foreach ($arts as $k => $l)
		{
			// Calcula el precio de coste del artículo
			$stocks = $this->obj->m_articuloseccion->stocks($k);
			$stk = $stocks['nStockFirme'] + $stocks['nStockDeposito'];
			if ($stk > 0)
			{
				$a = $this->obj->m_articulo->load($k);
				$coste = $a['fPrecioCompra'] * $stk - ($arts[$k]['coste'] * $arts[$k]['cantidad']);
				if (($stk - $arts[$k]['cantidad']) != 0)
					$coste /= format_decimals($stk - $arts[$k]['cantidad']);
				$coste = format_decimals($coste);
				// Actualiza el artículo
				if (!$this->obj->m_articulo->update($k, array('fPrecioCompra' => $coste)))
				{
					$this->_set_error_message($this->obj->m_articulo->error_message());
					$this->db->trans_rollback();
					return FALSE;
				}
			}
		}

		// Actualiza el estado del pedido
		if (!$this->update($id, array(
        	'nIdEstado' 	=> ALBARAN_ENTRADA_STATUS_EN_PROCESO,
			'dCierre' 		=> null
		)))
		{
			$this->db->trans_rollback();
			return FALSE;
		}
		//$this->db->trans_rollback();
		$this->db->trans_commit();
		return TRUE;
	}

	/**
	 * Calcula cuando se compró la última vez a un proveedor
	 * @param date $fecha Fecha a partir de la que tener en cuenta las compras
	 * @return MSG
	 */
	function ultimacompra($fecha)
	{
		set_time_limit(0);
		$fecha = format_mssql_date($fecha);
		$sql = ($this->db->dbdriver == 'mssql')?"UPDATE Prv_Proveedores_Editoriales_Tipos
				SET dCompra = dUltimaCompra
				FROM Prv_Proveedores_Editoriales_Tipos a (NOLOCK)
					INNER JOIN (
					SELECT alb.nIdProveedor,
						f.nIdTipo,
						f.nIdEditorial, 
						MAX(pd.dFechaEntrega) dUltimaCompra
					FROM Doc_LineasPedidosRecibidas lpr (NOLOCK)
						INNER JOIN Doc_LineasAlbaranesEntrada la (NOLOCK)
							ON lpr.nIdLineaAlbaran = la.nIdLinea
						INNER JOIN Doc_LineasPedidoProveedor lp (NOLOCK)
							ON lpr.nIdLineaPedido = lp.nIdLinea
						INNER JOIN Doc_AlbaranesEntrada alb (NOLOCK)
							ON alb.nIdAlbaran = la.nIdAlbaran
						INNER JOIN Doc_PedidosProveedor pd (NOLOCK)
							ON pd.nIdPedido = lp.nIdPedido
						INNER JOIN Cat_Fondo f (NOLOCK)
							ON la.nIdLibro = f.nIdLibro
					WHERE lp.dAct >= {$fecha}
					GROUP BY alb.nIdProveedor,
						f.nIdTipo,
						f.nIdEditorial
				) b
					ON a.nIdProveedor = b.nIdProveedor AND
					  	a.nIdTipo = b.nIdTipo AND
						a.nIdEditorial = b.nIdEditorial":
			"UPDATE Prv_Proveedores_Editoriales_Tipos a
					INNER JOIN (
					SELECT alb.nIdProveedor,
						f.nIdTipo,
						f.nIdEditorial, 
						MAX(pd.dFechaEntrega) dUltimaCompra
					FROM Doc_LineasPedidosRecibidas lpr 
						INNER JOIN Doc_LineasAlbaranesEntrada la 
							ON lpr.nIdLineaAlbaran = la.nIdLinea
						INNER JOIN Doc_LineasPedidoProveedor lp 
							ON lpr.nIdLineaPedido = lp.nIdLinea
						INNER JOIN Doc_AlbaranesEntrada alb 
							ON alb.nIdAlbaran = la.nIdAlbaran
						INNER JOIN Doc_PedidosProveedor pd 
							ON pd.nIdPedido = lp.nIdPedido
						INNER JOIN Cat_Fondo f 
							ON la.nIdLibro = f.nIdLibro
					WHERE lp.dAct >= {$fecha}
					GROUP BY alb.nIdProveedor,
						f.nIdTipo,
						f.nIdEditorial
				) b
					ON a.nIdProveedor = b.nIdProveedor AND
					  	a.nIdTipo = b.nIdTipo AND
						a.nIdEditorial = b.nIdEditorial
				SET dCompra = dUltimaCompra";
		$this->db->query($sql);
		
		#$this->db->query('ALTER TABLE Cat_Fondo DISABLE TRIGGER ALL');
		
		$sql = ($this->db->dbdriver == 'mssql')?"UPDATE Cat_Fondo
		SET dUltimaVenta = als.dSalida
		FROM Cat_Fondo f (NOLOCK)
			INNER JOIN (
				SELECT nIdLibro, MAX(dCreacion) dSalida
				FROM Doc_LineasAlbaranesSalida (NOLOCK)
				WHERE Doc_LineasAlbaranesSalida.dAct >= {$fecha}
				GROUP BY nIdLibro) als
				ON als.nIdLibro = f.nIdLibro":
		"UPDATE Cat_Fondo f
			INNER JOIN (
				SELECT nIdLibro, MAX(dCreacion) dSalida
				FROM Doc_LineasAlbaranesSalida 
				WHERE Doc_LineasAlbaranesSalida.dAct >= {$fecha}
				GROUP BY nIdLibro) als
				ON als.nIdLibro = f.nIdLibro
			SET dUltimaVenta = als.dSalida";
		$this->db->query($sql);
		
		$sql = ($this->db->dbdriver == 'mssql')?"UPDATE Cat_Fondo
		SET dUltimaCompra = al.dEntrada
		FROM Cat_Fondo f (NOLOCK)
			INNER JOIN (
				SELECT nIdLibro, MAX(dCreacion) dEntrada
				FROM Doc_LineasAlbaranesEntrada (NOLOCK)
				WHERE Doc_LineasAlbaranesEntrada.dAct >= {$fecha}
				GROUP BY nIdLibro) al
				ON al.nIdLibro = f.nIdLibro":
			"UPDATE Cat_Fondo f
				INNER JOIN (
					SELECT nIdLibro, MAX(dCreacion) dSalida
					FROM Doc_LineasAlbaranesSalida 
					WHERE Doc_LineasAlbaranesSalida.dAct >= {$fecha}
					GROUP BY nIdLibro) als
					ON als.nIdLibro = f.nIdLibro
				SET dUltimaVenta = als.dSalida";
		$this->db->query($sql);
		
		$sql = ($this->db->dbdriver == 'mssql')?"UPDATE Cat_Fondo
		SET dUltimaCompra = al.dEntrada
		FROM Cat_Fondo f (NOLOCK)
			INNER JOIN (
				SELECT nIdLibro, MAX(dCreacion) dEntrada
				FROM Doc_LineasAlbaranesEntrada (NOLOCK)
				WHERE Doc_LineasAlbaranesEntrada.dAct >= {$fecha}
				GROUP BY nIdLibro) al
				ON al.nIdLibro = f.nIdLibro":
		"UPDATE Cat_Fondo f
			INNER JOIN (
				SELECT nIdLibro, MAX(dCreacion) dEntrada
				FROM Doc_LineasAlbaranesEntrada
				WHERE Doc_LineasAlbaranesEntrada.dAct >= {$fecha}
				GROUP BY nIdLibro) al
				ON al.nIdLibro = f.nIdLibro
			SET dUltimaCompra = al.dEntrada";
		$this->db->query($sql);
		
		#$this->db->query('ALTER TABLE Cat_Fondo ENABLE TRIGGER ALL');
		
		return TRUE; 
	}
	
	/**
	 * Lee las líneas del albarán y los pedidos pendientes
	 * @param int $id Id del albarán
	 * @return array doc => datos del documento, lineas => líneas por libro
	 */
	function get_lineas($id)
	{
		//Crea una línea por cada título, si están repetidos los acumula
		$doc = $this->load($id, 'lineas');
		$lineas = array();
		foreach ($doc['lineas'] as $linea)
		{
			if (isset($lineas[$linea['nIdLibro']]))
			{
				$lineas[$linea['nIdLibro']]['nCantidad'] += $linea['nCantidad'];
				$lineas[$linea['nIdLibro']]['nCantidadAsignada'] += $linea['nCantidadAsignada'];
			}
			else
			{
				$lineas[$linea['nIdLibro']] = $linea;
				// Pendientes
				$this->obj->load->model('compras/m_pedidoproveedorlineaex');
				$where = 'nIdEstado IN (' . LINEA_PEDIDO_PROVEEDOR_STATUS_PENDIENTE_DE_RECIBIR . ', ' . LINEA_PEDIDO_PROVEEDOR_STATUS_PARCIALMENTE_RECIBIDO . ')';
				$where .= " AND (Prv_Proveedores.nIdProveedor={$doc['nIdProveedor']})";
				$where .= " AND (nIdLibro={$linea['nIdLibro']})";
				$data = $this->obj->m_pedidoproveedorlineaex->get(null, null, 'Doc_PedidosProveedor.dFechaEntrega', 'ASC', $where);
				$lineas[$linea['nIdLibro']]['pendientes'] = $data;
			}
			$lineas[$linea['nIdLibro']]['lineas'][] = $linea;
		}
		return array(
				'doc' => $doc,
				'lineas' => $lineas
		);
	}

	/**
	 * Obtiene los cargos de un albarán de entrada
	 * @param int $id Id del albarán
	 * @return float
	 */
	function get_cargos($id)
	{
		$this->db->flush_cache();
		$this->db->select_sum('fImporte', 'Cargos')
		->from('Albaranes_TiposCargo')
		->where("nIdAlbaran = {$id}");
		$query = $this->db->get();
		$data = $this->_get_results($query);
		return (count($data) == 1)?$data[0]['Cargos']:0;
	}
	
	/**
	 * Asigna un albarán de salida a un pedido proveedor
	 * @param int $id Id del albarán de salida
	 * @param array Asignación 0 -> Id artículo, 
	 *                         1 -> Id línea de pedido, 
	 *                         2 -> Cantidad, 
	 *                         3 -> Nombre sección, 
	 *                         4 -> Id del pedido, 
	 *                         5 -> Id de la sección
	 *                         6 -> Id de la línea de concurso
	 */
	function asignar($id, $asig)
	{
		$this->obj->load->model('compras/m_pedidoproveedorlinearecibida');
		$this->obj->load->model('compras/m_albaranentradalinea');
		$this->obj->load->model('compras/m_pedidoproveedorlinea');
		$this->obj->load->model('catalogo/m_articuloseccion');
		$this->obj->load->model('concursos/m_pedidoconcursolinea');
		$this->obj->load->model('concursos/m_estadolineaconcurso');
		$data = $this->get_lineas($id);
		$doc = $data['doc'];
		$lineas = $data['lineas'];

		$messages = array();
		$this->db->trans_begin();
		
		foreach ($asig as $a)
		{
			$idl = $a[0];
			$idlnp = $a[1];
			$ct = $a[2];
			$sec = $a[3];
			$idp = $a[4];
			$ids = $a[5];
			$idlc = isset($a[6])?$a[6]:null;
			$message = array(
				'linea' 	=> $idl, 
				'pedido' 	=> $idp, 
				'titulo' 	=> $lineas[$idl]['cTitulo'], 
				'seccion' 	=> $sec, 
				'cantidad' 	=> $ct
				); 
			foreach ($lineas[$idl]['lineas'] as $k => $linea)
			{
				$pendientes = $linea['nCantidad'] - $linea['nCantidadAsignada'];
				while ($pendientes > 0 && $ct > 0)
				{
					$act = min($pendientes, $ct);
					// Crea la relación
					$data = array();
					$data['nIdLineaAlbaran'] = $linea['nIdLinea'];
					$data['nIdLineaPedido'] = $idlnp;
					$data['nCantidad'] = $act;
					#print_r($data);
					if ($this->obj->m_pedidoproveedorlinearecibida->insert($data) < 0)
					{
						$this->db->trans_rollback();
						$this->_set_error_message($this->obj->m_pedidoproveedorlinearecibida->error_message());
						return FALSE;
					}
					// Actualiza el stock de la sección
					$sec = $this->obj->m_articuloseccion->get(null, null, null, null, "nIdLibro={$idl} AND nIdSeccion={$ids}");
					if (count($sec) != 1)
					{
						$this->db->trans_rollback();
						$this->_set_error_message(sprintf($this->lang->line('seccion-libro-noexistente'), $idl, $ids));
						return FALSE;
					}
					$sec = $sec[0];
					$upd = array();
					($doc['bDeposito']) ? ($upd['nStockDeposito'] = $sec['nStockDeposito'] + $act) : ($upd['nStockFirme'] = $sec['nStockFirme'] + $act);
					if (!$this->obj->m_articuloseccion->update($sec['nIdSeccionLibro'], $upd))
					{
						$this->db->trans_rollback();
						$this->_set_error_message($this->obj->m_articuloseccion->error_message());
						return FALSE;
					}

					// Actualiza el stock de la línea del pedido de proveedor
					$ln = $this->obj->m_pedidoproveedorlinea->load($idlnp);
					#var_dump($ln);
					$upd = array();
					$upd['nRecibidas'] = $ln['nRecibidas'] + $act;
					if (!$this->obj->m_pedidoproveedorlinea->update($idlnp, $upd))
					{
						$this->db->trans_rollback();
						$this->_set_error_message($this->obj->m_pedidoproveedorlinea->error_message());
						return FALSE;
					}
					// Actualiza el estado de la línea del concurso
					if (isset($idlc))
					{
						$con = $this->obj->m_pedidoconcursolinea->load($idlc);
						if ($con['nIdEstado'] == CONCURSOS_ESTADO_LINEA_PEDIDO_AL_PROVEEDOR)
						{
							if (!$this->obj->m_pedidoconcursolinea->update($idlc, array(
									'nIdEstado' 				=> CONCURSOS_ESTADO_LINEA_RECIBIDO_PROVEEDOR,
									'nIdLineaAlbaranEntrada' 	=> $linea['nIdLinea'],
									'fPrecio'					=> $linea['fPrecio']
									)))
							{
								$this->db->trans_rollback();
								$this->_set_error_message($this->obj->m_pedidoconcursolinea->error_message());
								return FALSE;
							}						
							$message['cConcurso'] = $con['cConcurso'];
							$message['cBiblioteca'] = $con['cBiblioteca'];
						}
					}					

					// Aumenta las asignadas
					$linea['nCantidadAsignada'] += $act;
					$pendientes -= $act;
					$ct -= $act;
				}
				$lineas[$idl]['lineas'][$k]['nCantidadAsignada'] = $linea['nCantidadAsignada'];
				// Actualiza la línea de albarán
				if (!$this->obj->m_albaranentradalinea->update($linea['nIdLinea'], array('nCantidadAsignada' => $linea['nCantidadAsignada'])))
				{
					$this->db->trans_rollback();
					$this->_set_error_message($this->obj->m_albaranentradalinea->error_message());
					return FALSE;
				}
			}
			$messages[] = $message;
		}
		// ¿Es Suscripción?
		$suscripcion = $this->get_suscripcion($id);
		// Estado del albarán
		if (!$this->update($id, array('nIdEstado' => ALBARAN_ENTRADA_STATUS_ASIGNADO,
			'bSuscripcion' => ($suscripcion > 0))))
		{
			$this->db->trans_rollback();
			return FALSE;
		}

		$this->db->trans_commit();
		return $messages;
	}

	/**
	 * Quita la asignación de las líneas de un albarán a un pedido de proveedor
	 * @param int $id Id del albarán de entrada
	 * @return bool
	 */
	function desasignar($id)
	{
		// Carga los modelos y datos
		$this->obj->load->model('compras/m_pedidoproveedorlinearecibida');
		$this->obj->load->model('compras/m_albaranentradalinea');
		$this->obj->load->model('compras/m_pedidoproveedorlinea');
		$this->obj->load->model('catalogo/m_articuloseccion');
		$this->obj->load->model('concursos/m_pedidoconcursolinea');
		$this->obj->load->model('concursos/m_estadolineaconcurso');
		$alb = $this->load($id, 'lineas');
		if (empty($alb))
		{
			$this->_set_error_message($this->lang->line('registro_no_encontrado'));
			return FALSE;
		}		
		$this->db->trans_begin();

		// Para cada línea
		foreach ($alb['lineas'] as $linea)
		{
			if ($linea['nCantidadDevuelta'] > 0)
			{
				$this->db->trans_rollback();
				$this->_set_error_message(sprintf($this->lang->line('error-albaranentrada-devuelto'), $linea['cTitulo']));
				return FALSE;
			}
			$data = $this->obj->m_pedidoproveedorlinearecibida->get(null, null, null, null, "nIdLineaAlbaran={$linea['nIdLinea']}");
			$ct = 0;
			if (count($data) > 0)
			{
				foreach ($data as $l)
				{
					// Actualiza el stock de la línea del pedido de proveedor
					$idlnp = $l['nIdLineaPedido'];
					$ln = $this->obj->m_pedidoproveedorlinea->load($idlnp);
					$act = $l['nCantidad'];
					$upd = array();
					$upd['nRecibidas'] = $ln['nRecibidas'] - $act;
					$upd['nIdEstado'] = ($upd['nRecibidas'] == 0) ? LINEA_PEDIDO_PROVEEDOR_STATUS_PENDIENTE_DE_RECIBIR : LINEA_PEDIDO_PROVEEDOR_STATUS_PARCIALMENTE_RECIBIDO;
					#echo '<pre>'; print_r($upd); echo '</pre>';
					if (!$this->obj->m_pedidoproveedorlinea->update($idlnp, $upd))
					{
						$this->db->trans_rollback();
						$this->_set_error_message($this->obj->m_pedidoproveedorlinea->error_message());
						return FALSE;
					}

					// Actualiza el stock de la sección
					$idl = $ln['nIdLibro'];
					$ids = $ln['nIdSeccion'];
					$sec = $this->obj->m_articuloseccion->get(null, null, null, null, "nIdLibro={$idl} AND nIdSeccion={$ids}");
					if (count($sec) != 1)
					{
						$this->db->trans_rollback();
						$this->_set_error_message(sprintf($this->lang->line('seccion-libro-noexistente'), $idl, $ids));
						return FALSE;
					}
					$sec = $sec[0];
					$upd = array();
					($alb['bDeposito']) ? ($upd['nStockDeposito'] = $sec['nStockDeposito'] - $act) : ($upd['nStockFirme'] = $sec['nStockFirme'] - $act);
					#$upd['nStockRecibir'] = $sec['nStockRecibir'] + $act;
					if (!$this->obj->m_articuloseccion->update($sec['nIdSeccionLibro'], $upd))
					{
						$this->db->trans_rollback();
						$this->_set_error_message($this->obj->m_articuloseccion->error_message());
						return FALSE;
					}

					// Actualiza el estado de la línea del concurso
					if (isset($l['nIdLineaPedidoConcurso']))
					{
						$con = $this->obj->m_pedidoconcursolinea->load($l['nIdLineaPedidoConcurso']);
						if ($con['nIdEstado'] == CONCURSOS_ESTADO_LINEA_RECIBIDO_PROVEEDOR)
						{
							if (!$this->obj->m_pedidoconcursolinea->update($l['nIdLineaPedidoConcurso'], array(
									'nIdEstado' => CONCURSOS_ESTADO_LINEA_PEDIDO_AL_PROVEEDOR,
									'nIdLineaAlbaranEntrada' => null
								)))
							{
								$this->db->trans_rollback();
								$this->_set_error_message($this->obj->m_pedidoconcursolinea->error_message());
								return FALSE;
							}						
						}
						else
						{
							$this->db->trans_rollback();
							$this->_set_error_message(sprintf($this->lang->line('albaraneentrada-desasignar-pedido-concurso-error'), $ln['cTitulo'], $l['cConcurso'], $l['cBiblioteca']));
							return FALSE;
						}						
					}					

					// Elimina la línea de pedido recibida
					$ct += $l['nCantidad'];
					if (!$this->obj->m_pedidoproveedorlinearecibida->delete($l['nIdLinea']))
					{
						$this->db->trans_rollback();
						$this->_set_error_message($this->obj->m_pedidoproveedorlinearecibida->error_message());
						return FALSE;
					}
				}
				// Actualiza las asignadas
				if (!$this->obj->m_albaranentradalinea->update($linea['nIdLinea'], array('nCantidadAsignada' => $linea['nCantidadAsignada'] - $ct)))
				{
					$this->db->trans_rollback();
					$this->_set_error_message($this->obj->m_albaranentradalinea->error_message());
					return FALSE;
				}
			}
		}
		// Actualiza el estado del albarán de entrada
		if (!$this->update($id, array('nIdEstado' => ALBARAN_ENTRADA_STATUS_CERRADO)))
		{
			$this->db->trans_rollback();
			return FALSE;
		}

		// Final
		$this->db->trans_commit();
		return TRUE;
	}

	/**
	 * Devuelve los datos de cantidad y tipo de albarán de un artículo dado
	 * @param int $alb Id del albarán
	 * @param int $id Id del artículo
	 * @return array (nCantidad, fPrecioVenta, bDeposito)
	 */
	function datos_etiqueta($alb, $id)
	{
		$this->db->flush_cache();
		$this->db->select_sum('Doc_LineasAlbaranesEntrada.nCantidad', 'nCantidad')
		->select('Doc_LineasAlbaranesEntrada.fPrecioVenta')
		->select('Doc_AlbaranesEntrada.bDeposito')
		->from('Doc_LineasAlbaranesEntrada')
		->join('Doc_AlbaranesEntrada', 'Doc_AlbaranesEntrada.nIdAlbaran=Doc_LineasAlbaranesEntrada.nIdAlbaran')
		->where('Doc_LineasAlbaranesEntrada.nIdAlbaran=' . $alb)
		->where('Doc_LineasAlbaranesEntrada.nIdLibro=' . $id)
		->group_by('Doc_LineasAlbaranesEntrada.fPrecioVenta, Doc_AlbaranesEntrada.bDeposito');
		$query = $this->db->get();
		$data = $this->_get_results($query);
		return (count($data)>0)?$data[0]:null;		
	}
	
	/**
	 * Lista los pedidos de cliente que hay que servir con el albarán de entrada
	 * @param int $id Id del albarán
	 * @return array
	 */
	function pedidoscliente($id)
	{
		$this->db->flush_cache();
		$this->db->select('Doc_LineasPedidoCliente.nCantidad')
		->select($this->_date_field('Doc_LineasPedidoCliente.dCreacion', 'dCreacion'))
		->select('Cat_Secciones.cNombre cSeccion')
		->select('Cat_Fondo.nIdLibro, Cat_Fondo.cTitulo, Cat_Fondo.cISBN')
		->select('Doc_PedidosCliente.nIdPedido, Doc_PedidosCliente.cRefCliente, Doc_PedidosCliente.cRefInterna')
		->select('Doc_LineasPedidoCliente.cRefCliente cRefCliente2, Doc_LineasPedidoCliente.cRefInterna cRefInterna2')
		->select('Cli_Clientes.nIdCliente, Cli_Clientes.cNombre, Cli_Clientes.cApellido, Cli_Clientes.cEmpresa')
		->from('Doc_LineasAlbaranesEntrada')
		->join('Doc_LineasPedidoCliente', 'Doc_LineasPedidoCliente.nIdLibro=Doc_LineasAlbaranesEntrada.nIdLibro')
		->join('Doc_PedidosCliente', 'Doc_PedidosCliente.nIdPedido=Doc_LineasPedidoCliente.nIdPedido')
		->join('Cat_Fondo', 'Cat_Fondo.nIdLibro=Doc_LineasAlbaranesEntrada.nIdLibro')
		->join('Cat_Secciones', 'Cat_Secciones.nIdSeccion=Doc_LineasPedidoCliente.nIdSeccion')
		->join('Cli_Clientes', 'Cli_Clientes.nIdCliente=Doc_PedidosCliente.nIdCliente')
		->where('Doc_LineasAlbaranesEntrada.nIdAlbaran=' . $id)
		->where('Doc_LineasPedidoCliente.nIdEstado=1')
		->group_by('Doc_LineasPedidoCliente.nCantidad')
		->group_by($this->_date_field('Doc_LineasPedidoCliente.dCreacion'))
		->group_by('Cat_Secciones.cNombre')
		->group_by('Cat_Fondo.nIdLibro, Cat_Fondo.cTitulo, Cat_Fondo.cISBN')
		->group_by('Doc_PedidosCliente.nIdPedido, Doc_PedidosCliente.cRefCliente, Doc_PedidosCliente.cRefInterna')
		->group_by('Doc_LineasPedidoCliente.cRefCliente, Doc_LineasPedidoCliente.cRefInterna')
		->group_by('Cli_Clientes.nIdCliente, Cli_Clientes.cNombre, Cli_Clientes.cApellido, Cli_Clientes.cEmpresa');

		$query = $this->db->get();
		$data = $this->_get_results($query);		
		return $data;
	} 

	/**
	 * Obtiene las líneas de concurso que cumplen con el criterio y están pendientes de recibir
	 * @param  int $concurso  Id del concurso
	 * @param  int $idpv     Id del proveedor
	 * @return array 
	 */
	function lineasconcurso($concurso, $idpv)
	{
		$this->obj->load->model('concursos/m_estadolineaconcurso');
		$this->db->flush_cache();
		$this->db->select('Ext_LineasPedidoConcurso.nIdLineaPedidoConcurso, Cat_Fondo.nIdLibro, Cat_Fondo.cTitulo, Cat_Fondo.cAutores, Cat_Fondo.cISBN')
		->select('Cat_Editoriales.cNombre cEditorial')
		->select('Cat_Fondo.cCUser')
		->select($this->_date_field('Cat_Fondo.dCreacion', 'dCreacion'))
		->from('Ext_LineasPedidoConcurso')
		->join('Ext_Bibliotecas', 'Ext_Bibliotecas.nIdBiblioteca=Ext_LineasPedidoConcurso.nIdBiblioteca')
		->join('Doc_LineasPedidoProveedor', 'Doc_LineasPedidoProveedor.nIdLinea=Ext_LineasPedidoConcurso.nIdLineaPedidoProveedor')
		->join('Doc_PedidosProveedor', 'Doc_PedidosProveedor.nIdPedido=Doc_LineasPedidoProveedor.nIdPedido')
		->join('Cat_Fondo', 'Cat_Fondo.nIdLibro=Ext_LineasPedidoConcurso.nIdLibro')
		->join('Cat_Editoriales', 'Cat_Fondo.nIdEditorial=Cat_Editoriales.nIdEditorial', 'left')
		->where('Doc_PedidosProveedor.nIdProveedor=' . $idpv)
		->where('Ext_Bibliotecas.nIdConcurso=' . $concurso)
		->where('Ext_LineasPedidoConcurso.nIdEstado=' . CONCURSOS_ESTADO_LINEA_PEDIDO_AL_PROVEEDOR)
		->order_by('Cat_Fondo.cTitulo');
		$query = $this->db->get();
		$data = $this->_get_results($query);
		return $data;
	}

	/**
	 * Muestra los títulos consultados desde la asignación de un albarán de entrada
	 * @param int $id Id del albarán de entrada
	 * @return array
	 */
	function consultados($id)
	{
		$this->obj->load->model('concursos/m_estadolineaconcurso');
		$this->db->flush_cache();
		$this->db->select('Cat_Fondo.nIdLibro, Cat_Fondo.cTitulo, Cat_Fondo.cAutores, Cat_Fondo.cISBN')
		->select('Cat_Editoriales.cNombre cEditorial')
		->select('Doc_LineasAlbaranesEntrada.nCantidad')
		->from('Doc_LineasAlbaranesEntrada')
		->join('Cat_Fondo', 'Cat_Fondo.nIdLibro=Doc_LineasAlbaranesEntrada.nIdLibro')
		->join('Cat_Editoriales', 'Cat_Fondo.nIdEditorial=Cat_Editoriales.nIdEditorial', 'left')
		->join('Doc_AlbaranEntradaLineaVisto', 'Doc_AlbaranEntradaLineaVisto.nIdLibro=Doc_LineasAlbaranesEntrada.nIdLibro', 'left')
		->where('Doc_LineasAlbaranesEntrada.nIdAlbaran =' . $id)
		->where('Doc_AlbaranEntradaLineaVisto.nIdLineaVista IS NULL')
		->order_by('Cat_Fondo.cTitulo');
		$query = $this->db->get();
		$data = $this->_get_results($query);
		return $data;
	}

	/**
	 * Muestra las líneas de depósito liquidadas
	 * @param int $id Id del albarán de entrada
	 * @return array
	 */
	function liquidacion($id = null)
	{
		$this->db->flush_cache();
		$this->db->select('Cat_Fondo.nIdLibro, Cat_Fondo.cTitulo, Cat_Fondo.cAutores, Cat_Fondo.cISBN')
		->select('Cat_Editoriales.cNombre cEditorial')
		->select('Doc_LineasLiquidacionDeposito.nCantidad')
		->select('Doc_LiquidacionDepositos.nIdDocumento')
		->select($this->_date_field('Doc_LiquidacionDepositos.dFecha', 'dFecha'))
		->from('Doc_LineasAlbaranesEntrada')
		->join('Doc_LineasLiquidacionDeposito', 'Doc_LineasLiquidacionDeposito.nIdLineaEntrada=Doc_LineasAlbaranesEntrada.nIdLinea')
		->join('Doc_LiquidacionDepositos', 'Doc_LiquidacionDepositos.nIdDocumento=Doc_LineasLiquidacionDeposito.nIdDocumento')
		->join('Cat_Fondo', 'Cat_Fondo.nIdLibro=Doc_LineasAlbaranesEntrada.nIdLibro')
		->join('Cat_Editoriales', 'Cat_Fondo.nIdEditorial=Cat_Editoriales.nIdEditorial', 'left')
		->where('Doc_LineasAlbaranesEntrada.nIdAlbaran =' . $id)
		->order_by('Cat_Fondo.cTitulo');
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
			$this->db->select('Prv_Proveedores.cNombre, Prv_Proveedores.cApellido, Prv_Proveedores.cEmpresa');
			$this->db->join('Prv_Proveedores', 'Doc_AlbaranesEntrada.nIdProveedor = Prv_Proveedores.nIdProveedor');
			$this->db->select('Gen_Divisas.cSimbolo');
			$this->db->join('Gen_Divisas', 'Doc_AlbaranesEntrada.nIdDivisa= Gen_Divisas.nIdDivisa', 'left');
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onAfterSelect($data, $id)
	 */
	protected function onAfterSelect(&$data, $id = null) {
		if (parent::onAfterSelect($data, $id)) {
			$data['cProveedor'] = format_name($data['cNombre'], $data['cApellido'], $data['cEmpresa']);
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeDelete($id)
	 */
	protected function onBeforeDelete($id) {
		// Si el albarán no está en proceso, no se puede borrar
		$albaran = $this->load($id);
		if ($albaran['nIdEstado'] != DEFAULT_ALBARAN_ENTRADA_STATUS) 
		{
			$this->_set_error_message(sprintf($this->lang->line('error-albaranentrada-cerrado'), $id));
			return FALSE;
		}

		# Borra el enlace a SINLI
		$this->obj->load->model('sys/m_sinli');
		$docs = $this->obj->m_sinli->get(0, 0, 0, 0, 'cTipo=\'ENVIO\' AND nIdDocumento=' . $id);
		if (count($docs)> 0)
		{
			foreach ($docs as $doc)
			{
				if (!$this->obj->m_sinli->update($doc['nIdFichero'], array('nIdDocumento' => null)))
				{
					$this->_set_error_message($this->obj->m_sinli->error_message());
					return FALSE;
				}
			}			
		}

		# Borra el enlace a la suscripción

		$this->db->flush_cache();
		$this->db->select('nIdSuscripcion')
		->from('Sus_Suscripciones')
		->where("nIdUltimaEntrada = {$id}");
		$query = $this->db->get();
		$data = $this->_get_results($query);
		
		$sus = (count($data)>0)?$data[0]['nIdSuscripcion']:null;
		if (isset($sus))
		{
			$this->obj->load->model('suscripciones/m_suscripcion');
			$last = $this->obj->m_suscripcion->get_pedidosproveedor($sus, TRUE, TRUE);
			$last = (isset($last[0]['nIdAlbaran']))?$last[0]['nIdAlbaran']:null;
			$upd['nIdUltimaEntrada'] = $last;
			if (!$this->obj->m_suscripcion->update($sus, $upd))
			{
				$this->_set_error_message($obj->m_suscripcion->error_message());
				return FALSE;
			}
		}

		return parent::onBeforeDelete($id);
	}

	/**
	 * Comprueba si el pedido va al extranjero
	 * @param int $idd ID de la dirección
	 * @return bool, TRUE: si es extranjero, FALSE: no lo es
	 */
	protected function _es_extanjero($idd)
	{
		if (!isset($idd)) return TRUE;
		$this->obj->load->model('proveedores/m_direccion');
		$d = $this->obj->m_direccion->load($idd);
		return ($d['nIdPais'] != $this->config->item('bp.pais.local'));
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeUpdate($id, $data)
	 */
	protected function onBeforeUpdate($id, &$data)
	{
		if (parent::onBeforeUpdate($id, $data))
		{
			if (isset($id) && (isset($data['bPrecioLibre']) || isset($data['bDeposito'])))
			{
				$alb = $this->load($id);
				$data['bPrecioLibre'] = isset($data['bPrecioLibre'])?((int) format_tobool($data['bPrecioLibre'])):$alb['bPrecioLibre'];
				$data['bDeposito'] = isset($data['bDeposito'])?((int) format_tobool($data['bDeposito'])):$alb['bDeposito'];
				if (($alb['nIdEstado'] != ALBARAN_ENTRADA_STATUS_EN_PROCESO) 
				&& (((int)$data['bPrecioLibre'] != $alb['bPrecioLibre'])
				|| ((int)$data['bDeposito'] != $alb['bDeposito'])))
				{
					$this->_set_error_message($this->lang->line('error-albaranentrada-cerrado'));
					return FALSE;
				}
			}
			if (isset($data['nIdDireccion']))
			{
				$data['bExtranjero'] = $this->_es_extanjero($data['nIdDireccion']);
			}
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Trigger llamado Antes de insertar los datos
	 * @param array $data Registro a insertar
	 * @return bool, TRUE: correcto, FALSE: error, se cancela acción
	 */
	protected function onBeforeInsert(&$data)
	{
		if (parent::onBeforeInsert($data))
		{
			$data['bExtranjero'] = isset($data['nIdDireccion'])?$this->_es_extanjero($data['nIdDireccion']):TRUE;
			return TRUE;
		}

		return FALSE;
	}

}

/* End of file M_albaranentrada.php */
/* Location: ./system/application/models/compras/M_albaranentrada.php */
