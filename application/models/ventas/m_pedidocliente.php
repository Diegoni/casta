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
 * Estados de la línea del pedido
 */
define('ESTADO_PEDIDO_CLIENTE_EN_PROCESO', 		1);
define('ESTADO_PEDIDO_CLIENTE_CERRADO', 		2);
define('ESTADO_PEDIDO_CLIENTE_PRESUPUESTO', 	3);
define('ESTADO_PEDIDO_CLIENTE_PENDIENTE_FINALIZAR', 6);
define('ESTADO_PEDIDO_CLIENTE_ENVIADO', 7);

/**
 * Estado líneas de pedido cliente por defecto
 * @var int
 */
define('DEFAULT_PEDIDO_CLIENTE_STATUS', ESTADO_PEDIDO_CLIENTE_EN_PROCESO);

/**
 * Pedido de cliente
 *
 */
class M_pedidocliente extends MY_Model
{
	/**
	 * Constructor
	 * @return M_albaransalida
	 */
	function __construct()
	{
		$data_model = array(
			'nIdCliente'	=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'clientes/cliente/search', 'cCliente')),
			'cRefCliente' 	=> array(), 
			'cRefInterna'	=> array(),

			'nIdDirEnv'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'clientes/direccion/search')),
			'nIdDirFac'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'clientes/direccion/search')),

			'fAnticipo'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT), 
			'nIdFactura'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'compras/factura/search')),
			'nIdAlbaranDescuentaAnticipo'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),

			'nIdEstado'		=> array(DATA_MODEL_DEFAULT_VALUE => DEFAULT_PEDIDO_CLIENTE_STATUS, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'ventas/estadopedidocliente/search')),
			'nIdTipoOrigen'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'ventas/tipoorigen/search')),		
			'dEnvio'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATE),
			'bCatalogar' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN, DATA_MODEL_DEFAULT_VALUE => FALSE),
			'bLock' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN, DATA_MODEL_DEFAULT_VALUE => FALSE),
			'bMostrarWeb' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN, DATA_MODEL_DEFAULT_VALUE => FALSE),
			'bPagado' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN, DATA_MODEL_DEFAULT_VALUE => FALSE),

			'bExentoIVA' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN, DATA_MODEL_DEFAULT_VALUE => FALSE),

			'tNotasExternas'	=> array(),
			'tNotasInternas'	=> array(),
			'cIdShipping'	=> array(),
			'nIdModoPago'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'ventas/modopago/search')),		
			'nIdModoEnvio'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'ventas/modoenvio/search')),		
			'nIdWeb'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),		
			'bMantenerPrecio' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),
			'bNoAvisar' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),
			'nIdBiblioteca'		=> array( DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'concursos/biblioteca/search')),
			'nIdSala'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'concursos/sala/search')),
		 );

		parent::__construct('Doc_PedidosCliente', 'nIdPedido', 'nIdPedido', array('cRefCliente', 'cRefInterna'), $data_model, TRUE);

		$this->_alias = array(
			'cCliente'	=> array('Cli_Clientes.cNombre, Cli_Clientes.cApellido, Cli_Clientes.cEmpresa', DATA_MODEL_TYPE_STRING),
		);

		$this->_relations['lineas'] = array (
			'ref'	=> 'ventas/m_pedidoclientelinea',
			'type'	=> DATA_MODEL_RELATION_1N,
			'cascade'	=> TRUE,
			'fk'	=> 'nIdPedido');

		$this->_relations['cliente'] = array (
			'ref'	=> 'clientes/m_cliente',
			'fk'	=> 'nIdCliente');

		$this->_relations['direccion'] = array (
			'ref'	=> 'clientes/m_direccioncliente',
			'fk'	=> 'nIdDirEnv');				

		$this->_relations['direccionfactura'] = array (
			'ref'	=> 'clientes/m_direccioncliente',
			'fk'	=> 'nIdDirFac');				

	}

	/**
	 * Cancelar el pedido del cliente
	 * @param int $id Id del pedido
	 * @return mixed FALSE: error, int número de registros
	 */
	function cancelar($id)
	{
		$obj = get_instance();
		// Comprueba que esté todo correcto
		$pedido = $this->reg->load($id, 'lineas');
		$obj->load->model('ventas/m_pedidoclientelinea');
		$count = 0;
		$this->db->trans_begin();
		foreach($pedido['lineas'] as $linea)
		{
			if (!in_array($linea['nIdEstado'], array(ESTADO_LINEA_PEDIDO_CLIENTE_EN_ALBARAN,
			ESTADO_LINEA_PEDIDO_CLIENTE_CANCELADA,
			ESTADO_LINEA_PEDIDO_CLIENTE_CANCELADA_Y_CATALOGADA
			)))
			{
				if (!$obj->m_pedidoclientelinea->update($linea['nIdLinea'], array('nIdEstado' => ESTADO_LINEA_PEDIDO_CLIENTE_CANCELADA)))
				{
					$this->_set_error_message($obj->m_pedidoclientelinea->error_message());
					$this->db->trans_rollback();
					return FALSE;
				}					
				++$count;
			}
		}
		$this->db->trans_commit();
		return $count;
	}

	/**
	 * Cancelar el pedido del cliente
	 * @param int $id Id del pedido
	 * @return mixed FALSE: error, int número de registros
	 */
	function enviado($id)
	{
		$obj = get_instance();
		// Comprueba que esté todo correcto
		$pedido = $this->reg->load($id, 'lineas');
		$obj->load->model('ventas/m_pedidoclientelinea');
		$count = $count2 = 0;
		$this->db->trans_begin();
		$art = array();
		foreach($pedido['lineas'] as $linea)
		{
			if (in_array($linea['nIdEstado'], array(ESTADO_LINEA_PEDIDO_CLIENTE_EN_ALBARAN)))
			{
				if (!$obj->m_pedidoclientelinea->update($linea['nIdLinea'], array('nIdEstado' => ESTADO_LINEA_PEDIDO_CLIENTE_ENVIADO)))
				{
					$this->_set_error_message($obj->m_pedidoclientelinea->error_message());
					$this->db->trans_rollback();
					return FALSE;
				}		
				$arr[] = $linea;			
				++$count;
				++$count2;
			}
			if (in_array($linea['nIdEstado'], array(ESTADO_LINEA_PEDIDO_CLIENTE_ENVIADO, ESTADO_LINEA_PEDIDO_CLIENTE_CANCELADA)))
				++$count2;
		}
		if ($count2 == count($pedido['lineas']))
		{
			if (!$this->update($id, array('nIdEstado' => ESTADO_PEDIDO_CLIENTE_ENVIADO)))
			{
				$this->db->trans_rollback();
				return FALSE;
			}			
		}
		$this->db->trans_commit();
		return array('count' => $count2, 'lineas' => $arr);
	}

	/**
	 * Devuelve el listado de albaranes que sirven un pedido
	 * @param int $id Id del pedido
	 * @return mixed FALSE: error, int número de registros
	 */
	function albaranes($id)
	{
		$this->db->flush_cache();			
		$this->db->select('Doc_LineasAlbaranesSalida.nIdAlbaran nIdAlbaran, Doc_LineasAlbaranesSalida.nCantidad')
		->select('Doc_LineasPedidoCliente.nIdLibro, Cat_Fondo.cTitulo, Cat_Fondo.cAutores, Cat_Fondo.cISBN, Cat_Secciones.cNombre cSeccion, Cat_Editoriales.cNombre cEditorial')
		->select('Cat_Secciones.cNombre cSeccion')
		->from('Doc_LineasPedidoCliente')
		->join('Cat_Fondo', "Cat_Fondo.nIdLibro = Doc_LineasPedidoCliente.nIdLibro")
		->join('Doc_LineasAlbaranesSalida', 'Doc_LineasAlbaranesSalida.nIdLineaPedido=Doc_LineasPedidoCliente.nIdLinea')
		->join('Cat_Secciones', "Cat_Secciones.nIdSeccion = Doc_LineasPedidoCliente.nIdSeccion")
		->join('Cat_Editoriales', 'Cat_Fondo.nIdEditorial = Cat_Editoriales.nIdEditorial', 'left')
		->where("Doc_LineasPedidoCliente.nIdPedido={$id}");
		$query = $this->db->get();
		$data = $this->_get_results($query);
		
		return $data;
	}

	/**
	 * Abrir el pedido del cliente
	 * @param int $id Id del pedido
	 * @return mixed FALSE: error, int número de registros
	 */
	function abrir($id)
	{
		// Comprueba que esté todo correcto
		$pedido = $this->reg->load($id);
		if ($pedido['nIdEstado'] != ESTADO_PEDIDO_CLIENTE_CERRADO)
		{
			$this->_set_error_message($this->lang->line('pedidocliente-nocerrado'));
			return FALSE;				
		}
		if (!$this->update($id, array('nIdEstado' => DEFAULT_PEDIDO_CLIENTE_STATUS)))
		{
			$this->db->trans_rollback();
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * Notas de un pedido
	 * @param $id
	 */
	function notas($id)
	{
		$this->db->select('')
		->from('Gen_Observaciones')
		->where('nIdTabla = 7')
		->where("nIdRegistro={$id}");
		$query = $this->db->get();
		return $this->_get_results($query);
	}
	
	/**
	 * Avisos para los clientes
	 * @param int $id Id del artículo
	 */
	function get_avisos($id)
	{
		// Pedidos
		$this->db->flush_cache();
		$this->db->select('lp.nIdSeccion nIdSeccion')
		->select('s.cNombre cSeccion')
		->select('lp.nIdLinea id')
		->select('f.cTitulo')
		->select('lp.fPrecio fPrecio')
		->select('lp.nCantidad nCantidad')
		->select('c.nIdCliente')
		->select('c.cNombre, c.cApellido, c.cEmpresa')
		->select('lp.cCUser, lp.nIdPedido, lp.nIdLinea, lp.nCantidadServida')
		->select('st.cDescripcion cEstado')
		->select($this->_date_field('lp.dCreacion',	'dFecha'))
		->select('lp.fDescuento	fDescuento,lp.nIdEstado')
		->select('Doc_InformacionCliente.cDescripcion cInformacion, Doc_InformacionCliente.nIdTipo nIdTipoInformacion')
		->from('Doc_LineasPedidoCliente lp')
		->join('Doc_PedidosCliente p', 'lp.nIdPedido = p.nIdPedido')
		->join('Cat_Fondo f', 'lp.nIdLibro = f.nIdLibro')
		->join('Cat_Secciones s', 'lp.nIdSeccion = s.nIdSeccion')
		->join('Cli_Clientes c', 'c.nIdCliente = p.nIdCliente')
		->join('Doc_EstadosLineaPedidoCliente st', 'lp.nIdEstado = st.nIdEstado')
		->join('Doc_InformacionCliente', "Doc_InformacionCliente.nIdInformacion =lp.nIdInformacion", 'left')
		->where('lp.nIdLinea IN (' . $id . ')');
		
		$query = $this->db->get();
		$data = $this->_get_results($query);
		return $data; 
	}
	
	/**
	 * Actualiza el estado de los pedido de cliente
	 * @param int $last Fecha de la última modificación de pedido a comprobar
	 * @param int $id Id de pedido a actualizar
	 * 	 * @return bool
	 */
	function actualizar_estado($last = null, $id = null)
	{
		set_time_limit(0);
		
		$sql = 'UPDATE Doc_PedidosCliente
			SET nIdEstado = 2
			WHERE nIdEstado IN (1, 7) AND nIdPedido NOT IN (
				SELECT nIdPedido
				FROM Doc_LineasPedidoCliente
				WHERE nIdEstado IN (1, 2, 3, 6)
				GROUP BY nIdPedido
			)';
	
		$this->db->query($sql);

		$count = $this->db->affected_rows();

		# Si estaba enviado y hay mas a EN PROCESO se cambia
		$sql = 'UPDATE Doc_PedidosCliente
			SET nIdEstado = 2
			WHERE nIdEstado = 7 AND nIdPedido IN (
				SELECT nIdPedido
				FROM Doc_LineasPedidoCliente
				WHERE nIdEstado IN (1, 2, 3, 6)
				GROUP BY nIdPedido
			)';
	
		$this->db->query($sql);

		return array('last' => time(), 'count' => $count + $this->db->affected_rows(), 'act' => $count + $this->db->affected_rows());		
	}

	/**
	 * Añade el IVA al pedido
	 * @param int $id Id del pedido
	 * @return MSG
	 */
	function add_iva($id = null)
	{
		$this->db->flush_cache();
		$this->db->select('lp.nIdLinea')
		->select('t.fIVA')
		->from('Doc_LineasPedidoCliente lp')
		->join('Cat_Fondo f', 'lp.nIdLibro = f.nIdLibro')
		->join('Cat_Tipos t', 't.nIdTipo = f.nIdTipo')
		->where('lp.nIdPedido = ' . $id);
		
		$query = $this->db->get();
		$data = $this->_get_results($query);
		$this->obj->load->model('ventas/m_pedidoclientelinea');
		$this->db->trans_begin();
		foreach ($data as $value) 
		{
			if (!$this->obj->m_pedidoclientelinea->update($value['nIdLinea'], array('fIVA' => $value['fIVA'])))
			{
				$this->db->trans_rollback();
				$this->_set_error_message($this->obj->m_pedidoclientelinea->error_message());
				return FALSE;
			}
		}
		$this->db->trans_commit();
		return TRUE; 
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeDelete($id)
	 */
	protected function onBeforeDelete($id)
	{
		// Si el albarán no está en proceso, no se puede borrar
		$albaran = $this->load($id);
		if ($albaran['nIdEstado'] != DEFAULT_PEDIDO_CLIENTE_STATUS)
		{
			$this->_set_error_message(sprintf($this->lang->line('error-pedidocliente-cerrado'), $id));
			return FALSE;
		}
		return parent::onBeforeDelete($id);
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeSelect($id, $sort, $dir, $where)
	 */
	protected function onBeforeSelect($id = null, &$sort = null, &$dir = null, &$where = null)
	{
		if (parent::onBeforeSelect($id, $sort, $dir, $where))
		{
			$this->db->select('Cli_Clientes.cNombre, Cli_Clientes.cApellido, Cli_Clientes.cEmpresa');
			$this->db->join('Cli_Clientes', "Cli_Clientes.nIdCliente = {$this->_tablename}.nIdCliente", 'left');
			$this->db->select('Doc_Facturas.nIdEstado nIdEstadoFactura');
			$this->db->select('Doc_AlbaranesSalida.nIdEstado nIdEstadoAlbaran');
			$this->db->join('Doc_Facturas', 'Doc_Facturas.nIdFactura = Doc_PedidosCliente.nIdFactura', 'left');
			$this->db->join('Doc_AlbaranesSalida', 'Doc_AlbaranesSalida.nIdAlbaran= Doc_PedidosCliente.nIdAlbaranDescuentaAnticipo', 'left');
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
			$data['cCliente'] = format_name($data['cNombre'], $data['cApellido'], $data['cEmpresa']);
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeUpdate($data)
	 */
	protected function onBeforeUpdate($id, &$data)
	{
		if (parent::onBeforeUpdate($id, $data))
		{
			if (isset($data['nIdCliente']))
			{
				# Comprueba que el cliente no esté bloqueado
				$this->obj->load->model('clientes/m_cliente');
				$cl = $this->obj->m_cliente->load($data['nIdCliente']);
				if (isset($cl['nIdEstado']) && $cl['nIdEstado'] != STATUS_CLIENTE_ACTIVADO)
				{
					$this->_set_error_message($this->lang->line('cliente-bloqueado'));
					return FALSE;
				} 			
				if (!isset($data['nIdDirEnv']))
				{
					$data['nIdDirEnv'] = $this->obj->m_cliente->get_direccion($data['nIdCliente'], 'PERFIL_ENVIO' );
				}
				if (!isset($data['nIdDirFac']))
				{
					$data['nIdDirFac'] = $this->obj->m_cliente->get_direccion($data['nIdCliente'], 'PERFIL_FACTURACION' );					
				} 
			}
		}
		return TRUE;
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
			# Comprueba que el cliente no esté bloqueado
			if (isset($data['nIdCliente']))
			{
				$this->obj->load->model('clientes/m_cliente');
				$cl = $this->obj->m_cliente->load($data['nIdCliente']);
				if (isset($cl['nIdEstado']) && $cl['nIdEstado'] != STATUS_CLIENTE_ACTIVADO)
				{
					$this->_set_error_message($this->lang->line('cliente-bloqueado'));
					return FALSE;
				}
			}
			return TRUE;
		}

		return TRUE;
	}
}

/* End of file M_pedidocliente.php */
/* Location: ./system/application/models/ventas/M_pedidocliente.php */
