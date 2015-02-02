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

define('ESTADO_LINEA_PEDIDO_CLIENTE_CANCELADO',					7);
define('ESTADO_LINEA_PEDIDO_CLIENTE_CANCELADO_Y_CATALOGADO',	8);
define('ESTADO_LINEA_PEDIDO_CLIENTE_CATALOGADA',				6);
define('ESTADO_LINEA_PEDIDO_CLIENTE_EN_ALBARAN',				4);
define('ESTADO_LINEA_PEDIDO_CLIENTE_EN_PROCESO',				1);
define('ESTADO_LINEA_PEDIDO_CLIENTE_NO_SE_PUEDE_SERVIR',		5);
define('ESTADO_LINEA_PEDIDO_CLIENTE_RECIBIDO',					2);
define('ESTADO_LINEA_PEDIDO_CLIENTE_RESERVADO',					3);

define('ESTADO_LINEA_PEDIDO_CLIENTE_ACEPTADO',					9);
define('ESTADO_LINEA_PEDIDO_CLIENTE_RECHAZADO',					10);
define('ESTADO_LINEA_PEDIDO_CLIENTE_PENDIENTE',					11);

define('ESTADO_LINEA_PEDIDO_CLIENTE_CANCELADA', 7);
define('ESTADO_LINEA_PEDIDO_CLIENTE_CANCELADA_Y_CATALOGADA', 8);
define('ESTADO_LINEA_PEDIDO_CLIENTE_RECIBIDA', 2);
define('ESTADO_LINEA_PEDIDO_CLIENTE_SERVIDA', 3);

define('ESTADO_LINEA_PEDIDO_CLIENTE_ENVIADO',					12);

/**
 * Estado líneas de pedido cliente por defecto
 * @var int
 */
define('DEFAULT_PEDIDO_CLIENTE_LINEA_STATUS', ESTADO_LINEA_PEDIDO_CLIENTE_EN_PROCESO);

/**
 * Líneas de pedido cliente
 * 
 *
 */
class m_pedidoclientelinea extends MY_Model
{
	/**
	 * Constructor
	 * @return m_pedidoclientelinea
	 */
	function __construct()
	{
		$obj = get_instance();
		$data_model = array(
			'nIdPedido'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'ventas/albaransalida/search')),
			'nIdSeccion'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'generico/seccion/search')),		
			'nIdLibro'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'catalogo/articulo/search')),		
			'nCantidad' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_DEFAULT_VALUE => 1),
			'nCantidadServida' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),

			'fPrecio' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT, DATA_MODEL_DEFAULT_VALUE => 0), 
			'fIVA' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT), 
			'fRecargo' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT), 
			'fDescuento' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT, DATA_MODEL_DEFAULT_VALUE => 0), 
			'fCoste' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT),
			
			'nIdEstado'		=> array(DATA_MODEL_DEFAULT_VALUE => DEFAULT_PEDIDO_CLIENTE_LINEA_STATUS, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'ventas/estadopedidoclientelinea/search')),
			
			'nIdInformacion'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'ventas/informacioncliente/search')),		
            'dFechaInformacion' => array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATE),
            
			'cRefCliente' 	=> array(), 
			'cRefInterna'	=> array(),

			'nIdAlbaranSal'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'ventas/albaransalida/search')),		
			'bAviso' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN, DATA_MODEL_DEFAULT_VALUE => FALSE),
			'dAviso' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATE),

			#'_fImporte' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT, DATA_MODEL_DEFAULT_VALUE => 0),
			#'_fBase' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT, DATA_MODEL_DEFAULT_VALUE => 0),
			#'_fDescuento' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT, DATA_MODEL_DEFAULT_VALUE => 0),
			#'_fImpuestos' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT, DATA_MODEL_DEFAULT_VALUE => 0),
			#'_fRecargo' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT, DATA_MODEL_DEFAULT_VALUE => 0),
			#'_fTotal' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT, DATA_MODEL_DEFAULT_VALUE => 0),
		);

		parent::__construct('Doc_LineasPedidoCliente', 'nIdLinea', 'nIdLinea', array('cRefCliente', 'cRefInterna'), $data_model, TRUE);
	}

	/**
	 * Cancelar la línea del pedido
	 * @param int $id Id del pedido
	 * @return bool FALSE: error, TRUE: se ha cancelado correctamente
	 */
	function cancelar($id)
	{
		$linea = $this->load($id);
		if (!in_array($linea['nIdEstado'], array(ESTADO_LINEA_PEDIDO_CLIENTE_EN_ALBARAN,
		ESTADO_LINEA_PEDIDO_CLIENTE_NO_SE_PUEDE_SERVIR, ESTADO_LINEA_PEDIDO_CLIENTE_CANCELADA,
		ESTADO_LINEA_PEDIDO_CLIENTE_CANCELADA_Y_CATALOGADA
		)))
		{
			if (!$this->update($linea['nIdLinea'], array('nIdEstado' => ESTADO_LINEA_PEDIDO_CLIENTE_CANCELADA)))
			{
				return FALSE;
			}
		}
		return TRUE;
	}

	/**
	 * Aceptar la línea del pedido en presupuesto
	 * @param int $id Id del pedido
	 * @return bool FALSE: error, TRUE: se ha cancelado correctamente
	 */
	function aceptar($id)
	{
		$linea = $this->load($id);
		if (in_array($linea['nIdEstado'], array(ESTADO_LINEA_PEDIDO_CLIENTE_PENDIENTE, 
				ESTADO_LINEA_PEDIDO_CLIENTE_RECHAZADO
		)))
		{
			if (!$this->update($id, array('nIdEstado' => ESTADO_LINEA_PEDIDO_CLIENTE_ACEPTADO)))
			{
				return FALSE;
			}
		}
		return TRUE;
	}

	/**
	 * Rechazar la línea del pedido en presupuesto
	 * @param int $id Id del pedido
	 * @return bool FALSE: error, TRUE: se ha cancelado correctamente
	 */
	function rechazar($id)
	{
		$linea = $this->load($id);
		if (in_array($linea['nIdEstado'], array(ESTADO_LINEA_PEDIDO_CLIENTE_PENDIENTE, 
				ESTADO_LINEA_PEDIDO_CLIENTE_ACEPTADO
		)))
		{
			if (!$this->update($linea['nIdLinea'], array('nIdEstado' => ESTADO_LINEA_PEDIDO_CLIENTE_RECHAZADO)))
			{
				return FALSE;
			}
		}
		return TRUE;
	}

	/**
	 * Marcar la línea del pedido como imposible de servir
	 * @param int $id Id de la línea
	 * @return bool FALSE: error, TRUE: se ha actualizado correctamente
	 */
	function imposibleservir($id, &$imposible)
	{
		$linea = $this->load($id);
		if ($linea['nIdEstado'] == ESTADO_LINEA_PEDIDO_CLIENTE_NO_SE_PUEDE_SERVIR)
		{
			$imposible = FALSE;

			if (!$this->update($linea['nIdLinea'], array('nIdEstado' => ESTADO_LINEA_PEDIDO_CLIENTE_EN_PROCESO)))
			{
				return FALSE;
			}
		}
		elseif (!in_array($linea['nIdEstado'], array(ESTADO_LINEA_PEDIDO_CLIENTE_EN_ALBARAN,
		ESTADO_LINEA_PEDIDO_CLIENTE_NO_SE_PUEDE_SERVIR, ESTADO_LINEA_PEDIDO_CLIENTE_CANCELADA,
		ESTADO_LINEA_PEDIDO_CLIENTE_CANCELADA_Y_CATALOGADA
		)))
		{
			$imposible = TRUE;
			if (!$this->update($linea['nIdLinea'], array('nIdEstado' => ESTADO_LINEA_PEDIDO_CLIENTE_NO_SE_PUEDE_SERVIR)))
			{
				return FALSE;
			}
		}
		return TRUE;
	}

	/**
	 * Marcar la línea del pedido como catalogada
	 * @param int $id Id de la línea
	 * @return bool FALSE: error, TRUE: se ha actualizado correctamente
	 */
	function catalogado($id)
	{
		$linea = $this->load($id);
		if (!in_array($linea['nIdEstado'], array(ESTADO_LINEA_PEDIDO_CLIENTE_EN_ALBARAN,
		ESTADO_LINEA_PEDIDO_CLIENTE_NO_SE_PUEDE_SERVIR, ESTADO_LINEA_PEDIDO_CLIENTE_CANCELADA,
		ESTADO_LINEA_PEDIDO_CLIENTE_CANCELADA_Y_CATALOGADA
		)))
		{
			$data['nIdEstado'] = ESTADO_LINEA_PEDIDO_CLIENTE_CATALOGADA;
			$data['nCantidadServida'] = $linea['nCantidad'];
			if (!$this->update($linea['nIdLinea'], $data))
			{
				return FALSE;
			}
		}
		return TRUE;
	}

	/**
	 * Marcar la línea del pedido como avisada
	 * @param int $id Id de la línea
	 * @return bool FALSE: error, TRUE: se ha actualizado correctamente
	 */
	function avisado($id, &$aviso)
	{
		$linea = $this->load($id);
		if (!isset($aviso))	$aviso = !$linea['bAviso'] || !isset($linea['bAviso']);
		$date = ($aviso)?time():null;
		if (!$this->update($linea['nIdLinea'], array('bAviso' => $aviso, 'dAviso' => $date)))
		{
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * Actualiza el precio de una líenea de pedido
	 * @param int $id Id de la línea
	 * @param int $tarifa Id de la tarifa
	 * @return MSG
	 */
	function actualizarprecio($id = null, $tarifa = null, $tarifascliente = null, $force = TRUE)
	{
		$this->obj->load->model('catalogo/m_articulo');

		$data = $this->load($id);

		if (!in_array($data['nIdEstado'], array(
			ESTADO_LINEA_PEDIDO_CLIENTE_EN_PROCESO,
			ESTADO_LINEA_PEDIDO_CLIENTE_NO_SE_PUEDE_SERVIR,
			ESTADO_LINEA_PEDIDO_CLIENTE_EN_ALBARAN,
			ESTADO_LINEA_PEDIDO_CLIENTE_SERVIDA,
			ESTADO_LINEA_PEDIDO_CLIENTE_CATALOGADA,
			ESTADO_LINEA_PEDIDO_CLIENTE_RECIBIDA			
		)))
		{
			$this->_set_error_message(sprintf($this->lang->line('pedido-linea-precio-no-estado'), $id));
			return FALSE;
		}

		$old = $data['fPrecio'];
		$old2 = $data['fCoste'];
		$art = $this->obj->m_articulo->load($data['nIdLibro'], 'tarifas');
		# Comprueba si el cliente tiene una tarifa asignada al tipo de artículo
		if (count($tarifascliente)>0)
		{
			foreach ($tarifascliente as $value) 
			{
				if ($value['nIdTipoLibro'] == $art['nIdTipo'])
				{
					$tarifa = $value['nIdTipoTarifa'];
					break;
				}
			}
		}
		$precio = format_get_tarifa($art, $art['tarifas'], $tarifa);
		if (!is_numeric($precio)) $precio = 0;
		if ($precio != $old || $force || $old2 != $art['fPrecioCompra'])
		{
			$res = $this->update($id, array('fPrecio' => $precio, 'fCoste' => $art['fPrecioCompra']));
			if (!$res) return FALSE;
			$res = array(
				'old' 		=> $old, 
				'new' 		=> $precio, 
				'art' 		=> $art,
				'oldcoste'	=> $old2, 
				'newcoste' 	=> $art['fPrecioCompra'], 
				'linea' 	=> $data);
			return $res;
		}
		if ($force)
		{
			$this->_set_error_message(sprintf($this->lang->line('pedido-linea-precio-sincambios'), $id));
			return FALSE;
		}
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
			$this->db->select('Cat_Fondo.cTitulo, Cat_Fondo.cAutores, Cat_Fondo.cISBN, Cat_Secciones.cNombre cSeccion, Cat_Editoriales.cNombre cEditorial, Cat_Fondo.nIdOferta, Cat_Fondo.bNoDto')
			->select('Cat_Fondo.fPrecio fPrecioL')
			->select('Cat_EstadosLibro.cDescripcion cEstadoLibro, Cat_Fondo.nIdEstado nIdEstadoLibro')
			->select('Doc_EstadosLineaPedidoCliente.cDescripcion cEstado')
			->select('Doc_InformacionCliente.cDescripcion cInformacion, Doc_InformacionCliente.nIdTipo nIdTipoInformacion')
			->join('Cat_Fondo', "Cat_Fondo.nIdLibro = {$this->_tablename}.nIdLibro")
			->join('Cat_EstadosLibro', 'Cat_EstadosLibro.nIdEstado = Cat_Fondo.nIdEstado', 'left')
			->join('Cat_Secciones', "Cat_Secciones.nIdSeccion = {$this->_tablename}.nIdSeccion")
			->join('Cat_Editoriales', 'Cat_Fondo.nIdEditorial = Cat_Editoriales.nIdEditorial', 'left')
			->join('Doc_EstadosLineaPedidoCliente', "Doc_EstadosLineaPedidoCliente.nIdEstado={$this->_tablename}.nIdEstado", 'left')
			->join('Doc_InformacionCliente', "Doc_InformacionCliente.nIdInformacion = {$this->_tablename}.nIdInformacion", 'left');
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Calcula los importes de las líneas
	 * @see system/application/libraries/MY_Model#onAfterSelect($data, $id)
	 */
	protected function onAfterSelect(&$data, $id = null)
	{
		if (parent::onAfterSelect($data, $id))
		{
			$importes = format_calculate_importes($data);
			$data = array_merge($data, $importes);
			$pvp = format_add_iva($data['fPrecioL'], $data['fIVA']);
			$data['fPVPL'] = $pvp;
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Acciones previas a INSERT/UPDATE
	 * @param array $data Datos
	 * @param int $id Id del registro si UPDATE
	 * @return bool
	 */
	protected function _pre_ins($data, $id = null)
	{
		if (isset($data['nIdEstado']) || isset($data['nCantidad']) || isset($data['nCantidadServida']))
		{
			$servir = 0;
			$reservado = 0;
			# Cantidades y sección
			if (isset($id))
			{				
				$pre = $this->load($id);
				#var_dump($pre);
				$data['nIdSeccion'] = $pre['nIdSeccion'];
				$data['nIdLibro'] = $pre['nIdLibro'];
				if (!isset($data['nIdEstado'])) $data['nIdEstado'] = $pre['nIdEstado'];
				if (!isset($data['nCantidad'])) $data['nCantidad'] = $pre['nCantidad'];
				if (!isset($data['nCantidadServida'])) $data['nCantidadServida'] = $pre['nCantidadServida'];
			}
			if (!isset($data['nCantidad'])) $data['nCantidad'] = 0;
			if (!isset($data['nCantidadServida'])) $data['nCantidadServida'] = 0;

			# Si no existe la sección, la crea 
			$this->obj->load->model('catalogo/m_articuloseccion');
			if (isset($data['nIdSeccion']) && (isset($data['nIdLibro'])))
			{				
				$sl = $this->obj->m_articuloseccion->get(0, 1, null, null, "nIdLibro = {$data['nIdLibro']} AND nIdSeccion = {$data['nIdSeccion']}");
				if (count($sl) == 0)
				{
					$ids = $this->obj->m_articuloseccion->insert(array('nIdSeccion' => $data['nIdSeccion'], 'nIdLibro' => $data['nIdLibro']));
					if ($ids < 0)
					{
						$this->_set_error_message($this->obj->m_articuloseccion->error_message());
						return FALSE;
					}
				}
				else
				{
					$ids = $sl[0]['nIdSeccionLibro'];
					$servir = $sl[0]['nStockServir'];
					$reservado = $sl[0]['nStockReservado'];
				}
			}
						
			# quita las anteriores cantidades
			if (isset($id))
			{				
				if (in_array($pre['nIdEstado'], array(ESTADO_LINEA_PEDIDO_CLIENTE_SERVIDA, ESTADO_LINEA_PEDIDO_CLIENTE_EN_PROCESO, ESTADO_LINEA_PEDIDO_CLIENTE_RECIBIDA, ESTADO_LINEA_PEDIDO_CLIENTE_CATALOGADA)))
				{
					$servir -= $pre['nCantidad'] - $pre['nCantidadServida'];
				}
				
				if (in_array($pre['nIdEstado'], array(ESTADO_LINEA_PEDIDO_CLIENTE_SERVIDA, ESTADO_LINEA_PEDIDO_CLIENTE_CATALOGADA)))
				{
					$reservado -= $pre['nCantidadServida'];
				}
				#var_dump($servir, $reservado); die();
			}
			if ($servir < 0)
			{
				$msg = print_r($data, TRUE) . "\n" . print_r($pre, TRUE) . "\n" . print_r($sl[0], TRUE) . "\n";
				
				$f = fopen(DIR_LOG_PATH. 'errores.txt', 'w+');
				fwrite($f, $msg);
				fclose($f);
				#$this->obj->load->library('Logger');
				#$this->obj->logger->log($msg, 'errores');				
			}

			# añade las nuevas
			if (in_array($data['nIdEstado'], array(ESTADO_LINEA_PEDIDO_CLIENTE_SERVIDA, ESTADO_LINEA_PEDIDO_CLIENTE_EN_PROCESO, ESTADO_LINEA_PEDIDO_CLIENTE_RECIBIDA, ESTADO_LINEA_PEDIDO_CLIENTE_CATALOGADA)))
			{
				$servir += $data['nCantidad'] - $data['nCantidadServida'];
			}
			
			if (in_array($data['nIdEstado'], array(ESTADO_LINEA_PEDIDO_CLIENTE_SERVIDA, ESTADO_LINEA_PEDIDO_CLIENTE_CATALOGADA)))
			{
				$reservado += $data['nCantidadServida'];
			}
						
			#var_dump($servir, $reservado);
			
			# Actualiza el stock
			if (!$this->obj->m_articuloseccion->update($ids, array('nStockServir' => $servir, 'nStockReservado' => $reservado)))
			{
				$this->_set_error_message($obj->m_articuloseccion->error_message());
				return FALSE;
			}
			#die();
		}
		return TRUE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeDelete($id)
	 * @todo Actualizar el estado del pedido cuando se modifican las líneas
	 */
	protected function onBeforeDelete($id)
	{
		$pre = $this->load($id);
		# Elimina el stock
		$this->obj->load->model('catalogo/m_articuloseccion');
		$sl = $this->obj->m_articuloseccion->get(0, 1, null, null, "nIdLibro = {$pre['nIdLibro']} AND nIdSeccion = {$pre['nIdSeccion']}");
		if (count($sl) > 0)
		{
			$ids = $sl[0]['nIdSeccionLibro'];
			$servir = $sl[0]['nStockServir'];
			$reservado = $sl[0]['nStockReservado'];
			if (in_array($pre['nIdEstado'], array(ESTADO_LINEA_PEDIDO_CLIENTE_SERVIDA, ESTADO_LINEA_PEDIDO_CLIENTE_EN_PROCESO, ESTADO_LINEA_PEDIDO_CLIENTE_RECIBIDA, ESTADO_LINEA_PEDIDO_CLIENTE_CATALOGADA)))
			{
				$servir -= $pre['nCantidad'];
			}
			
			if (in_array($pre['nIdEstado'], array(ESTADO_LINEA_PEDIDO_CLIENTE_SERVIDA)))
			{
				$reservado -= $pre['nCantidadServida'];
			}
			
			# Actualiza el stock
			if (!$this->obj->m_articuloseccion->update($ids, array('nStockServir' => $servir, 'nStockReservado' => $reservado)))
			{
				$this->_set_error_message($obj->m_articuloseccion->error_message());
				return FALSE;
			}
		}
		return parent::onBeforeDelete($id);
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeUpdate($data)
	 * @todo Actualizar el estado del pedido cuando se modifican las líneas
	 */
	protected function onBeforeUpdate($id, &$data)
	{
		if (parent::onBeforeUpdate($id, $data))
		{
			if (isset($data['nCantidadServida']) || isset($data['nCantidad']))
			{
				$reg = $this->load($id);
				$cantidad = isset($data['nCantidad'])?$data['nCantidad']:$reg['nCantidad'];
				$servidas = isset($data['nCantidadServida'])?$data['nCantidadServida']:$reg['nCantidadServida'];
				$servidas = min($servidas, $cantidad);
				#echo "Q: $cantidad  S: $servidas";
				if (($cantidad > $servidas) && ($servidas > 0))
				{
					// Crea una nueva línea con las que no se sirven
					$new = $reg;
					unset($new['nIdLinea']);
					unset($new['dCreacion']);
					unset($new['dAct']);
					unset($new['cCUser']);
					unset($new['cAUser']);
					unset($new['nIdEstado']);
					unset($new['nCantidadServida']);
					$new['nCantidad'] = $cantidad - $servidas;
					if ($this->insert($new) < 0)
					{
						return FALSE;
					}
					$data['nCantidad'] = $servidas;
					$data['nIdEstado'] = ESTADO_LINEA_PEDIDO_CLIENTE_SERVIDA;
					#echo '<pre>'; print_r($new); echo '</pre>'; die();
				}
				if (($servidas == 0) &&
					($reg['nIdEstado'] == ESTADO_LINEA_PEDIDO_CLIENTE_SERVIDA ||
					$reg['nIdEstado'] == ESTADO_LINEA_PEDIDO_CLIENTE_CATALOGADA))
				{
					$data['nIdEstado'] = ESTADO_LINEA_PEDIDO_CLIENTE_EN_PROCESO;
				}
				if (($servidas > 0 && $cantidad == $servidas) &&
					(!isset($data['nIdEstado'])||($data['nIdEstado'] == ESTADO_LINEA_PEDIDO_CLIENTE_EN_PROCESO)) &&
					($reg['nIdEstado'] == ESTADO_LINEA_PEDIDO_CLIENTE_EN_PROCESO ||
					$reg['nIdEstado'] == ESTADO_LINEA_PEDIDO_CLIENTE_NO_SE_PUEDE_SERVIR))
				{
					$data['nIdEstado'] = ESTADO_LINEA_PEDIDO_CLIENTE_SERVIDA;
				}
				$data['nCantidadServida'] = $servidas;
			}
			return $this->_pre_ins($data, $id);
		}
		return FALSE;
	}

	/**
	 * Trigger llamado Antes de insertar los datos
	 * @param array $data Registro a insertar
	 * @return bool, TRUE: correcto, FALSE: error, se cancela acción
	 * @todo Actualizar el estado del pedido cuando se modifican las líneas
	 */
	protected function onBeforeInsert(&$data)
	{
		static $cache = array();

		if (parent::onBeforeInsert($data))
		{
			if ((isset($data['nCantidad']) && isset($data['nCantidadServida'])))
			{
				$data['nCantidadServida'] = min($data['nCantidadServida'], $data['nCantidad']);
				if ($data['nCantidadServida'] == $data['nCantidad'])
				{
					$data['nIdEstado'] = ESTADO_LINEA_PEDIDO_CLIENTE_SERVIDA;
				}
				elseif (($data['nCantidadServida'] > 0))
				{
					// Crea una nueva línea con las que no se sirven
					$new = $data;
					unset($new['nIdLinea']);
					$new['nCantidad'] = $data['nCantidadServida'];
					if ($this->insert($new) < 0)
					{
						return FALSE;
					}
				
					$data['nCantidad'] = $data['nCantidad'] - $data['nCantidadServida'];
					$data['nCantidadServida'] = 0;
				}

			}
			if (!isset($cache[$data['nIdPedido']]))
			{
				$this->obj->load->model('ventas/m_pedidocliente');
				$cache[$data['nIdPedido']] = $this->obj->m_pedidocliente->load($data['nIdPedido']);
			}
			if ($cache[$data['nIdPedido']]['nIdEstado'] == ESTADO_PEDIDO_CLIENTE_PRESUPUESTO)
			{
				if (isset($data['nCantidadServida']) && $data['nCantidadServida'] > 0)
				{
					$data['nIdEstado'] = ESTADO_LINEA_PEDIDO_CLIENTE_ACEPTADO;
				}
				else
				{
					$data['nIdEstado'] = ESTADO_LINEA_PEDIDO_CLIENTE_PENDIENTE;
				}
			}
			return $this->_pre_ins($data);
		}

		return FALSE;
	}
}

/* End of file m_pedidoclientelinea.php */
/* Location: ./system/application/models/compras/m_pedidoclientelinea.php */
