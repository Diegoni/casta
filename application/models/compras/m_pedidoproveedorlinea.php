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

#define('LINEA_PEDIDO_PROVEEDOR_STATUS_CERRADO', 2);
define('LINEA_PEDIDO_PROVEEDOR_STATUS_CANCELADO', 6);
define('LINEA_PEDIDO_PROVEEDOR_STATUS_CANCELADO_Y_PARCIALMENTE_RECIBIDO', 7);
define('LINEA_PEDIDO_PROVEEDOR_STATUS_DESCATALOGADO', 5);
define('LINEA_PEDIDO_PROVEEDOR_STATUS_EN_PROCESO', 1);
define('LINEA_PEDIDO_PROVEEDOR_STATUS_PARCIALMENTE_RECIBIDO', 4);
define('LINEA_PEDIDO_PROVEEDOR_STATUS_PENDIENTE_DE_RECIBIR', 2);
define('LINEA_PEDIDO_PROVEEDOR_STATUS_RECIBIDO', 3);

define('DEFAULT_LINEA_PEDIDO_PROVEEDOR_STATUS', LINEA_PEDIDO_PROVEEDOR_STATUS_EN_PROCESO);

/**
 * Líneas de pedido proveedor
 *
 */
class M_pedidoproveedorlinea extends MY_Model
{
	/**
	 * Constructor
	 * @return M_pedidoproveedorlinea
	 */
	function __construct()
	{
		$obj = get_instance();
		$data_model = array(
			'nIdPedido'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'compras/devolucion/search')),
			'nIdSeccion'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'catalogo/seccion/search')),		
			'nIdLibro'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'catalogo/articulo/search')),		
			'nCantidad' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE), 
			'nRecibidas' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_READONLY => TRUE, DATA_MODEL_DEFAULT_VALUE => 0), 

			'fPrecio' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT, DATA_MODEL_DEFAULT_VALUE => 0), 
			'fIVA' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT, DATA_MODEL_DEFAULT_VALUE => 0), 
			'fRecargo' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT, DATA_MODEL_DEFAULT_VALUE => 0), 
			'fDescuento' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT, DATA_MODEL_DEFAULT_VALUE => 0),
			
			'nIdEstado'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_DEFAULT_VALUE => DEFAULT_LINEA_PEDIDO_PROVEEDOR_STATUS, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'compras/estadopedidoproveedorlinea/search')),		

			'cRefProveedor' => array(), 
			'cRefInterna'	=> array(),
		
			'nIdInformacion'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'compras/informacionproveedor/search')),		
            'dFechaInformacion' => array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATE),
		
			#'BaseImponible' 		=> array(DATA_MODEL_READONLY => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT), 
			#'PrecioUnitarioExento' 	=> array(DATA_MODEL_READONLY => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT), 
			#'PrecioUnitario' 		=> array(DATA_MODEL_READONLY => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT), 
			#'Total' 				=> array(DATA_MODEL_READONLY => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT), 
		);
		
		$this->_relations['pedidosuscripcion'] = array(
            'ref' => 'suscripciones/m_pedidosuscripcion',
            'fk' => 'nIdLineaPedido');

		parent::__construct('Doc_LineasPedidoProveedor', 'nIdLinea', 'nIdLinea', 'nIdLinea', $data_model, TRUE);
	}

	/**
	 * Cancelar la línea del pedido
	 * @param int $id Id del pedido
	 * @param string $linea Línea de datos
	 * @return book FALSE: error, TRUE: se ha cancelado correctamente
	 */
	function cancelar($id, &$linea = null)
	{
		$linea = $this->load($id);
		if (in_array($linea['nIdEstado'], array(LINEA_PEDIDO_PROVEEDOR_STATUS_PENDIENTE_DE_RECIBIR,
		LINEA_PEDIDO_PROVEEDOR_STATUS_PARCIALMENTE_RECIBIDO
		)))
		{
			$count = 0;
			// Cambia estado
			$status = ($linea['nRecibidas'] > 0)?LINEA_PEDIDO_PROVEEDOR_STATUS_CANCELADO_Y_PARCIALMENTE_RECIBIDO:LINEA_PEDIDO_PROVEEDOR_STATUS_CANCELADO;
			if (!$this->update($id, array('nIdEstado' => $status)))
			{
				return FALSE;
			}
			$this->obj->load->model('concursos/m_pedidoconcursolinea');
			$this->obj->load->model('concursos/m_estadolineaconcurso');
			$sl = $this->obj->m_pedidoconcursolinea->get(0, 1, null, null, "nIdLineaPedidoProveedor={$id}");
			foreach ($sl as $reg) 
			{
				if (($reg['nIdEstado'] == CONCURSOS_ESTADO_LINEA_A_PEDIR) || ($reg['nIdEstado'] == CONCURSOS_ESTADO_LINEA_PEDIDO_AL_PROVEEDOR))
				{
					if (!$this->obj->m_pedidoconcursolinea->update($reg['nIdLineaPedidoConcurso'], 
						array(
							'nIdLineaPedidoProveedor' => null,
							'nIdEstado' => CONCURSOS_ESTADO_LINEA_EN_PROCESO
							)))
					{
						$this->_set_error_message($this->obj->m_pedidoconcursolinea->error_message());
						return FALSE;
					}
				}
			}

			return TRUE;
		}

		$this->_set_error_message($this->lang->line('pedido-proveedor-no-posible-cancelar-linea'));
		return FALSE;
	}

	/**
	 * Cantidad recibida de la línea indicada
	 * @param int $id Id de la línea
	 * @retrun int
	 */
	function recibidas($id)
	{
		$this->db->flush_cache();
		$this->db->select('ISNULL(SUM(nCantidad), 0) nCantidad')
		->from('Doc_LineasPedidosRecibidas')
		->where("nIdLineaPedido = {$id}");
		$query = $this->db->get();
		$data = $this->_get_results($query);
		return (count($data)>0)?$data[0]['nCantidad']:0;

	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeSelect($id, $sort, $dir, $where)
	 */
	protected function onBeforeSelect($id = null, &$sort = null, &$dir = null, &$where = null)
	{
		if (parent::onBeforeSelect($id, $sort, $dir, $where))
		{
			$this->db->select('Cat_Fondo.cTitulo, Cat_Fondo.cAutores, Cat_Fondo.nEAN, Cat_Fondo.cISBN, Cat_Secciones.cNombre cSeccion, Cat_Editoriales.cNombre cEditorial')
			->select('Doc_EstadosLineaPedidoProveedor.cDescripcion cEstado')
			->select('Doc_InformacionProveedor.cDescripcion cInformacion')
			->join('Cat_Fondo', "Cat_Fondo.nIdLibro = {$this->_tablename}.nIdLibro")
			->join('Cat_Secciones', "Cat_Secciones.nIdSeccion = {$this->_tablename}.nIdSeccion")
			->join('Cat_Editoriales', 'Cat_Fondo.nIdEditorial = Cat_Editoriales.nIdEditorial', 'left')
			->join('Doc_EstadosLineaPedidoProveedor', "Doc_EstadosLineaPedidoProveedor.nIdEstado = {$this->_tablename}.nIdEstado", 'left')
			->join('Doc_InformacionProveedor', "Doc_InformacionProveedor.nIdInformacion = {$this->_tablename}.nIdInformacion", 'left');

			$this->db->select('Ext_Concursos.cDescripcion cConcurso, Ext_Concursos.nIdConcurso')
			->select('Ext_Bibliotecas.cDescripcion cBiblioteca')
			->select('Ext_LineasPedidoConcurso.nIdLineaPedidoConcurso')
			->join('Ext_LineasPedidoConcurso', 'Ext_LineasPedidoConcurso.nIdLineaPedidoProveedor=Doc_LineasPedidoProveedor.nIdLinea', 'left')
			->join('Ext_Bibliotecas', "Ext_Bibliotecas.nIdBiblioteca = Ext_LineasPedidoConcurso.nIdBiblioteca", 'left')
			->join('Ext_Concursos', "Ext_Concursos.nIdConcurso = Ext_Bibliotecas.nIdConcurso", 'left');
			
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
			$importes = format_calculate_importes($data);
			#echo '<pre>'; print_r($data); echo '</pre>';
			#if (isset($data['nCantidad']) && isset($data['nRecibidas']))
			{
				$data['nPendientes'] = $data['nCantidad'] - $data['nRecibidas'];
			}
			$data = array_merge($data, $importes);
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
	protected function _pre_ins(&$data, $id = null, &$pd = null)
	{
		# Comprueba que el pedido no sea de una sección exclusiva
		if (isset($data['nIdPedido']) && isset($data['nIdSeccion']))
		{
			if (!isset($pd))
			{
				$this->obj->load->model('compras/m_pedidoproveedor');
				$pd = $this->obj->m_pedidoproveedor->load($data['nIdPedido']);
			}
			if (isset($pd['nIdSeccion']) && ($pd['nIdSeccion'] != $data['nIdSeccion']))
			{
				$this->obj->load->model('generico/m_seccion');
				$sc = $this->obj->m_seccion->load($pd['nIdSeccion']);
				$this->_set_error_message(sprintf($this->lang->line('pedido-proveedor-seccion-error'), $data['nIdPedido'], $sc['cNombre']));
				return FALSE;
			}
		}

		if (isset($data['nIdEstado']) || isset($data['nCantidad'])  || isset($data['nRecibidas']))
		{
			if (isset($data['nCantidad']) && $data['nCantidad'] < 0)
			{
				$this->_set_error_message($this->lang->line('cantidad-mayor-0'));
				return FALSE;
			}

			$apedir = 0;
			$recibir = 0 ;
			# Cantidades y sección
			if (isset($id))
			{				
				$pre = $this->load($id);
				#var_dump($pre);
				$data['nIdSeccion'] = $pre['nIdSeccion'];
				$data['nIdLibro'] = $pre['nIdLibro'];
				if (!isset($data['nIdEstado'])) $data['nIdEstado'] = $pre['nIdEstado'];
				if (!isset($data['nCantidad'])) $data['nCantidad'] = $pre['nCantidad'];
				if (!isset($data['nRecibidas'])) $data['nRecibidas'] = $pre['nRecibidas'];
			}
			if (!isset($data['nCantidad'])) $data['nCantidad'] = 0;
			if (!isset($data['nRecibidas'])) $data['nRecibidas'] = 0;

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
					$apedir = $sl[0]['nStockAPedir'];
					$recibir = $sl[0]['nStockRecibir'];
				}
			}
			# quita las anteriores cantidades
			if (isset($id))
			{				
				if (in_array($pre['nIdEstado'], array(LINEA_PEDIDO_PROVEEDOR_STATUS_EN_PROCESO)))
				{
					$apedir -= $pre['nCantidad'];
				}
				
				if (in_array($pre['nIdEstado'], array(LINEA_PEDIDO_PROVEEDOR_STATUS_PARCIALMENTE_RECIBIDO, LINEA_PEDIDO_PROVEEDOR_STATUS_PENDIENTE_DE_RECIBIR)))
				{
					$recibir -= ($pre['nCantidad'] - $pre['nRecibidas']);
				}
			}

			# añade las nuevas
			if (in_array($data['nIdEstado'], array(LINEA_PEDIDO_PROVEEDOR_STATUS_EN_PROCESO)))
			{
				$apedir += $data['nCantidad'];
			}
			
			if (in_array($data['nIdEstado'], array(LINEA_PEDIDO_PROVEEDOR_STATUS_PARCIALMENTE_RECIBIDO, LINEA_PEDIDO_PROVEEDOR_STATUS_PENDIENTE_DE_RECIBIR)))
			{
				$recibir += ($data['nCantidad'] - $data['nRecibidas']);
			}
						
			# Actualiza el stock
			if (!$this->obj->m_articuloseccion->update($ids, array('nStockAPedir' => $apedir, 'nStockRecibir' => $recibir)))
			{
				$this->_set_error_message($obj->m_articuloseccion->error_message());
				return FALSE;
			}
			
			# Actualiza el estado
			if ((isset($data['nIdEstado']) && 
				!in_array($data['nIdEstado'], 
					array(LINEA_PEDIDO_PROVEEDOR_STATUS_CANCELADO, 
						LINEA_PEDIDO_PROVEEDOR_STATUS_CANCELADO_Y_PARCIALMENTE_RECIBIDO)))
				 && $data['nRecibidas'] > 0)
			{
				$data['nIdEstado'] = ($data['nRecibidas'] == $data['nCantidad']) ? LINEA_PEDIDO_PROVEEDOR_STATUS_RECIBIDO : LINEA_PEDIDO_PROVEEDOR_STATUS_PARCIALMENTE_RECIBIDO;
			}
			if (isset($data['nIdEstado'])
				&& ($data['nIdEstado'] == LINEA_PEDIDO_PROVEEDOR_STATUS_CANCELADO)
				 && $data['nRecibidas'] > 0)
			{
				$data['nIdEstado'] = LINEA_PEDIDO_PROVEEDOR_STATUS_CANCELADO_Y_PARCIALMENTE_RECIBIDO;
			}
		}
		return TRUE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onAfterInsert($data)
	 */
	protected function onBeforeInsert(&$data)
	{
		if (parent::onBeforeInsert($data))
		{
			#Comprueba que el pedido esté EN PROCESO
			$this->obj->load->model('compras/m_pedidoproveedor');
			$pd = $this->obj->m_pedidoproveedor->load($data['nIdPedido']);
			if ($pd['nIdEstado'] != PEDIDO_PROVEEDOR_STATUS_EN_CREACION)
			{
				$this->_set_error_message(sprintf($this->lang->line('pedido-proveedor-cerrado-error'), $data['nIdPedido']));
				return FALSE;
			}

			if (!$this->_pre_ins($data, null, $pd)) return FALSE;

			return TRUE;
		}
		return FALSE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onAfterInsert($data)
	 */
	protected function onBeforeUpdate($id, &$data)
	{
		#var_dump($data); die();
		if (parent::onBeforeUpdate($id, $data))
		{
			if (!isset($data['nIdEstado']))
			{
				$pre = $this->load($id);
				$estado = $pre['nIdEstado'];
			}
			else
			{
				$estado = $data['nIdEstado'];
			}
		
			if (($estado != LINEA_PEDIDO_PROVEEDOR_STATUS_EN_PROCESO) &&
				(isset($data['nCantidad']) || isset($data['nIdLibro']) || isset($data['nIdSeccion'])))
			{
				$this->_set_error_message($this->lang->line('pedidoproveedor-update-error-state'));
				return FALSE;
			}

			if (!$this->_pre_ins($data, $id)) return FALSE;

			return TRUE;
		}
		return FALSE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeDelete($id)
	 * @todo Actualizar el estado del pedido cuando se modifican las líneas
	 */
	protected function onBeforeDelete($id)
	{
		// Si la línea no está en proceso, no se puede borrar
		$pre = $this->load($id);
		if ($pre['nIdEstado'] != LINEA_PEDIDO_PROVEEDOR_STATUS_EN_PROCESO)
		{
			$this->_set_error_message($this->lang->line('pedidoproveedor-delete-error-state'));
			return FALSE;
		}

		# Elimina el stock
		$this->obj->load->model('catalogo/m_articuloseccion');
		$sl = $this->obj->m_articuloseccion->get(0, 1, null, null, "nIdLibro = {$pre['nIdLibro']} AND nIdSeccion = {$pre['nIdSeccion']}");
		#var_dump($sl); die();
		if (count($sl) > 0)
		{
			$ids = $sl[0]['nIdSeccionLibro'];
			$apedir = $sl[0]['nStockAPedir'];
			$recibir = $sl[0]['nStockRecibir'];
			if (in_array($pre['nIdEstado'], array(LINEA_PEDIDO_PROVEEDOR_STATUS_EN_PROCESO)))
			{
				$apedir -= $pre['nCantidad'];
			}
			
			if (in_array($pre['nIdEstado'], array(LINEA_PEDIDO_PROVEEDOR_STATUS_PARCIALMENTE_RECIBIDO, LINEA_PEDIDO_PROVEEDOR_STATUS_PENDIENTE_DE_RECIBIR)))
			{
				$recibir -= ($pre['nCantidad'] + $pre['nRecibidas']);
			}
			#var_dump($apedir, $recibir); die();
			# Actualiza el stock
			if (!$this->obj->m_articuloseccion->update($ids, array('nStockAPedir' => $apedir, 'nStockRecibir' => $recibir)))
			{
				$this->_set_error_message($obj->m_articuloseccion->error_message());
				return FALSE;
			}
		}
		# Elimina las referencias del concurso
		$this->obj->load->model('concursos/m_pedidoconcursolinea');
		$this->obj->load->model('concursos/m_estadolineaconcurso');
		$sl = $this->obj->m_pedidoconcursolinea->get(0, 1, null, null, "nIdLineaPedidoProveedor = {$id}");
		foreach ($sl as $reg) 
		{
			if ($reg['nIdEstado'] != CONCURSOS_ESTADO_LINEA_A_PEDIR)
			{
				$this->_set_error_message(sprintf($this->lang->line('pedido-proveedor-linea-error-concurso'), $reg['cConcurso']));
				return FALSE;
			}
			if (!$this->obj->m_pedidoconcursolinea->update($reg['nIdLineaPedidoConcurso'], 
				array(
					'nIdLineaPedidoProveedor' => NULL,
					'nIdEstado' => CONCURSOS_ESTADO_LINEA_EN_PROCESO
					)))
			{
				$this->_set_error_message($obj->m_pedidoconcursolinea->error_message());
				return FALSE;
			}
		}
		return parent::onBeforeDelete($id);
	}

}

/* End of file M_pedidoproveedorLinea.php */
/* Location: ./system/application/models/compras/M_pedidoproveedorLinea.php */