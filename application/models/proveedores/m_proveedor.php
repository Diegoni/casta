<?php
/**
 * Casta
 *
 * Gestión de librerías
 *
 * @package		1.1
 * @subpackage	Models
 * @category	clientes
 * @author		Diego Nieto
 * @copyright	Copyright (c) 2015
 * @link		https://github.com/Diegoni/casta
 * @since		Version 1.1
 * @version		$Rev:  $
 * @filesource
 */

/**
 * Proveedores
 *
 */
class M_Proveedor extends MY_Model
{
	/**
	 * Costructor
	 * @return M_proveedor
	 */
	function __construct()
	{
		$data_model = array(
			'cNombre' 			=> array(), 
			'cApellido'			=> array(),
			'cEmpresa'			=> array(DATA_MODEL_DEFAULT => TRUE),
			'cNIF' 				=> array(),
			'nIdCuenta' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT), 
			'fDescuento' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DOUBLE),		
			'tComentario' 		=> array(),
			'cRandom' 			=> array(),
			'cIdioma'			=> array(),
			'cSINLI'			=> array(),
			'cSINLIBuzon'		=> array(),
			'bEnviarSINLI'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),
			'bEnviarSINLIDep'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),
			'bRecargo'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),
			'bDisabled'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),		
			'fCompraMinima'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DOUBLE),
		);

		parent::__construct(
					'Prv_Proveedores', 
					'nIdProveedor', 
					'cEmpresa, cNombre, cApellido', 
					array('cNombre', 'cApellido', 'cEmpresa'), 
					$data_model
				);
	}
	
	/**
	 * Unificador de proveedores
	 * @param int $id1 Id del proveedor destino
	 * @param int $id2 Id del proveedor repetida
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

		$this->load->helper('unificar');

		$tablas[] = array('tabla' => 'Doc_AlbaranesEntrada', 'model' => 'compras/m_albaranentrada');
		$tablas[] = array('tabla' => 'Doc_PedidosProveedor', 'model' => 'compras/m_pedidoproveedor');
		$tablas[] = array('tabla' => 'Prv_Direcciones', 'model' => 'proveedores/m_direccion');
		$tablas[] = array('tabla' => 'Prv_EMails', 'model' => 'proveedores/m_email');
		$tablas[] = array('tabla' => 'Prv_Profiles');
		$tablas[] = array('tabla' => 'Prv_Telefonos', 'model' => 'proveedores/m_telefono');
		$tablas[] = array('tabla' => 'Prv_Contactos', 'model' => 'proveedores/m_contacto');
		$tablas[] = array('tabla' => 'Cat_Editoriales', 'model' => 'catalogo/m_editorial');
		$tablas[] = array('tabla' => 'Cat_Fondo', 'model' => 'catalogo/m_articulo');
		$tablas[] = array('tabla' => 'Cat_Fondo', 'model' => 'catalogo/m_articulo', 'id' => 'nIdProveedorManual');
		$tablas[] = array('tabla' => 'Doc_Devoluciones', 'model' => 'compras/m_devolucion');
		$tablas[] = array('tabla' => 'Doc_CancelacionesPedidoProveedor', 'model' => 'compras/m_cancelacion');
		$tablas[] = array('tabla' => 'Doc_ReclamacionesPedidoProveedor', 'model' => 'compras/m_reclamacion');
		$tablas[] = array('tabla' => 'Doc_Devoluciones', 'model' => 'compras/m_devolucion');
		$tablas[] = array('tabla' => 'Doc_LiquidacionDepositos', 'model' => 'compras/m_liquidaciondepositos');
		$tablas[] = array('tabla' => 'Doc_FacturasProveedor');
		$tablas[] = array('tabla' => 'Prv_Proveedores_Cat_Fondo', 'model' => 'compras/m_proveedorarticulo');
		$tablas[] = array('tabla' => 'Prv_Proveedores_Editoriales_Tipos', 'model' => 'compras/m_proveedoreditorial');
		$tablas[] = array('tabla' => 'Ven_Comisiones');
		$tablas[] = array('tabla' => 'Gen_Observaciones', 'id' => 'nIdRegistro', 'where' => 'cTabla=\'Prv_Proveedores\'', 'model' => 'generico/m_nota');
		$tablas[] = array('tabla' => 'Prv_Proveedores_Fondo_Compras');

		// TRANS
		$this->db->trans_begin();

		foreach ($id_or as $id)
		{
			if (!unificar_nn($this, 'Prv_Proveedores_Cat_Fondo', 'nIdProveedor', 'nIdLibro', $id1, $id))
			{
				$this->db->trans_rollback();
				return FALSE;
			}
			if (!unificar_nn($this, 'Prv_Proveedores_Editoriales_Tipos', 'nIdProveedor', 
				$this->db->concat(array($this->db->varchar('nIdEditorial'), "'_'", $this->db->varchar('nIdTipo'))), 
				$id1, $id))
			{
				$this->db->trans_rollback();
				return FALSE;
			}
			if (!unificar_nn($this, 'Ven_Comisiones', 'nIdProveedor', 'nIdVendedor', $id1, $id))
			{
				$this->db->trans_rollback();
				return FALSE;
			}
			if (!unificar_nn($this, 'Prv_Proveedores_Cat_Fondo', 'nIdProveedor', 'nIdProveedor', $id1, $id))
			{
				$this->db->trans_rollback();
				return FALSE;
			}
			// Tablas
			if (!unificar_do($this, $tablas, $id1, $id, 'nIdProveedor'))
			{
				$this->db->trans_rollback();
				return FALSE;
			}
		}

		// Borrado
		$this->db->flush_cache();
		$this->db->where("nIdProveedor IN ({$id2})")
		->delete('Prv_Proveedores');
		if ($this->_check_error())
		{
			$this->db->trans_rollback();
			return FALSE;
		}

		// Limpieza de caches
		unificar_clear_cache($tablas);
		$this->clear_cache();

		// COMMIT
		$this->db->trans_commit();
		return TRUE;
	}

	/**
	 * Devuelve el perfil del tipo indciado del proveedor  del tipo de perfil indicado.
	 * Si no existe el tipo indicado se devuelve uno general.
	 * Si no existe el general se devuelve el primero que encuentra.
	 * @param int $id ID del proveedor
	 * @param string $model Modelo de datos
	 * @param int $profile Tipo de perfil
	 */
	private function get_profile($id, $model, $profile = null)
	{
		return $this->utils->get_profile_model($id, 'nIdProveedor', "proveedores/{$model}", $profile);
	}

	/**
	 * Devuelve el email del proveedor del tipo indicado.
	 * Si no existe el tipo indicado se devuelve uno general.
	 * Si no existe el general se devuelve el primero que encuentra.
	 * @param int $id ID del proveedor
	 * @param int $profile Tipo de perfil
	 */
	function get_email($id, $profile = null)
	{
		return $this->get_profile($id, 'm_email', $profile);
	}

	/**
	 * Devuelve la dirección del proveedor del tipo indicado.
	 * Si no existe el tipo indicado se devuelve uno general.
	 * Si no existe el general se devuelve el primero que encuentra.
	 * @param int $id ID del proveedor
	 * @param int $profile Tipo de perfil
	 */
	function get_direccion($id, $profile = null)
	{
		return $this->get_profile($id, 'm_direccion', $profile);
	}

	/**
	 * Análisis de los mivimientos de un proveedor
	 * @param int $id Id del proveedor
	 * @param datatime $desde Fecha inicial
	 * @param datatime $hasta Fecha final
	 * @return HTML_FILE
	 */
	function analisis($id = null, $desde = null, $hasta = null) 
	{
		# Compras
		$this->db->flush_cache();
		$this->db->select('MONTH(Doc_AlbaranesEntrada.dCierre) mes, YEAR(Doc_AlbaranesEntrada.dCierre) y')
		->select_sum($this->db->numeric('(Doc_LineasPedidosRecibidas.nCantidad * Doc_LineasAlbaranesEntrada.fPrecio) * (1 - Doc_LineasAlbaranesEntrada.fDescuento / 100.0)'),
			'importe')
		->from("Doc_LineasPedidosRecibidas")
		->join('Doc_LineasAlbaranesEntrada', 'Doc_LineasAlbaranesEntrada.nIdLinea = Doc_LineasPedidosRecibidas.nIdLineaAlbaran')
		->join('Doc_AlbaranesEntrada', 'Doc_LineasAlbaranesEntrada.nIdAlbaran = Doc_AlbaranesEntrada.nIdAlbaran')
		->join('Doc_LineasPedidoProveedor', 'Doc_LineasPedidoProveedor.nIdLinea = Doc_LineasPedidosRecibidas.nIdLineaPedido')
		->where('Doc_AlbaranesEntrada.nIdEstado IN (2, 3, 4)')
		->order_by('YEAR(Doc_AlbaranesEntrada.dCierre), MONTH(Doc_AlbaranesEntrada.dCierre)')
		->group_by('MONTH(Doc_AlbaranesEntrada.dCierre),YEAR(Doc_AlbaranesEntrada.dCierre)');

		if(is_numeric($id))
		{
			$this->db->where("Doc_AlbaranesEntrada.nIdProveedor = {$id}");
		}
		if(!empty($desde))
		{
			$desde = format_mssql_date($desde);
			$this->db->where("Doc_AlbaranesEntrada.dCierre >={$desde}");
		}
		if(!empty($hasta))
		{
			$hasta = format_mssql_date($hasta);
			$this->db->where("Doc_AlbaranesEntrada.dCierre < " . $this->db->dateadd('d', 1, $hasta));
		}
		$query = $this->db->get();
		$data = $this->_get_results($query);
		$compras = $this->obj->utils->year_meses_datos($data);

		# Devoluciones
		$this->db->flush_cache();
		$this->db->select('MONTH(Doc_Devoluciones.dCierre) mes, YEAR(Doc_Devoluciones.dCierre) y')
		->select_sum('ISNULL(Doc_LineasDevolucion.fCoste*Doc_LineasDevolucion.nCantidad, 0)', 'importe')
		->from("Doc_Devoluciones")
		->join('Doc_LineasDevolucion', 'Doc_LineasDevolucion.nIdDevolucion = Doc_Devoluciones.nIdDevolucion')
		->order_by('YEAR(Doc_Devoluciones.dCierre),MONTH(Doc_Devoluciones.dCierre)')
		->group_by('MONTH(Doc_Devoluciones.dCierre), YEAR(Doc_Devoluciones.dCierre)');

		if(is_numeric($id))
		{
			$this->db->where("Doc_Devoluciones.nIdProveedor = {$id}");
		}
		if(!empty($desde))
		{
			$this->db->where("Doc_Devoluciones.dCierre >={$desde}");
		}
		if(!empty($hasta))
		{
			$this->db->where("Doc_Devoluciones.dCierre < " . $this->db->dateadd('d', 1, $hasta));
		}
		$query = $this->db->get();
		$data = $this->_get_results($query);
		$devs = $this->obj->utils->year_meses_datos($data);

		return array('compras' => $compras, 'devoluciones' => $devs);

		foreach ($compras as $key => $value)
		{
			$final[$key]['compras'] = $value;
		}
		foreach ($devs as $key => $value)
		{
			$final[$key]['devoluciones'] = $value;
		}

		return $final;
	}

	/**
	 * Listado de artículos compradós (última compra) por el proveedor indicado
	 * @param  int  $id    Id del proveedor
	 * @param  boolean $stock TRUE: tiene Stock
	 * @param  date  $desde Fecha inicio de compra
	 * @param  date  $hasta Fecha final de compra
	 * @return array
	 */
	function comprados($id, $stock = FALSE, $desde = NULL, $hasta = NULL)
	{
		$this->db->flush_cache();
		$this->db->select('MAX(Doc_AlbaranesEntrada.nIdAlbaran) nIdAlbaran')
		->select('Cat_Fondo.nIdLibro, Cat_Fondo.cTitulo, Cat_Fondo.cAutores')
		->select('Cat_Editoriales.cNombre')
		->from('Doc_AlbaranesEntrada')
		->join('Doc_LineasAlbaranesEntrada', 'Doc_LineasAlbaranesEntrada.nIdAlbaran = Doc_AlbaranesEntrada.nIdAlbaran')
		->join('Cat_Fondo', 'Cat_Fondo.nIdLibro=Doc_LineasAlbaranesEntrada.nIdLibro')
		->join('Cat_Editoriales', 'Cat_Editoriales.nIdEditorial=Cat_Fondo.nIdEditorial', 'left')
		->where('Doc_AlbaranesEntrada.nIdProveedor='. $id)
		->group_by('Cat_Fondo.nIdLibro, Cat_Fondo.cTitulo, Cat_Fondo.cAutores')
		->group_by('Cat_Editoriales.cNombre')
		->order_by('Cat_Fondo.cTitulo');

		if(!empty($desde))
		{
			$desde = format_mssql_date($desde);
			$this->db->where("Doc_AlbaranesEntrada.dCierre >={$desde}");
		}
		if(!empty($hasta))
		{
			$hasta = format_mssql_date($hasta);
			$this->db->where("Doc_AlbaranesEntrada.dCierre < " . $this->db->dateadd('d', 1, $hasta));
		}
		if ($stock)
		{
			$this->db->join('Cat_Secciones_Libros', 'Cat_Secciones_Libros.nIdLibro = Cat_Fondo.nIdLibro')
			->join('Cat_Secciones', 'Cat_Secciones.nIdSeccion=Cat_Secciones_Libros.nIdSeccion')
			->select('Cat_Secciones.cNombre cSeccion')
			->select('Cat_Secciones_Libros.nStockFirme, Cat_Secciones_Libros.nStockDeposito')
			->where('Cat_Secciones_Libros.nStockFirme + Cat_Secciones_Libros.nStockDeposito > 0')
			->group_by('Cat_Secciones.cNombre')
			->group_by('Cat_Secciones_Libros.nStockFirme, Cat_Secciones_Libros.nStockDeposito');
		}

		$query = $this->db->get();
		$data = $this->_get_results($query);
		# Elimina los artículos que tienen una compra posterior a otro proveedor
		foreach ($data as $k => $reg)
		{
			$this->db->flush_cache();
			$this->db->select('count(*) ct')
			->from('Doc_AlbaranesEntrada')
			->join('Doc_LineasAlbaranesEntrada', 'Doc_LineasAlbaranesEntrada.nIdAlbaran = Doc_AlbaranesEntrada.nIdAlbaran')
			->where('Doc_AlbaranesEntrada.nIdProveedor<>'. $id)
			->where('Doc_LineasAlbaranesEntrada.nIdLibro=' . $reg['nIdLibro'])
			->where('Doc_AlbaranesEntrada.nIdAlbaran > '.$reg['nIdAlbaran']);
			$query = $this->db->get();
			$res = $this->_get_results($query);
			if ($res[0]['ct'] > 0)
				unset($data[$k]);
		}
		return $data;
	}

}
/* End of file M_proveedor.php */
/* Location: ./system/application/models/proveedores/m_proveedor.php */
