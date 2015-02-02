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
#define('PEDIDO_PROVEEDOR_STATUS_CERRADO', 2);

define('PEDIDO_PROVEEDOR_STATUS_CANCELADO', 5);
define('PEDIDO_PROVEEDOR_STATUS_CANCELADO_Y_PARCIALMENTE_RECIBIDO', 6);
define('PEDIDO_PROVEEDOR_STATUS_EN_CREACION', 1);
define('PEDIDO_PROVEEDOR_STATUS_ENVIADO', 2);
define('PEDIDO_PROVEEDOR_STATUS_PARCIALMENTE_RECIBIDO', 4);
define('PEDIDO_PROVEEDOR_STATUS_RECIBIDO', 3);

define('DEFAULT_PEDIDO_PROVEEDOR_STATUS', PEDIDO_PROVEEDOR_STATUS_EN_CREACION);

/**
 * Pedidos Proveedor
 *
 */
class M_Pedidoproveedor extends MY_Model
{
	/**
	 * Constructor
	 * @return M_Pedidoproveedor
	 */
	function __construct()
	{
		$data_model = array(
            'nIdProveedor' 		=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'proveedores/proveedor/search', 'cProveedor')),
            'nIdDireccion' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
            'nIdEntrega' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
            'cRefProveedor' 	=> array(),
            'cRefInterna' 		=> array(),
            'nIdDivisa' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'generico/divisa/search')),
            'fValorDivisa' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT),
            'nIdEstado' 		=> array(DATA_MODEL_DEFAULT_VALUE => DEFAULT_PEDIDO_PROVEEDOR_STATUS, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'compras/estadopedidoproveedor/search')),
            'dFechaEntrega' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATE),
            'bDeposito' 		=> array(DATA_MODEL_DEFAULT_VALUE => 0, DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),
            'bRevistas' 		=> array(DATA_MODEL_DEFAULT_VALUE => 0, DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),
            'bRenovacion' 		=> array(DATA_MODEL_DEFAULT_VALUE => 0, DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),
            'bBloqueado' 		=> array(DATA_MODEL_DEFAULT_VALUE => 0, DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),
            'tNotasExternas' 	=> array(),
            'tNotasInternas' 	=> array(),
            'nLibros' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
            'fTotal' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_MONEY),
            'bEnviadoSINLI'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATE),
			'nIdSeccion'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'generico/seccion/search')),
			'nIdConcurso'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'concursos/concurso/search')),
		);

		parent::__construct('Doc_PedidosProveedor', 'nIdPedido', 'nIdPedido', array('cRefProveedor', 'cRefInterna', 'Cat_Secciones.cNombre'), $data_model, TRUE);
		$this->_alias = array(
			'cProveedor'	=> array('Prv_Proveedores.cNombre, Prv_Proveedores.cApellido, Prv_Proveedores.cEmpresa', DATA_MODEL_TYPE_STRING),
			'nDiasEntrega'	=> array('dFechaEntrega'),
			'nDias'			=> array('dCreacion'),
		);

		$this->_relations['lineas'] = array(
            'ref' => 'compras/m_pedidoproveedorlinea',
            'cascade' => TRUE,
            'type' => DATA_MODEL_RELATION_1N,
            'fk' => 'nIdPedido');

		$this->_relations['proveedor'] = array(
            'ref' => 'proveedores/m_proveedor',
            'fk' => 'nIdProveedor');

		$this->_relations['direccion'] = array(
            'ref' => 'proveedores/m_direccion',
            'fk' => 'nIdDireccion');

		$this->_relations['entrega'] = array(
            'ref' => 'proveedores/m_direccion',
            'fk' => 'nIdEntrega');
			
		$this->_relations['pedidosuscripcion'] = array(
            'ref' => 'suscripciones/m_pedidosuscripcion',
            'cascade' => TRUE,
            'type' => DATA_MODEL_RELATION_1N,
            'fk' => 'nIdPedido');
	}

	/**
	 * Líneas de pedido asignada
	 * @param int $id Id del pedido
	 * @return array
	 */
	function asignadas($id)
	{
		$this->db->flush_cache();
		$this->db->select('Doc_LineasPedidoProveedor.nIdLinea, Doc_LineasPedidoProveedor.nCantidad, Doc_LineasPedidosRecibidas.nCantidad nAsignadas')
		->from('Doc_LineasPedidoProveedor')
		->join('Doc_LineasPedidosRecibidas', 'Doc_LineasPedidosRecibidas.nIdLineaPedido = Doc_LineasPedidoProveedor.nIdLinea')
		->where("Doc_LineasPedidoProveedor.nIdPedido = {$id}");
		$query = $this->db->get();
		$data = $this->_get_results($query);
		return $data;
	}

	/**
	 * Abre el pedido del proveedor
	 * @param int $id Id del pedido
	 * @return MSG
	 */
	function abrir($id)
	{
		$data = $this->asignadas($id);
		if (count($data) > 0)
		{
			$this->_set_error_message($this->lang->line('pedido-proveedor-lineas-asignadas-error'));
			return FALSE;
		}

		$obj = get_instance();
		$obj->load->model('compras/m_pedidoproveedorlinea');
		$obj->load->model('catalogo/m_articuloseccion');

		// Leemos las líneas
		$this->db->flush_cache();
		$this->db->select('la.nIdLibro, la.nIdSeccion, la.nCantidad, la.nIdLinea')
		->select('sl.nStockAPedir, sl.nStockRecibir, sl.nIdSeccionLibro')
		->select('Ext_LineasPedidoConcurso.nIdLineaPedidoConcurso, Ext_LineasPedidoConcurso.nIdEstado')
		->from('Doc_LineasPedidoProveedor la')
		->join('Cat_Secciones s', 'la.nIdSeccion = s.nIdSeccion')
		->join('Cat_Secciones_Libros sl', 'la.nIdLibro = sl.nIdLibro AND la.nIdSeccion = sl.nIdSeccion', 'left')
		->join('Ext_LineasPedidoConcurso', 'Ext_LineasPedidoConcurso.nIdLineaPedidoProveedor = la.nIdLinea', 'left')
		->where("la.nIdPedido = {$id}")
		->where("la.nIdEstado = 2");

		$query = $this->db->get();
		$data = $this->_get_results($query);

		// Si no tiene líneas se elimina
		if (count($data) == 0) return TRUE;

		$stock = array();
		$this->db->trans_begin();

		foreach ($data as $reg)
		{
			// Actualiza la línea de pedido
			if (!$obj->m_pedidoproveedorlinea->update($reg['nIdLinea'], array(
                        'nIdEstado' => DEFAULT_LINEA_PEDIDO_PROVEEDOR_STATUS))) 
			{
				$this->_set_error_message($obj->m_pedidoproveedorlinea->error_message());
				$this->db->trans_rollback();
				return FALSE;
			}
			# Actualiza el concurso
			if ($reg['nIdEstado'] == CONCURSOS_ESTADO_LINEA_PEDIDO_AL_PROVEEDOR)
			{
				if (!$obj->m_pedidoconcursolinea->update($reg['nIdLineaPedidoConcurso'], 
					array(						
						'nIdEstado' => CONCURSOS_ESTADO_LINEA_A_PEDIR
						)))
				{
					$this->_set_error_message($obj->m_pedidoconcursolinea->error_message());
					return FALSE;
				}
			}
		}

		// Actualiza el estado del pedido
		if (!$this->update($id, array(
                    'nIdEstado' => DEFAULT_PEDIDO_PROVEEDOR_STATUS,
                    'dFechaEntrega' => null
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
	 * Cierra un pedido a proveedor
	 * @param int $id Id del pedido
	 */
	function cerrar($id, $force = TRUE)
	{
		$pedido = $this->load($id, 'proveedor');
		if ($pedido['nIdEstado'] != DEFAULT_PEDIDO_PROVEEDOR_STATUS)
		{
			$this->_set_error_message($this->lang->line('error-pedidoproveedor-cerrado'));
			return FALSE;
		}

		// Leemos las líneas
		$this->db->flush_cache();
		$this->db->select('la.nIdLibro, la.nIdSeccion, la.nCantidad, la.nIdLinea')
		->select('la.fPrecio, la.fIVA, la.fRecargo, la.fDescuento')
		->select('sl.nStockAPedir, sl.nStockRecibir, sl.nIdSeccionLibro')
		->select('s.bBloqueada, s.cNombre')
		->select('Ext_LineasPedidoConcurso.nIdLineaPedidoConcurso, Ext_LineasPedidoConcurso.nIdEstado')
		->from('Doc_LineasPedidoProveedor la')
		->join('Cat_Secciones s', 'la.nIdSeccion = s.nIdSeccion')
		->join('Cat_Secciones_Libros sl', 'la.nIdLibro = sl.nIdLibro AND la.nIdSeccion = sl.nIdSeccion', 'left')
		->join('Ext_LineasPedidoConcurso', 'Ext_LineasPedidoConcurso.nIdLineaPedidoProveedor = la.nIdLinea', 'left')
		->where("la.nIdPedido  = {$id}");

		$query = $this->db->get();
		$data = $this->_get_results($query);

		// Si no tiene líneas se elimina
		if (count($data) == 0)
		{
			return $this->delete($id);
		}

		// Modelos de datos que se van a usar
		$obj = get_instance();
		$obj->load->model('compras/m_pedidoproveedorlinea');
		$obj->load->model('catalogo/m_articuloseccion');
		$obj->load->model('concursos/m_pedidoconcursolinea');
		$obj->load->model('concursos/m_estadolineaconcurso');

		// Id del anticipo
		#$idanticipo = $this->config->item('bp.anticipo.idarticulo');
		// Se comprueba línea a línea
		$this->db->trans_begin();
		// BUG #1556
		// Si hay más de una línea de pedido, solo resta el stock de la última
		// Se crea un array para ir almacenando los stocks
		$stock = array();
		$total = 0;
		$libros = 0;
		foreach ($data as $reg)
		{
			// Bloqueada?
			if ($reg['bBloqueada'] == 1)
			{
				$this->_set_error_message(sprintf($this->lang->line('albaranes-cerrar-seccion-bloqueada'), $reg['cNombre']));
				$this->db->trans_rollback();
				return FALSE;
			}

			$libros += $reg['nCantidad'];
			$totales = format_calculate_importes($reg);
			$total += $totales['fTotal'];

			// Actualiza la línea de pedido
			if (!$obj->m_pedidoproveedorlinea->update($reg['nIdLinea'], array(
                        'nIdEstado' => LINEA_PEDIDO_PROVEEDOR_STATUS_PENDIENTE_DE_RECIBIR))) 
			{
				$this->_set_error_message($obj->m_pedidoproveedorlinea->error_message());
				$this->db->trans_rollback();
				return FALSE;
			}
			# Actualiza el concurso
			if (($reg['nIdEstado'] == CONCURSOS_ESTADO_LINEA_A_PEDIR) || ($reg['nIdEstado'] == CONCURSOS_ESTADO_LINEA_EN_PROCESO))
			{
				if (!$obj->m_pedidoconcursolinea->update($reg['nIdLineaPedidoConcurso'], 
					array(
						'nIdEstado' => CONCURSOS_ESTADO_LINEA_PEDIDO_AL_PROVEEDOR
						)))
				{
					$this->_set_error_message($obj->m_pedidoconcursolinea->error_message());
					return FALSE;
				}
			}			
		}

		if (!$force)
		{
			if (is_numeric($pedido['proveedor']['fCompraMinima']) 
				&& ($pedido['proveedor']['fCompraMinima'] > 0)
				&& ($total < $pedido['proveedor']['fCompraMinima']))
			{
				$this->db->trans_rollback();
				$this->_set_error_message(sprintf($this->lang->line('pedidoproveedor-cerrar-importe-error'), $id, format_price($pedido['proveedor']['fCompraMinima'])));
				return FALSE;
			}
		}

		// Actualiza el estado del pedido
		if (!$this->update($id, array(
        	'nIdEstado' 	=> PEDIDO_PROVEEDOR_STATUS_ENVIADO,
			'fTotal'		=> $total,
			'nLibros'		=> $libros,
			'dFechaEntrega' => time()
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
	 * Asigna los totales de un pedido
	 * @param int $id Id del pedido
	 * @return TRUE: ok, FALSE: error
	 */
	function totales($id)
	{
		$pedido = $this->load($id);

		// Leemos las líneas
		$this->db->flush_cache();
		$this->db->select('la.nCantidad')
		->select('la.fPrecio, la.fIVA, la.fRecargo, la.fDescuento')
		->from('Doc_LineasPedidoProveedor la')
		->where("la.nIdPedido  = {$id}");

		$query = $this->db->get();
		$data = $this->_get_results($query);

		// Si no tiene líneas se elimina
		if (count($data) == 0)
		{
			return TRUE;
		}

		$total = 0;
		$libros = 0;
		foreach ($data as $reg)
		{

			$libros += $reg['nCantidad'];
			$totales = format_calculate_importes($reg);
			$total += $totales['fTotal'];
		}

		// Actualiza el estado del pedido
		if (!$this->update($id, array(
			'fTotal'		=> $total,
			'nLibros'		=> $libros
		)))
		{
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * Cancelar un pedido a proveedor
	 * @param int $id Id del pedido
	 */
	function cancelar($id)
	{
		$pedido = $this->load($id);
		if ($pedido['nIdEstado'] == DEFAULT_PEDIDO_PROVEEDOR_STATUS)
		{
			$this->_set_error_message($this->lang->line('error-pedidoproveedor-no-cerrado'));
			return FALSE;
		}
		// Carga los estados de las líneas
		$obj = get_instance();
		$obj->load->model('compras/m_pedidoproveedorlinea');

		// Leemos las líneas
		$this->db->flush_cache();
		$this->db->select('la.nIdLibro, la.nIdSeccion, la.nCantidad, la.nIdLinea, la.nIdSeccion, la.nIdEstado')
		->select('sl.nStockAPedir, sl.nStockRecibir, sl.nIdSeccionLibro')
		->select('Ext_LineasPedidoConcurso.nIdLineaPedidoConcurso, Ext_LineasPedidoConcurso.nIdEstado nIdEstado2')
		->from('Doc_LineasPedidoProveedor la')
		->join('Cat_Secciones_Libros sl', 'la.nIdLibro = sl.nIdLibro AND la.nIdSeccion = sl.nIdSeccion', 'left')
		->join('Ext_LineasPedidoConcurso', 'Ext_LineasPedidoConcurso.nIdLineaPedidoProveedor = la.nIdLinea', 'left')
		->where("la.nIdPedido  = {$id} AND la.nIdEstado IN (" . LINEA_PEDIDO_PROVEEDOR_STATUS_PENDIENTE_DE_RECIBIR . ', ' .
			LINEA_PEDIDO_PROVEEDOR_STATUS_PARCIALMENTE_RECIBIDO . ')');

		$query = $this->db->get();
		$data = $this->_get_results($query);

		// Si no tiene líneas se elimina
		if (count($data) == 0)
		{
			$this->_set_error_message($this->lang->line('error-pedidoproveedor-cancelar-nolineas'));
			return FALSE;
		}

		// Modelos de datos que se van a usar
		$obj->load->model('catalogo/m_articuloseccion');
		$obj->load->model('concursos/m_pedidoconcursolinea');
		$obj->load->model('concursos/m_estadolineaconcurso');

		// Se comprueba línea a línea
		$this->db->trans_begin();

		// Se crea un array para ir almacenando los stocks
		$stock = array();
		foreach ($data as $reg)
		{
			$id_stock = "{$reg['nIdSeccion']}_{$reg['nIdLibro']}";
			if (isset($stock[$id_stock]))
			{
				$reg['nStockRecibir'] = $stock[$id_stock]['nStockRecibir'];
				if (!isset($reg['nIdSeccionLibro']))
				$reg['nIdSeccionLibro'] = $stock[$id_stock]['nIdSeccionLibro'];
			}

			// Si no existe la relación con la sección, la crea
			// Actualiza la relación secciones-artículos
			$ct = $this->m_pedidoproveedorlinea->recibidas($reg['nIdLinea']);

			if (!isset($reg['nIdSeccionLibro']))
			{
				// Crea la relación
				$idsl = $obj->m_articuloseccion->insert(array(
                            'nIdSeccion' => $reg['nIdSeccion'],
                            'nIdLibro' => $reg['nIdLibro'],
                            'nStockRecibir' => -$reg['nCantidad'] + $ct
				));
				if ($idsl < 0)
				{
					$this->_set_error_message($obj->m_articuloseccion->error_message());
					$this->db->trans_rollback();
					return FALSE;
				}
				$stock[$id_stock]['nStockRecibir'] = -$reg['nCantidad'] + $ct;
				$stock[$id_stock]['nIdSeccionLibro'] = $idsl;
			}
			else
			{
				if (!$obj->m_articuloseccion->update($reg['nIdSeccionLibro'], array(
                            'nStockRecibir' => $reg['nStockRecibir'] - $reg['nCantidad'] + $ct))) 
				{
					$this->_set_error_message($obj->m_articuloseccion->error_message());
					$this->db->trans_rollback();
					return FALSE;
				}
				$stock[$id_stock]['nStockRecibir'] = $reg['nStockRecibir'] - $reg['nCantidad'] + $ct;
			}

			// Actualiza la línea de pedido
			$status = ($ct == 0) ? LINEA_PEDIDO_PROVEEDOR_STATUS_CANCELADO : LINEA_PEDIDO_PROVEEDOR_STATUS_CANCELADO_Y_PARCIALMENTE_RECIBIDO;
			if (!$obj->m_pedidoproveedorlinea->update($reg['nIdLinea'], array('nIdEstado' => $status)))
			{
				$this->_set_error_message($obj->m_pedidoproveedorlinea->error_message());
				$this->db->trans_rollback();
				return FALSE;
			}

			#Actualiza el concurso
			if (($reg['nIdEstado2'] == CONCURSOS_ESTADO_LINEA_A_PEDIR) || ($reg['nIdEstado2'] == CONCURSOS_ESTADO_LINEA_PEDIDO_AL_PROVEEDOR))
			{
				if (!$obj->m_pedidoconcursolinea->update($reg['nIdLineaPedidoConcurso'], 
					array(
						'nIdLineaPedidoProveedor' => null,
						'nIdEstado' => CONCURSOS_ESTADO_LINEA_EN_PROCESO
						)))
				{
					$this->_set_error_message($obj->m_pedidoconcursolinea->error_message());
					return FALSE;
				}
			}
		}

		// Actualiza el estado del pedido
		$status = ($pedido['nIdEstado'] == PEDIDO_PROVEEDOR_STATUS_PARCIALMENTE_RECIBIDO) ? PEDIDO_PROVEEDOR_STATUS_CANCELADO_Y_PARCIALMENTE_RECIBIDO : PEDIDO_PROVEEDOR_STATUS_CANCELADO;
		if (!$this->update($id, array('nIdEstado' => $status)))
		{
			$this->db->trans_rollback();
			return FALSE;
		}
		//$this->db->trans_rollback();
		$this->db->trans_commit();
		return TRUE;
	}

	/**
	 * Actualiza el estado de los pedido de proveedor
	 * @param int $last Fecha de la última modificación de pedido a comprobar
	 * @param int $id Id de pedido a actualizar
	 * @return bool
	 */
	function actualizar_estado($last = null, $id = null)
	{
		set_time_limit(0);
		$sql_base = ($this->db->dbdriver == 'mssql')?'UPDATE Doc_PedidosProveedor
			SET nIdEstado = %1
			WHERE nIdEstado IN (%2) AND nIdPedido IN (
				SELECT nIdPedido
				FROM Doc_LineasPedidoProveedor
				WHERE nIdEstado IN (%3)
				GROUP BY nIdPedido
			)':
			'UPDATE Doc_PedidosProveedor 
				inner join Doc_LineasPedidoProveedor
					on Doc_PedidosProveedor.nIdPedido = Doc_LineasPedidoProveedor.nIdPedido 
						and Doc_PedidosProveedor.nIdEstado IN (%2) 
						and Doc_LineasPedidoProveedor.nIdEstado IN (%3)
			SET Doc_PedidosProveedor.nIdEstado=%1';

		$sql = str_replace(array('%1', '%2', '%3'), array('2', '4', '2'), $sql_base);
		$this->db->query($sql);
		$count = $this->db->affected_rows();
		# Parcialmente recibido PEDIDO_PROVEEDOR_STATUS_PARCIALMENTE_RECIBIDO
		$sql = 'UPDATE Doc_PedidosProveedor
			SET nIdEstado = 4
			WHERE nIdEstado = 2 AND nIdPedido IN (
				SELECT nIdPedido
				FROM Doc_LineasPedidoProveedor
				WHERE nIdEstado IN (4)
				GROUP BY nIdPedido
			)';
		$sql = str_replace(array('%1', '%2', '%3'), array('4', '2', '4'), $sql_base);
		$this->db->query($sql);
		$count = $this->db->affected_rows();

		# Cancelados PEDIDO_PROVEEDOR_STATUS_CANCELADO
		$sql = 'UPDATE Doc_PedidosProveedor
			SET nIdEstado = 5
			WHERE nIdEstado IN (2, 4, 3) AND nIdPedido IN (
				SELECT nIdPedido
				FROM Doc_LineasPedidoProveedor
				WHERE nIdEstado IN (6, 7)
				GROUP BY nIdPedido
			)';
		$sql = str_replace(array('%1', '%2', '%3'), array('5', '2, 4, 3', '6, 7'), $sql_base);
		$this->db->query($sql);
		$count += $this->db->affected_rows();

		$sql = 'UPDATE Doc_PedidosProveedor
			SET nIdEstado = 6
			WHERE nIdEstado IN (5) AND nIdPedido IN (
				SELECT nIdPedido
				FROM Doc_LineasPedidoProveedor
				WHERE nIdEstado IN (7)
				GROUP BY nIdPedido
			)';
		$sql = str_replace(array('%1', '%2', '%3'), array('6', '5', '7'), $sql_base);
		$this->db->query($sql);
		$count += $this->db->affected_rows();

		/*
		define('PEDIDO_PROVEEDOR_STATUS_CANCELADO', 5);
		define('PEDIDO_PROVEEDOR_STATUS_CANCELADO_Y_PARCIALMENTE_RECIBIDO', 6);
		define('PEDIDO_PROVEEDOR_STATUS_EN_CREACION', 1);
		define('PEDIDO_PROVEEDOR_STATUS_ENVIADO', 2);
		define('PEDIDO_PROVEEDOR_STATUS_PARCIALMENTE_RECIBIDO', 4);
		define('PEDIDO_PROVEEDOR_STATUS_RECIBIDO', 3);
		*/
		# Cancelados PEDIDO_PROVEEDOR_STATUS_CANCELADO

		$sql = 'UPDATE Doc_PedidosProveedor
			SET nIdEstado = 2
			WHERE nIdEstado IN (5, 6) AND nIdPedido IN (
				SELECT nIdPedido
				FROM Doc_LineasPedidoProveedor
				WHERE nIdEstado IN (2)
				GROUP BY nIdPedido
			)';
		$sql = str_replace(array('%1', '%2', '%3'), array('2', '5,6', '2'), $sql_base);
		$this->db->query($sql);
		$count += $this->db->affected_rows();

		$sql = 'UPDATE Doc_PedidosProveedor
			SET nIdEstado = 4
			WHERE nIdEstado IN (5, 6) AND nIdPedido IN (
				SELECT nIdPedido
				FROM Doc_LineasPedidoProveedor
				WHERE nIdEstado IN (4)
				GROUP BY nIdPedido
			)';
		$sql = str_replace(array('%1', '%2', '%3'), array('4', '5,6', '4'), $sql_base);
		$this->db->query($sql);
		$count += $this->db->affected_rows();

		/*
		define('LINEA_PEDIDO_PROVEEDOR_STATUS_CANCELADO', 6);
		define('LINEA_PEDIDO_PROVEEDOR_STATUS_CANCELADO_Y_PARCIALMENTE_RECIBIDO', 7);
		define('LINEA_PEDIDO_PROVEEDOR_STATUS_DESCATALOGADO', 5);
		define('LINEA_PEDIDO_PROVEEDOR_STATUS_EN_PROCESO', 1);
		define('LINEA_PEDIDO_PROVEEDOR_STATUS_PARCIALMENTE_RECIBIDO', 4);
		define('LINEA_PEDIDO_PROVEEDOR_STATUS_PENDIENTE_DE_RECIBIR', 2);
		define('LINEA_PEDIDO_PROVEEDOR_STATUS_RECIBIDO', 3);
		*/

		# Recibido PEDIDO_PROVEEDOR_STATUS_RECIBIDO
		# si está descatalogado habría que poner recibido?
		$sql = 'UPDATE Doc_PedidosProveedor
			SET nIdEstado = 3
			WHERE nIdEstado = 2 AND nIdPedido IN (
				SELECT nIdPedido
				FROM Doc_LineasPedidoProveedor
				WHERE nIdEstado IN (3)
				GROUP BY nIdPedido
			)';
		$sql = str_replace(array('%1', '%2', '%3'), array('3', '2', '3'), $sql_base);
		$this->db->query($sql);
		$count += $this->db->affected_rows();

		# Recibido y Cancelado PEDIDO_PROVEEDOR_STATUS_RECIBIDO
		$sql = 'UPDATE Doc_PedidosProveedor
			SET nIdEstado = 3
			WHERE nIdEstado = 5 AND nIdPedido IN (
				SELECT nIdPedido
				FROM Doc_LineasPedidoProveedor
				WHERE nIdEstado IN (4)
				GROUP BY nIdPedido
			)';
		$sql = str_replace(array('%1', '%2', '%3'), array('3', '5', '4'), $sql_base);
		$this->db->query($sql);
		$count += $this->db->affected_rows();

		# Cancelado y Cancelado PEDIDO_PROVEEDOR_STATUS_RECIBIDO
		$sql = 'UPDATE Doc_PedidosProveedor
			SET nIdEstado = 3
			WHERE nIdEstado = 5 AND nIdPedido IN (
				SELECT nIdPedido
				FROM Doc_LineasPedidoProveedor
				WHERE nIdEstado IN (4)
				GROUP BY nIdPedido
			)';
		$sql = str_replace(array('%1', '%2', '%3'), array('3', '5', '4'), $sql_base);
		$this->db->query($sql);
		$count += $this->db->affected_rows();

		# Cancelado y Cancelado PEDIDO_PROVEEDOR_STATUS_PARCIALMENTE_RECIBIDO
		$sql = 'UPDATE Doc_PedidosProveedor
			SET nIdEstado = 4
			WHERE nIdEstado = 3 AND nIdPedido IN (
				SELECT nIdPedido
				FROM Doc_LineasPedidoProveedor
				WHERE nIdEstado IN (2)
				GROUP BY nIdPedido
			)';
		$sql = str_replace(array('%1', '%2', '%3'), array('4', '3', '2'), $sql_base);
		$this->db->query($sql);
		$count += $this->db->affected_rows();



		return array('last' => time(), 'count' => $count, 'act' => $count);
	}

	/**
	 * Limpiar los pedidos a proveedor sin líneas
	 * @return int Registros eliminados
	 */
	function limpiar()
	{
		$this->reg->delete_by('nIdPedido NOT IN (select nIdPedido	from Doc_LineasPedidoProveedor)');
		return $this->reg->get_count();
	}

	/**
	 * Unificador de documengtos
	 * @param int $id1 Id del documento destino
	 * @param int $id2 Id del documento repetida
	 * @return bool, TRUE: correcto, FALSE: incorrecto
	 */
	function unificar($id1, $id2)
	{
		set_time_limit(0);
		foreach($id2 as $k=>$v)
		{
			if ($id2[$k] == '') unset($id2[$k]);
		}
		$id_or = $id2;
		$id2 = implode(',', $id2);
		if ($id2 == '') return TRUE;

		// TRANS
		$this->db->trans_begin();

		$this->db->where("nIdPedido IN ({$id2})")
		->update('Doc_LineasPedidoProveedor', array('nIdPedido' => $id1));
		if ($this->_check_error())
		{
			$this->db->trans_rollback();
			return FALSE;
		}

		$this->db->where("nIdPedido IN ({$id2})")
		->update('Sus_PedidosSuscripcion', array('nIdPedido' => $id1));
		if ($this->_check_error())
		{
			$this->db->trans_rollback();
			return FALSE;
		}

		// Borrado
		$this->db->flush_cache();
		$this->db->where("nIdPedido IN ({$id2})")
		->delete('Doc_PedidosProveedor');
		if ($this->_check_error())
		{
			$this->db->trans_rollback();
			return FALSE;
		}

		// Limpieza de caches
		$this->clear_cache();

		// COMMIT
		$this->db->trans_commit();
		return TRUE;
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
			$this->db->select('Cat_Secciones.cNombre cSeccion');
			$this->db->join('Cat_Secciones', "Cat_Secciones.nIdSeccion = {$this->_tablename}.nIdSeccion", 'left');
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
			if (isset($data['dFechaEntrega']))
			{
				$data['nDiasEntrega'] = daysDifference($data['dFechaEntrega'], time());
			}
			if (isset($data['dCreacion']))
			{
				$data['nDias'] = daysDifference($data['dCreacion'], time());
			}
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
		// Si el albarán no está en proceso, no se puede borrar
		$albaran = $this->load($id);
		if ($albaran['nIdEstado'] != DEFAULT_PEDIDO_PROVEEDOR_STATUS)
		{
			$this->_set_error_message(sprintf($this->lang->line('error-pedidoproveedor-cerrado'), $id));
			return FALSE;
		}
		return parent::onBeforeDelete($id);
	}
}

/* End of file M_Pedidoproveedor.php */
/* Location: ./system/application/models/compras/M_Pedidoproveedor.php */
