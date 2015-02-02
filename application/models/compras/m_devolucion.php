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
define('DEVOLUCION_STATUS_EN_PROCESO', 1);
define('DEVOLUCION_STATUS_CERRADA', 2);
define('DEVOLUCION_STATUS_ENTREGADA', 3);

define('DEFAULT_DEVOLUCION_STATUS', DEVOLUCION_STATUS_EN_PROCESO);

/**
 * Devoluciones Proveedor
 *
 */
class M_devolucion extends MY_Model
{
	/**
	 * Base de datos OLTP
	 * @var string
	 */
	private $_prefix = '';

	/**
	 * Constructor
	 * @return M_devolucion
	 */
	function __construct()
	{
		$data_model = array(
            'nIdProveedor' 		=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'proveedores/proveedor/search', 'cProveedor')),
            'nIdDireccion' 		=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'proveedores/direccion/search')),
			'nIdTipoDevolucion'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'compras/tipodevolucion/search')),
			'cRefProveedor' 	=> array(),
            'cRefInterna' 		=> array(),
            'nIdDivisa' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'generico/divisa/search')),
            'fValorDivisa' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT),
            'nIdEstado' 		=> array(DATA_MODEL_DEFAULT_VALUE => DEFAULT_DEVOLUCION_STATUS, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'compras/estadodevolucion/search')),
            'dCierre' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATE),
            'dEntrega' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATE),
            'bDeposito' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),
            'nPaquetes' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
            'tNotasExternas' 	=> array(),
            'tNotasInternas' 	=> array(),
            'nLibros' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
            'fTotal' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_MONEY),
            
			'cIdShipping'		=> array(),
		);

		parent::__construct('Doc_Devoluciones', 'nIdDevolucion', 'nIdDevolucion', array('cRefProveedor', 'cRefInterna'), $data_model, TRUE);

		$this->_relations['lineas'] = array(
            'ref' => 'compras/m_devolucionlinea',
            'cascade' => TRUE,
            'type' => DATA_MODEL_RELATION_1N,
            'fk' => 'nIdDevolucion');

		$this->_relations['proveedor'] = array(
            'ref' => 'proveedores/m_proveedor',
            'fk' => 'nIdProveedor');

		$this->_relations['direccion'] = array(
            'ref' => 'proveedores/m_direccion',
            'fk' => 'nIdDireccion');

		$this->_prefix = $this->config->item('bp.oltp.database');

	}

	/**
	 * Cierra una devolución. Asigna las líneas del albarán a las líneas de la devolución.
	 * @param int $id Id de la devolución
	 * @return bool, TRUE: Cierre correcto, FALSE: Cierre no correcto
	 */
	function cerrar($id)
	{
		$dev = $this->load($id, 'lineas');
		// Estado en proceso
		if ($dev['nIdEstado'] != DEVOLUCION_STATUS_EN_PROCESO)
		{
			$this->_set_error_message(sprintf($this->lang->line('error-devolucion-cerrada'), $id));
			return FALSE;
		}

		$this->db->trans_begin();
		$obj = get_instance();
		$obj->load->model('compras/m_albaranentradalinea');
		$obj->load->model('compras/m_devolucionlinea');
		$obj->load->model('catalogo/m_articuloseccion');

		$total = 0;
		$libros = 0;
		foreach ($dev['lineas'] as $linea)
		{
			$data = null;
			if ($linea['nCantidad'] > 0)
			{
				// Busca el albarán de entrada de estas líneas
				$this->db->flush_cache();
				$this->db->select("Doc_LineasAlbaranesEntrada.nIdLinea")
				->select('Doc_LineasAlbaranesEntrada.nCantidad, Doc_LineasAlbaranesEntrada.nCantidadDevuelta')
				->select('Doc_LineasAlbaranesEntrada.fPrecio, Doc_LineasAlbaranesEntrada.fDescuento')
				->from('Doc_LineasAlbaranesEntrada')
				->join('Doc_AlbaranesEntrada', 'Doc_LineasAlbaranesEntrada.nIdAlbaran=Doc_AlbaranesEntrada.nIdAlbaran')
				->where('Doc_LineasAlbaranesEntrada.nCantidad - Doc_LineasAlbaranesEntrada.nCantidadDevuelta > 0')
				->where("Doc_LineasAlbaranesEntrada.nIdLibro={$linea['nIdLibro']}")
				->where('Doc_LineasAlbaranesEntrada.fDescuento < 100')
				->where('Doc_AlbaranesEntrada.nIdEstado <> 1')
				->order_by('Doc_LineasAlbaranesEntrada.dCreacion', 'DESC')
				->limit($linea['nCantidad']);
				$dp = $dev['bDeposito']?1:0;
				$this->db->where("ISNULL(Doc_AlbaranesEntrada.bDeposito,  0) = {$dp}");
				$query = $this->db->get();
				#print array_pop($this->db->queries); die();
				$data = $this->_get_results($query);
			}
			$ct = $linea['nCantidad'];
			$libros += $linea['nCantidad'];

			// Quita el stock
			$sec = $obj->m_articuloseccion->get(null, null, null, null, "nIdLibro = {$linea['nIdLibro']} AND nIdSeccion = {$linea['nIdSeccion']}");
			if (count($sec) == 0)
			{
				$id_s = $obj->m_articuloseccion->insert(array('nIdLibro' => $linea['nIdLibro'],  'nIdSeccion' => $linea['nIdSeccion']));
				if ($id_s < 0)
				{
					$this->_set_error_message($obj->m_articuloseccion->error_message());
					$this->db->trans_rollback();
					return FALSE;
				}
				$sec = $obj->m_articuloseccion->load($id_s);
			}
			else
			{
				$sec = $sec[0];
			}

			$upd = array('nStockADevolver' => $sec['nStockADevolver'] + $ct);
			/*if ($dev['bDeposito']) {
				$upd['nStockDeposito'] = $sec['nStockDeposito'] - $ct;
				} else {
				$upd['nStockFirme'] = $sec['nStockFirme'] - $ct;
				}*/

			if (!$obj->m_articuloseccion->update($sec['nIdSeccionLibro'], $upd))
			{
				$this->_set_error_message($obj->m_articuloseccion->error_message());
				$this->db->trans_rollback();
				return FALSE;
			}

			if (isset($data))
			{
				foreach ($data as $reg)
				{
					// Asigna las unidades devueltas
					$libre = $reg['nCantidad'] - $reg['nCantidadDevuelta'];
					$asignar = min($libre, $ct);
					$upd = array(
                    'nCantidadDevuelta' => $reg['nCantidadDevuelta'] + $asignar,
					);
					if (!$obj->m_albaranentradalinea->update($reg['nIdLinea'], $upd))
					{
						$this->_set_error_message($obj->m_albaranentradalinea->error_message());
						$this->db->trans_rollback();
						return FALSE;
					}

					// Precio y descuento del albarán de entrada original
					// Cantidad la asignada
					$upd = array(
	                    'fPrecio' 			=> $reg['fPrecio'],
	                    'fDescuento' 		=> $reg['fDescuento'],
	                    'nIdLineaAlbaran' 	=> $reg['nIdLinea'],
	                    'nIdEstado' 		=> DEVOLUCION_LINEA_STATUS_CERRADA,
	                    'nCantidad' 		=> $asignar
					);
					if (!$obj->m_devolucionlinea->update($linea['nIdLinea'], $upd))
					{
						$this->_set_error_message($obj->m_devolucionlinea->error_message());
						$this->db->trans_rollback();
						return FALSE;
					}
					$upd['fIVA'] = $linea['fIVA'];
					$upd['fRecargo'] = $linea['fRecargo'];
					$upd['nCantidad'] = $asignar;

					$totales = format_calculate_importes($upd);
					$total += $totales['fTotal'];

					$ct -= $asignar;

					// Se han asignado todas?
					if ($ct == 0)
					break;

					// Se crea una nueva línea
					unset($linea['nIdLinea']);
					$linea['nCantidad'] = $ct;
					$idl = $obj->m_devolucionlinea->insert($linea);
					if ($idl < 1)
					{
						$this->_set_error_message($obj->m_devolucionlinea->error_message());
						$this->db->trans_rollback();
						return FALSE;
					}
					$linea['nIdLinea'] = $idl;
				}
			}
			// No hay albaranes de entrada suficientes para esta devolucion...?
			if ($ct != 0)
			{
				$upd = array(
                    'nIdEstado' => DEVOLUCION_LINEA_STATUS_CERRADA,
					'nCantidad' => $ct
				);
				if (!$obj->m_devolucionlinea->update($linea['nIdLinea'], $upd))
				{
					$this->_set_error_message($obj->m_devolucionlinea->error_message());
					$this->db->trans_rollback();
					return FALSE;
				}
				$linea['nCantidad'] = $ct;
				$totales = format_calculate_importes($linea);
				$total += $totales['fTotal'];
			}
		}

		// Actualiza el estado de la devolucion
		#$data['_fTotal'] = $total;
		$data = array(
            'nIdEstado' => DEVOLUCION_STATUS_CERRADA,
			'fTotal'	=> $total,
			'nLibros'	=> $libros,
            'dCierre' 	=> time()
		);

		if (!$this->update($id, $data))
		{
			$this->db->trans_rollback();
			return FALSE;
		}
		$this->db->trans_commit();

		return TRUE;
	}

	/**
	 * Abre la devolución
	 * @param int $id Id de la devolución
	 * @return bool, TRUE: Cierre correcto, FALSE: Cierre no correcto
	 */
	function abrir($id)
	{
		$dev = $this->load($id, 'lineas');

		// Ha de estar cerrada
		if ($dev['nIdEstado'] != DEVOLUCION_STATUS_CERRADA) {
			$this->_set_error_message(sprintf($this->lang->line('error-devolucion-no-cerrada'), $id));
			return FALSE;
		}

		$this->db->trans_begin();
		$obj = get_instance();
		$obj->load->model('compras/m_albaranentradalinea');
		$obj->load->model('compras/m_devolucionlinea');
		$obj->load->model('catalogo/m_articuloseccion');

		foreach ($dev['lineas'] as $linea)
		{
			$ct = $linea['nCantidad'];

			// Quita el stock
			$sec = $obj->m_articuloseccion->get(null, null, null, null, "nIdLibro = {$linea['nIdLibro']} AND nIdSeccion = {$linea['nIdSeccion']}");
			$sec = $sec[0];
			$upd = array('nStockADevolver' => $sec['nStockADevolver'] - $ct);
			if (!$obj->m_articuloseccion->update($sec['nIdSeccionLibro'], $upd)) {
				$this->_set_error_message($obj->m_articuloseccion->error_message());
				$this->db->trans_rollback();
				return FALSE;
			}
			$reg = $obj->m_albaranentradalinea->load($linea['nIdLineaAlbaran']);

			$upd = array(
				'nCantidadDevuelta' => $reg['nCantidadDevuelta'] - $ct,
			);
			if (!$obj->m_albaranentradalinea->update($linea['nIdLineaAlbaran'], $upd))
			{
				$this->_set_error_message($obj->m_albaranentradalinea->error_message());
				$this->db->trans_rollback();
				return FALSE;
			}

			// Quita la referencia del albarán de entrada
			$upd = array(
                    'nIdLineaAlbaran' => null,
                    'nIdEstado' => DEVOLUCION_LINEA_STATUS_EN_PROCESO,
			);
			if (!$obj->m_devolucionlinea->update($linea['nIdLinea'], $upd))
			{
				$this->_set_error_message($obj->m_devolucionlinea->error_message());
				$this->db->trans_rollback();
				return FALSE;
			}
		}

		// Actualiza el estado de la devolucion
		#$data['_fTotal'] = $total;
		$data = array(
            'nIdEstado' => DEVOLUCION_STATUS_EN_PROCESO,
            'dCierre' => null
		);

		if (!$this->update($id, $data))
		{
			$this->db->trans_rollback();
			return FALSE;
		}
		$this->db->trans_commit();

		return TRUE;
	}

	/**
	 * Entrega una devolución.
	 * @param int $id Id de la devolución
	 * @return bool, TRUE: Entrega correcta, FALSE: Entrega no correcto
	 */
	function entregar($id) {
		$dev = $this->load($id, 'lineas');
		// Estado en proceso
		if ($dev['nIdEstado'] != DEVOLUCION_STATUS_CERRADA)
		{
			$this->_set_error_message(sprintf($this->lang->line('error-devolucion-no-cerrada'), $id));
			return FALSE;
		}

		$this->db->trans_begin();
		$obj = get_instance();
		$obj->load->model('compras/m_albaranentradalinea');
		$obj->load->model('compras/m_devolucionlinea');
		$obj->load->model('catalogo/m_articuloseccion');

		foreach ($dev['lineas'] as $linea)
		{
			// Busca el albarán de entrada de estas líneas
			$ct = $linea['nCantidad'];

			// Quita el stock
			$sec = $obj->m_articuloseccion->get(null, null, null, null, "nIdLibro = {$linea['nIdLibro']} AND nIdSeccion = {$linea['nIdSeccion']}");
			$sec = $sec[0];

			$upd = array('nStockADevolver' => $sec['nStockADevolver'] - $ct);
			if ($dev['bDeposito'])
			{
				$upd['nStockDeposito'] = $sec['nStockDeposito'] - $ct;
			}
			else
			{
				$upd['nStockFirme'] = $sec['nStockFirme'] - $ct;
			}

			if (!$obj->m_articuloseccion->update($sec['nIdSeccionLibro'], $upd))
			{
				$this->_set_error_message($obj->m_articuloseccion->error_message());
				$this->db->trans_rollback();
				return FALSE;
			}
		}

		// Actualiza el estado de la devolucion
		$data = array(
            'nIdEstado' => DEVOLUCION_STATUS_ENTREGADA,
            'dEntrega' => time()
		);

		if (!$this->update($id, $data))
		{
			$this->db->trans_rollback();
			return FALSE;
		}
		$this->db->trans_commit();

		return TRUE;
	}

	/**
	 * Crea una devolución con el contenido de otra rechazado
	 * @param int $id Id de la devolución
	 * @return int Id de la nueva devolución
	 */
	function contra($id)
	{
		$d = $this->reg->load($id, TRUE);
		unset($d['nIdDevolucion']);
		unset($d['nIdEstado']);
		unset($d['dCierre']);
		unset($d['dEntrega']);
		foreach ($d['lineas'] as $k => $v)
		{
			$d['lineas'][$k]['nCantidad'] = -$v['nCantidad'];
			unset($d['lineas'][$k]['nIdLinea']);
		}
		$id_n = $this->reg->insert($d);
		return $id_n;
	}

	/**
	 * Coprueba el si hay suficiente stock para cerrar una devolución
	 * @param int $id Id de la devolución
	 * @return array
	 */
	function check($id, $estado = DEVOLUCION_STATUS_EN_PROCESO)
	{
		// Lee la devolución
		$d = $this->reg->load($id);
		if ($d['nIdEstado'] != $estado) {
			$this->_set_error_message(sprintf($this->lang->line('error-devolucion-cerrada'), $id));
			return FALSE;
		}

		// Leemos las líneas
		$this->db->flush_cache();
		$this->db->select('la.nIdLibro, la.nIdSeccion, la.nCantidad, la.nIdLinea')
		->select('sl.nStockFirme, sl.nStockDeposito, sl.nIdSeccionLibro')
		->select('s.cNombre cSeccion, f.cTitulo')
		->from("{$this->_tablename} a")
		->join('Doc_LineasDevolucion la', 'la.nIdDevolucion = a.nIdDevolucion')
		->join('Cat_Fondo f', 'la.nIdLibro = f.nIdLibro')
		->join('Cat_Secciones s', 'la.nIdSeccion = s.nIdSeccion')
		->join('Cat_Secciones_Libros sl', 'la.nIdLibro = sl.nIdLibro AND la.nIdSeccion = sl.nIdSeccion', 'left')
		->where("la.nIdDevolucion = {$id}");

		$query = $this->db->get();
		$data = $this->_get_results($query);
		#echo '<pre>'; var_dump($data); echo '</pre>'; die();

		$errores = array();

		// Si no tiene líneas se elimina
		if (count($data) == 0)
		{
			return $errores;
		}

		$stock = array();
		foreach ($data as $reg)
		{
			if ($reg['nCantidad'] > 0)
			{
				$pre = isset($stock[$reg['nIdSeccionLibro']]) ? $stock[$reg['nIdSeccionLibro']] : $reg[($d['bDeposito'] ? 'nStockDeposito' : 'nStockFirme')];
				if ($reg['nCantidad'] > $pre)
				{
					$reg['nExceso'] = $reg['nCantidad'] - $pre;
					$errores[] = $reg;
					$stock[$reg['nIdSeccionLibro']] = 0;
				}
				else
				{
					$stock[$reg['nIdSeccionLibro']] = $pre - $reg['nCantidad'];
				}
			}
		}
		return $errores;
	}

	/**
	 * Reliza devoluciones de todos los libros de una sección. Solo realiza una propuesta,
	 * no los crea
	 * @param int $id Id sección
	 * @param bool $habitual TRUE: Usa el proveedor habitual, FALSE: Usa el último al que se compró
	 * @return array
	 */
	function devolver_seccion($id, $habitual = FALSE)
	{
		//Stock
		$this->db->flush_cache();
		$this->db->select('
				f.nIdLibro, 
				f.cTitulo,
				f.cAutores,
				sl.nStockFirme, 
				sl.nStockDeposito')
		->select($this->db->isnull('nADevolverFirme', 0, 'nADevolverFirme'))
		->select($this->db->isnull('nADevolverDeposito', 0, 'nADevolverDeposito'))
		->from('Cat_Fondo f')
		->join('Cat_Secciones_Libros sl', 'f.nIdLibro = sl.nIdLibro')
		->join('(SELECT nIdSeccion, nIdLibro, SUM(nCantidad) nADevolverFirme 
				FROM Doc_LineasDevolucion d1
					INNER JOIN Doc_Devoluciones dv1 
						ON  d1.nIdDevolucion = dv1.nIdDevolucion
				WHERE dv1.bDeposito=0 AND dv1.nIdEstado IN (1,2)
				GROUP BY nIdSeccion, nIdLibro) d1', 
				'd1.nIdSeccion = sl.nIdSeccion AND d1.nIdLibro = sl.nIdLibro', 'left')
		->join('(SELECT nIdSeccion, nIdLibro, SUM(nCantidad) nADevolverDeposito 
				FROM Doc_LineasDevolucion d1
					INNER JOIN Doc_Devoluciones dv1 
						ON  d1.nIdDevolucion = dv1.nIdDevolucion
				WHERE dv1.bDeposito=1 AND dv1.nIdEstado IN (1,2)
				GROUP BY nIdSeccion, nIdLibro) d2', 
				'd2.nIdSeccion = sl.nIdSeccion AND d2.nIdLibro = sl.nIdLibro', 'left')
		->where("sl.nIdSeccion = {$id}")
		->where('(sl.nStockFirme + sl.nStockDeposito - sl.nStockADevolver) > 0')
		->order_by('f.cTitulo');
		if (!$habitual)
		{
			$this->db->select('p.nIdProveedor,p.cEmpresa,p.cNombre, p.cApellido')
			->select('lal.fIVA,
				lal.fDescuento,
				lal.fPrecio,
				lal.fPrecioDivisa')
			->join('(SELECT MAX(lal.nIdLinea) nIdLinea, nIdLibro
					FROM Doc_LineasAlbaranesEntrada lal (NOLOCK)
					GROUP BY nIdLibro) ae1', 'f.nIdLibro = ae1.nIdLibro', 'left')
			->join('Doc_LineasAlbaranesEntrada lal', 'lal.nIdLinea = ae1.nIdLinea', 'left')
			->join('Doc_AlbaranesEntrada ae2', 'lal.nIdAlbaran = ae2.nIdAlbaran', 'left')
			->join('Prv_Proveedores p', 'p.nIdProveedor = ae2.nIdProveedor', 'left');
		}
		set_time_limit(0);
		$query = $this->db->get();
		$data = $this->_get_results($query);
		#var_dump($data); die();
		#echo '<pre>'; print_r($this->db->queries); echo '</pre>'; die();
		//Agrupa por firme/deposito/proveedor
		$pedidos = array();
		$this->obj->load->model('catalogo/m_articulo');
		$this->obj->load->model('proveedores/m_proveedor');
		foreach ($data as $d)
		{
			if (!isset($d['nIdProveedor']))
			{
				// Busca el proveedor habitual
				$l = $this->obj->m_articulo->load($d['nIdLibro']);
				$pv = $this->obj->m_articulo->get_proveedor_habitual($l);
				if (!isset($pv))
				{
					$this->_set_error_message(sprintf($this->lang->line('devolver-seccion-no-proveedor')), $l['nIdLibro'], $l['cTitulo']);
					return FALSE;
				}
				$dto = $this->obj->m_articulo->get_descuento($d['nIdLibro'], $pv);
				$pv = $this->obj->m_proveedor->load($pv);
				$d['nIdProveedor'] = $pv['nIdProveedor'];
				$d['cProveedor'] = format_name($pv['cNombre'], $pv['cApellido'], $pv['cEmpresa']);
				$d['fIVA'] = $l['fIVA'];
				$d['fPrecio'] = $l['fPrecio'];
				$d['fPrecioDivisa'] = $l['fPrecio'];
				$d['fDescuento'] = $dto;
			}
			else
			{
				$d['cProveedor'] = format_name($d['cNombre'], $d['cApellido'], $d['cEmpresa']);
			}
			$d['nIdSeccion'] = $id;
			if ($d['nStockFirme'] > 0)
			{
				$d['nCantidad'] = $d['nStockFirme'] - (isset($d['nADevolverFirme'])?$d['nADevolverFirme']:0);
				if ($d['nCantidad']>0) $pedidos['firme'][$d['nIdProveedor']][] = $d;
			}

			if ($d['nStockDeposito'] > 0)
			{
				$d['nCantidad'] = $d['nStockDeposito'] - (isset($d['nADevolverDeposito'])?$d['nADevolverDeposito']:0);
				if ($d['nCantidad']>0) $pedidos['deposito'][$d['nIdProveedor']][] = $d;
			}
		}
		// Crea los pedidos agrupados
		$firmes = (isset($pedidos['firme'])) ? $this->_agrupar_pedidos($pedidos['firme'], false) : array();
		$depositos = (isset($pedidos['deposito'])) ? $this->_agrupar_pedidos($pedidos['deposito'], true) : array();
		return array_merge($firmes, $depositos);
	}

	/**
	 * Agrupa los pedidos según el número de títulos indicados en <b>devoluciones.lineaspedidoseccion</b>
	 * @param array $pedidos Pedidos a agrupar
	 * @param bool $deposito true: crea el pedido en depósito
	 * @return array
	 */
	private function _agrupar_pedidos($pedidos, $deposito)
	{
		$n_pedidos = array();
		foreach ($pedidos as $pv => $pedido)
		{
			$count = 0;
			$lineas = array();
			foreach ($pedido as $linea)
			{
				$lineas[] = $linea;
				$count++;
				if ($count >= $this->config->item('devoluciones.lineaspedidoseccion'))
				{
					//pedido listo
					$n_pedidos[] = array(
                        'nIdProveedor' => $pv,
                        'cProveedor' => $linea['cProveedor'],
                        'bDeposito' => $deposito,
                        'lineas' => $lineas
					);
					$count = 0;
					$lineas = array();
				}
			}
			if (count($lineas) > 0)
			{
				//último pedido
				$n_pedidos[] = array(
                    'nIdProveedor' => $pv,
                    'cProveedor' => $lineas[0]['cProveedor'],
                    'bDeposito' => $deposito,
                    'lineas' => $lineas
				);
			}
		}

		return $n_pedidos;
	}

	/**
	 * Listado de libros sin ventas en un periodo
	 * @param int $ids ID de la sección
	 * @param int $tipo 1: 1+ años, 2: 2+ años, 3: 3+ años
	 * @param string $orden Campo de orden del listado
	 * @param bool $nacional TRUE: Solo libros nacionales
	 * @return array
	 */
	function libros_sin_venta($ids = null, $tipo = null, $orden = 'cTitulo', $nacional = TRUE)
	{
		$this->db->flush_cache();
		$this->db->select_max('nIdVolcado', 'nIdVolcado')
		->from("{$this->_prefix}Ext_AntiguedadStockVolcados");

		$query = $this->db->get();
		$data = $this->_get_results($query);
		$id = $data[0]['nIdVolcado'];

		$obj = get_instance();
		if (isset($ids) && is_numeric($ids))
		{
			$obj->load->model('generico/m_seccion');
			$codigo = $obj->m_seccion->load($ids);
			$codigo_s = $codigo['cCodigo'];
		}
		if (!is_string($orden)) $orden = 'cTitulo';
		$this->db->flush_cache();
		$this->db->select("f.nIdLibro,
			f.cTitulo, 
			f.cAutores,
			s.cSeccion,
			p.cEmpresa, p.cNombre, p.cApellido,
			e.cNombre cEditorial,
			s.nIdSeccion,
			p.nIdProveedor")
		->from("{$this->_prefix}Ext_AntiguedadStockSecciones s")
		->join('Cat_Fondo f', 'f.nIdLibro = s.nIdLibro')
		->join('Cat_Secciones Seccion', 's.nIdSeccion = Seccion.nIdSeccion')
		->join('Cat_Secciones_Libros', 'Cat_Secciones_Libros.nIdLibro = f.nIdLibro AND Cat_Secciones_Libros.nIdSeccion = Seccion.nIdSeccion')
		->join('Prv_Proveedores p', 'p.nIdProveedor = f.nIdProveedor', 'left')
		->join('Cat_Editoriales e', 'e.nIdEditorial = f.nIdEditorial', 'left')
		->where("s.nIdVolcado = {$id}")
		->where('f.nIdOferta IS NULL')
		->where('f.nIdEstado <> 9')
		->where("(Cat_Secciones_Libros.nStockFirme + Cat_Secciones_Libros.nStockDeposito - Cat_Secciones_Libros.nStockADevolver) >= 1")
		->where("(f.dUltimaVenta < " . $this->db->dateadd('yy', -1, 'GETDATE()') . " OR f.dUltimaVenta IS NULL)");
		
		if (isset($ids))
			$this->db->where("(Seccion.cCodigo LIKE '{$codigo_s}.%' OR Seccion.nIdSeccion = {$ids})");
		
		if ($nacional) $this->db->where("f.cISBNBase LIKE '97884%'");

		$orden = str_replace('cProveedor', 'p.cEmpresa, p.cNombre, p.cApellido', $orden);
		$this->db->order_by($orden);

		if ($tipo == 1)
		{
			$this->db->select($this->db->int('s.fFirme2') . ' fFirme');
			$this->db->select('s.fImporte3 + fImporte2 + fImporte4 fImporte');
			$this->db->where($this->db->int('s.fFirme2') . ' > 0');
		}
		else if ($tipo == 2)
		{
			$this->db->select($this->db->int('s.fFirme3') . ' fFirme');
			$this->db->select('s.fImporte3 + fImporte4 fImporte');
			$this->db->where($this->db->int('s.fFirme3') . ' > 0');
		}
		else
		{
			$this->db->select($this->db->int('s.fFirme4') . ' fFirme');
			$this->db->select('fImporte4 fImporte');
			$this->db->where($this->db->int('s.fFirme4') . ' > 0');
		}

		$query = $this->db->get();
		$data = $this->_get_results($query);
		$obj->load->model('catalogo/m_articuloubicacion');

		foreach ($data as $k => $v)
		{
			$data[$k]['ubicaciones'] = $obj->m_articuloubicacion->get(null, null, 'm.cDescripcion', null, "nIdLibro = {$v['nIdLibro']}");
		}

		return $data;
	}

	/**
	 * Listado de libros sin ventas en un periodo
	 * @param int $ids ID de la sección
	 * @param int $idp Id del proveedor
	 * @param date $desde Fecha desde la que no hay ventas
	 * @param int $qty Cantidad mínima
	 * @param int $idm Id de la materia
	 * @param string $orden Campo de orden del listado
	 * @return array
	 */
	function libros_sin_venta2($ids = null, $idp = null, $desde = null, $qty = null, $idm = null, $orden = 'cTitulo')
	{
		$this->db->flush_cache();

		$obj = get_instance();
		if (isset($ids) && is_numeric($ids))
		{
			$obj->load->model('generico/m_seccion');
			$codigo = $obj->m_seccion->load($ids);
			$codigo_s = $codigo['cCodigo'];
		}
		if (isset($idm) && is_numeric($idm))
		{
			$obj->load->model('catalogo/m_materia');
			$codigo = $obj->m_materia->load($ids);
			$codigo_m = $codigo['cCodMateria'];
		}
		if (!isset($qty) || !is_numeric($qty)) $qty = 1;
		
		$desde = format_mssql_date($desde);
		$desdewhere ="";

		$this->db->flush_cache();
		$this->db->select("Cat_Fondo.nIdLibro,
			Cat_Fondo.cTitulo, 
			Cat_Fondo.cAutores,
			Cat_Fondo.fPrecio fImporte,
			Cat_Secciones.cNombre cSeccion,
			Prv_Proveedores.cEmpresa, Prv_Proveedores.cNombre, Prv_Proveedores.cApellido,
			Cat_Editoriales.cNombre cEditorial,
			Cat_Secciones.nIdSeccion,
			Cat_Secciones_Libros.nStockFirme + Cat_Secciones_Libros.nStockDeposito - Cat_Secciones_Libros.nStockADevolver fFirme,
			Prv_Proveedores.nIdProveedor")
		->select($this->_date_field('Entrada.dEntrada', 'dEntrada'))
		->select($this->_date_field('Cat_Fondo.dUltimaVenta', 'dUltimaVenta'))		
		->from('Cat_Fondo')
		->join('Cat_Secciones_Libros', 'Cat_Secciones_Libros.nIdLibro = Cat_Fondo.nIdLibro')
		->join('Cat_Secciones', 'Cat_Secciones.nIdSeccion = Cat_Secciones_Libros.nIdSeccion')
		->join('Cat_Editoriales', 'Cat_Editoriales.nIdEditorial = Cat_Fondo.nIdEditorial', 'left')
		->join('(SELECT nIdLibro, MAX(dCreacion) dEntrada FROM Doc_LineasAlbaranesEntrada (NOLOCK) GROUP BY nIdLibro) Entrada', 'Entrada.nIdLibro = Cat_Fondo.nIdLibro', 'left')
		->join('Prv_Proveedores', 'Prv_Proveedores.nIdProveedor = ISNULL(Cat_Fondo.nIdProveedorManual, Cat_Editoriales.nIdProveedor)', 'left')
		->where("(Cat_Secciones_Libros.nStockFirme - Cat_Secciones_Libros.nStockADevolver) >= {$qty}")
		->where("(Entrada.dEntrada IS NULL OR (Entrada.dEntrada < " . $this->db->dateadd('d', 1, $desde) . "))")
		->where("Cat_Fondo.nIdLibro NOT IN (
		SELECT nIdLibro
		FROM Doc_LineasAlbaranesSalida al (NOLOCK)
		WHERE (al.dCreacion > {$desde})
		GROUP BY nIdLibro
		HAVING SUM(nCantidad) > 0)"
		);
		if (isset($codigo_m))
		{
			$this->db->join('Cat_Libros_Materias', 'Cat_Libros_Materias.nIdLibro = Cat_Fondo.nIdLibro', 'left');
			$this->db->join('Cat_Materias', 'Cat_Libros_Materias.nIdMateria = Cat_Materias.nIdMateria', 'left');
			$this->db->where("(Cat_Materias.cCodMateria LIKE '{$codigo_m}.%' OR Cat_Materias.nIdMateria = {$idm})");
		}
		if (isset($codigo_s)) $this->db->where("(Cat_Secciones.cCodigo LIKE '{$codigo_s}.%' OR Cat_Secciones.nIdSeccion = {$ids})");
		if (is_numeric($idp)) $this->db->where("Prv_Proveedores.nIdProveedor = {$idp}");
		
		
		if (isset($orden) && $orden != '' && $orden !== FALSE) 
		{
			$orden = str_replace('cProveedor', 'Prv_Proveedores.cEmpresa, Prv_Proveedores.cNombre, Prv_Proveedores.cApellido', $orden);
			$orden = str_replace('s.cSeccion', 'Cat_Secciones.cNombre', $orden);
			$this->db->order_by($orden);
		}

		$query = $this->db->get();
		$data = $this->_get_results($query);
		$obj->load->model('catalogo/m_articuloubicacion');

		foreach ($data as $k => $v)
		{
			$data[$k]['ubicaciones'] = $obj->m_articuloubicacion->get(null, null, 'm.cDescripcion', null, "nIdLibro = {$v['nIdLibro']}");
		}

		return $data;
	}

	/**
	 * Listado de libros sin ventas en un periodo
	 * @param int $ids ID de la sección
	 * @param int $idp Id del proveedor
	 * @param int $idm Id de la materia
	 * @param string $orden Campo de orden del listado
	 * @return array
	 */
	function libros_sin_venta3($ids = null, $idp = null, $idm = null, $orden = 'cTitulo')
	{
		$this->db->flush_cache();

		$obj = get_instance();
		if (isset($ids) && is_numeric($ids))
		{
			$obj->load->model('generico/m_seccion');
			$codigo = $obj->m_seccion->load($ids);
			$codigo_s = $codigo['cCodigo'];
		}
		if (isset($idm) && is_numeric($idm))
		{
			$obj->load->model('catalogo/m_materia');
			$codigo = $obj->m_materia->load($ids);
			$codigo_m = $codigo['cCodMateria'];
		}

		$this->db->flush_cache();
		$this->db->select_max('nIdVolcado')
		->from("{$this->_prefix}Ext_AntiguedadStockVolcados");
		$query = $this->db->get();
		if(!$query) return null;

		$volcado = $query->row_array();
		$volcado = $volcado['nIdVolcado'];

		$this->db->flush_cache();
		$this->db->select("Cat_Fondo.nIdLibro,
			Cat_Fondo.cTitulo, 
			Cat_Fondo.cAutores,
			Cat_Fondo.fPrecio fImporte,
			Cat_Secciones.cNombre cSeccion,
			Prv_Proveedores.cEmpresa, Prv_Proveedores.cNombre, Prv_Proveedores.cApellido,
			Cat_Editoriales.cNombre cEditorial,
			Cat_Secciones.nIdSeccion,
			Cat_Secciones_Libros.nStockFirme + Cat_Secciones_Libros.nStockDeposito - Cat_Secciones_Libros.nStockADevolver fFirme,
			Prv_Proveedores.nIdProveedor")
		->select($this->_date_field('Cat_Fondo.dUltimaCompra', 'dEntrada'))
		->select($this->_date_field('Cat_Fondo.dUltimaVenta', 'dUltimaVenta'))
		->from('Cat_Fondo')
		->join('Cat_Secciones_Libros', 'Cat_Secciones_Libros.nIdLibro = Cat_Fondo.nIdLibro')
		->join('Cat_Secciones', 'Cat_Secciones.nIdSeccion = Cat_Secciones_Libros.nIdSeccion')
		->join("{$this->_prefix}Ext_AntiguedadStockSecciones", "{$this->_prefix}Ext_AntiguedadStockSecciones.nIdLibro=Cat_Secciones_Libros.nIdLibro AND {$this->_prefix}Ext_AntiguedadStockSecciones.nIdSeccion=Cat_Secciones_Libros.nIdSeccion")
		->join('Cat_Editoriales', 'Cat_Editoriales.nIdEditorial = Cat_Fondo.nIdEditorial', 'left')
		->join('Prv_Proveedores', 'Prv_Proveedores.nIdProveedor = ISNULL(Cat_Fondo.nIdProveedorManual, Cat_Editoriales.nIdProveedor)', 'left')
		->where("(Cat_Secciones_Libros.nStockFirme - Cat_Secciones_Libros.nStockADevolver) > 1")
		->where("{$this->_prefix}Ext_AntiguedadStockSecciones.nIdVolcado={$volcado}")
		->where("{$this->_prefix}Ext_AntiguedadStockSecciones.fFirme2 + {$this->_prefix}Ext_AntiguedadStockSecciones.fFirme3 + {$this->_prefix}Ext_AntiguedadStockSecciones.fFirme4 > 0");

		if (isset($codigo_m))
		{
			$this->db->join('Cat_Libros_Materias', 'Cat_Libros_Materias.nIdLibro = Cat_Fondo.nIdLibro', 'left');
			$this->db->join('Cat_Materias', 'Cat_Libros_Materias.nIdMateria = Cat_Materias.nIdMateria', 'left');
			$this->db->where("(Cat_Materias.cCodMateria LIKE '{$codigo_m}.%' OR Cat_Materias.nIdMateria = {$idm})");
		}
		if (isset($codigo_s)) $this->db->where("(Cat_Secciones.cCodigo LIKE '{$codigo_s}.%' OR Cat_Secciones.nIdSeccion = {$ids})");
		if (is_numeric($idp)) $this->db->where("Prv_Proveedores.nIdProveedor = {$idp}");		
		
		if (isset($orden) && $orden != '' && $orden !== FALSE) 
		{
			$orden = str_replace('cProveedor', 'Prv_Proveedores.cEmpresa, Prv_Proveedores.cNombre, Prv_Proveedores.cApellido', $orden);
			$orden = str_replace('s.cSeccion', 'Cat_Secciones.cNombre', $orden);
			$this->db->order_by($orden);
		}

		$query = $this->db->get();
		$data = $this->_get_results($query);
		$obj->load->model('catalogo/m_articuloubicacion');

		foreach ($data as $k => $v)
		{
			$data[$k]['ubicaciones'] = $obj->m_articuloubicacion->get(null, null, 'm.cDescripcion', null, "nIdLibro = {$v['nIdLibro']}");
		}

		return $data;
	}

	/**
	 * Cierra una devolución. Asigna las líneas del albarán a las líneas de la devolución.
	 * @param int $id Id de la devolución
	 * @return bool, TRUE: Cierre correcto, FALSE: Cierre no correcto
	 */
	function reasignar()
	{
		$this->db->flush_cache();
		$this->db->select('*')
		->from('Doc_LineasDevolucion')
		->where('nIdAlbaran IS NOT NULL AND nIdLineaAlbaran IS NULL');
		$query = $this->db->get();
		$lineas = $this->_get_results($query);

		$this->db->trans_begin();
		$obj = get_instance();
		$obj->load->model('compras/m_albaranentradalinea');
		$obj->load->model('compras/m_devolucionlinea');

		$count = 0;
		foreach ($lineas as $linea)
		{
			// Busca el albarán de entrada de estas líneas
			$this->db->flush_cache();
			$this->db->select("Doc_LineasAlbaranesEntrada.nIdLinea")
			->from('Doc_LineasAlbaranesEntrada')
			->where("Doc_LineasAlbaranesEntrada.nIdLibro={$linea['nIdLibro']}")
			->where("Doc_LineasAlbaranesEntrada.nIdAlbaran={$linea['nIdAlbaran']}");
			$query = $this->db->get();
			$data = $this->_get_results($query);
			if (count($data) > 0)
			{
				$data = $data[0];

				// Precio y descuento del albarán de entrada original
				// Cantidad la asignada
				$upd = array(
            	'nIdLineaAlbaran' => $data['nIdLinea'],
				);
				if (!$obj->m_devolucionlinea->update($linea['nIdLinea'], $upd)) {
					$this->_set_error_message($obj->m_devolucionlinea->error_message());
					$this->db->trans_rollback();
					return -1;
				}
				++$count;
			}
		}

		$this->db->trans_commit();

		return $count;
	}

	/**
	 * Artículos en devoluciones por entregar
	 * @param int $ids Id de la sección
	 * @param int $pv ID del proveedor
	 * @return array, 'total' => Los totales por secciones principales, 
	 * 'lineas' => Las líneas, 
	 * 'devoluciones' => Las devoluciones
	 */
	function a_entregar($ids = null, $pv = null)
	{
		# Última antigüedad
		$this->db->flush_cache();
		$this->db->select_max('nIdVolcado', 'nIdVolcado')
		->from("{$this->_prefix}Ext_AntiguedadStockVolcados");

		$query = $this->db->get();
		$data = $this->_get_results($query);
		$id = $data[0]['nIdVolcado'];
		
		$obj = get_instance();
		if (isset($ids) && is_numeric($ids))
		{
			$obj->load->model('generico/m_seccion');
			$codigo = $obj->m_seccion->load($ids);
			$codigo_s = $codigo['cCodigo'];
		}
				
		$this->db->flush_cache();
		$this->db->select('fFirme1, fFirme2, fFirme3, fFirme4')
		->select('Doc_LineasDevolucion.nCantidad, Doc_LineasDevolucion.fCoste')
		->select('Doc_LineasDevolucion.nIdSeccion')
		->select('Cat_Secciones.cNombre, Cat_Secciones.cCodigo')
		->select('Doc_LineasDevolucion.nIdDevolucion')
		->select('Cat_Fondo.nIdLibro, Cat_Fondo.cTitulo, Cat_Fondo.cISBN')
		->select('Prv_Proveedores.cNombre, Prv_Proveedores.cApellido, Prv_Proveedores.cEmpresa')
		->select($this->_date_field('Doc_Devoluciones.dCierre', 'dCierre'))
		->from('Doc_Devoluciones')
		->join('Prv_Proveedores', 'Doc_Devoluciones.nIdProveedor=Prv_Proveedores.nIdProveedor')
		->join('Doc_LineasDevolucion', 'Doc_Devoluciones.nIdDevolucion=Doc_LineasDevolucion.nIdDevolucion')
		->join('Cat_Fondo', 'Cat_Fondo.nIdLibro=Doc_LineasDevolucion.nIdLibro')
		->join('Cat_Secciones', 'Cat_Secciones.nIdSeccion=Doc_LineasDevolucion.nIdSeccion')
		->join($prefix.'Ext_AntiguedadStockSecciones a','a.nIdSeccion=Doc_LineasDevolucion.nIdSeccion AND a.nIdLibro=Doc_LineasDevolucion.nIdLibro AND a.nIdVolcado='.$id, 'left')
		->where('Doc_Devoluciones.nIdEstado=2')
		->order_by('Cat_Secciones.cNombre, (Doc_LineasDevolucion.fCoste * Doc_LineasDevolucion.nCantidad)');		

		if (is_numeric($ids))
			$this->db->where("(Cat_Secciones.cCodigo LIKE '{$codigo_s}.%' OR Cat_Secciones.nIdSeccion = {$ids})");
		if (is_numeric($pv))
			$this->db->where("Doc_Devoluciones.nIdProveedor={$pv}");

		$query = $this->db->get();
		$lineas = $this->_get_results($query);
		#var_dump($pv);
		#echo '<pre>'; print_r($this->db->queries); #die();
		$data = array();
		$codes = array();
		$devs = array();
		for($i=0; $i < count($lineas); $i++)
		{
			$cantidad = $lineas[$i]['nCantidad'];
			$firme1 = $firme2 = $firme3 = $firme4 = 0;
			$act = min($cantidad, $lineas[$i]['fFirme4']);
			$firme4 = $act * $lineas[$i]['fCoste'];
			$cantidad -= $act; 
			if ($cantidad > 0)
			{
				$act = min($cantidad, $lineas[$i]['fFirme3']);
				$firme3 = $act * $lineas[$i]['fCoste'];
				$cantidad -= $act; 				
			}
			if ($cantidad > 0)
			{
				$act = min($cantidad, $lineas[$i]['fFirme2']);
				$firme2 = $act * $lineas[$i]['fCoste'];
				$cantidad -= $act; 				
			}
			if ($cantidad > 0)
			{
				$firme1 = $cantidad * $lineas[$i]['fCoste'];
			}
			$codigo =preg_split('/\./', $lineas[$i]['cCodigo']);
			$codigo = $codigo[0];
			if (!isset($data[$codigo]))
			{
				$data[$codigo] = array(
					'firme1' => $firme1, 
					'firme2' => $firme2, 
					'firme3' => $firme3, 
					'firme4' => $firme4,
					'cantidad' => $lineas[$i]['nCantidad']);
				$codes[] = $codigo;
			}		
			else 
			{
				$data[$codigo]['firme1'] += $firme1;
				$data[$codigo]['firme2'] += $firme2;
				$data[$codigo]['firme3'] += $firme3;
				$data[$codigo]['firme4'] += $firme4;
				$data[$codigo]['cantidad'] += $lineas[$i]['nCantidad'];
			}
			if (!isset($devs[$lineas[$i]['nIdDevolucion']]))
			{
				$devs[$lineas[$i]['nIdDevolucion']] = array(
				'firme1' => $firme1, 
				'firme2' => $firme2, 
				'firme3' => $firme3, 
				'firme4' => $firme4,
				'cNombre' => $lineas[$i]['cNombre'], 
				'cApellido' => $lineas[$i]['cApellido'], 
				'cEmpresa' => $lineas[$i]['cEmpresa'], 
				'cantidad' => $lineas[$i]['nCantidad'], 
				'dCierre' => $lineas[$i]['dCierre']);
			}		
			else 
			{
				$devs[$lineas[$i]['nIdDevolucion']]['firme1'] += $firme1;
				$devs[$lineas[$i]['nIdDevolucion']]['firme2'] += $firme2;
				$devs[$lineas[$i]['nIdDevolucion']]['firme3'] += $firme3;
				$devs[$lineas[$i]['nIdDevolucion']]['firme4'] += $firme4;
				$devs[$lineas[$i]['nIdDevolucion']]['cantidad'] += $lineas[$i]['nCantidad'];
			}
			$lineas[$i]['firme1'] = $firme1;
			$lineas[$i]['firme2'] = $firme2;
			$lineas[$i]['firme3'] = $firme3;
			$lineas[$i]['firme4'] = $firme4;
			$lineas[$i]['codigo'] = $codigo;
		}
		if (!empty($codes))
		{
			$this->db->flush_cache();
			$this->db->select('nIdSeccion, cNombre')
			->from('Cat_Secciones')
			->where('nIdSeccion IN (' . implode(',', $codes) . ')')
			->order_by('cNombre');
			$query = $this->db->get();
			$data2 = $this->_get_results($query);
			foreach($data2 as $k=>$v)
			{
				if (isset($data[$v['nIdSeccion']]))
					$data2[$k] = array_merge($v, $data[$v['nIdSeccion']]);
			}
		}
		else
		{
			$data2['ALL'] = $data;
		}
		#var_dump($data); die();
		return array('lineas' => $lineas, 'total' => $data2, 'devoluciones' => $devs);
		
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
			$this->db->join('Prv_Proveedores', "Prv_Proveedores.nIdProveedor = {$this->_tablename}.nIdProveedor", 'left');
			$this->db->select('Doc_TipoDevolucion.cDescripcion cTipoDevolucion');
			$this->db->join('Doc_TipoDevolucion', "Doc_TipoDevolucion.nIdTipoDevolucion = {$this->_tablename}.nIdTipoDevolucion", 'left');
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onAfterSelect($data, $id)
	 */
	protected function onAfterSelect(&$data, $id = null)
	{
		if (parent::onAfterSelect($data, $id))
		{
			$data['cProveedor'] = format_name($data['cNombre'], $data['cApellido'], $data['cEmpresa']);
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeDelete($id)
	 */
	protected function onBeforeDelete($id)
	{
		// Si el documento no está en proceso, no se puede borrar
		$doc = $this->load($id);
		if ($doc['nIdEstado'] != DEFAULT_DEVOLUCION_STATUS)
		{
			$this->_set_error_message(sprintf($this->lang->line('error-devolucion-cerrada'), $id));
			return FALSE;
		}
		return parent::onBeforeDelete($id);
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeUpdate($id, $data)
	 */
	protected function onBeforeUpdate($id, &$data)
	{
		#echo 'en onBeforeUpdate';
		#var_dump($_GET);
		#var_dump($this);
		//echo 'En onBeforeUpdate';
		if (parent::onBeforeUpdate($id, $data))
		{
			if (isset($id) && isset($data['dEntrega']) && (!isset($data['nIdEstado']) || $data['nIdEstado'] != DEVOLUCION_STATUS_ENTREGADA))
			{
				$devolucion = $this->load($id);
				// Cambio de cliente
				if ($devolucion['nIdEstado'] != DEVOLUCION_STATUS_ENTREGADA)
				{
					$this->_set_error_message($this->lang->line('devolucion-no-entregada'));
					return FALSE;
				}
			}
			return TRUE;
		}
		return FALSE;
	}
	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeUpdate($id, $data)
	 */
	protected function onBeforeInsert(&$data)
	{
		#echo 'en onBeforeUpdate';
		#var_dump($_GET);
		#var_dump($this);
		//echo 'En onBeforeUpdate';
		if (parent::onBeforeInsert($data))
		{
			if (isset($data['dEntrega']))
			{
				$this->_set_error_message($this->lang->line('devolucion-no-entregada'));
				return FALSE;
			}
			return TRUE;
		}
		return FALSE;
	}
}

/* End of file M_devolucion.php */
/* Location: ./system/application/models/compras/M_devolucion.php */
