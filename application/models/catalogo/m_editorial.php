<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	catalogo * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Editoriales
 *
 */
class M_editorial extends MY_Model
{
	/**
	 * Constructor
	 * @return M_editorial
	 */
	function __construct()
	{
		$data_model = array(
			'cNombre'			=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE),
			'cNombreCorto'		=> array(DATA_MODEL_DEFAULT => TRUE),
			'nIdProveedor'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'proveedores/proveedor/search', 'cProveedor')),
			'tComentario' 		=> array(),
		);

		parent::__construct('Cat_Editoriales', 'nIdEditorial', 'cNombre', 'cNombre', $data_model, TRUE);
		#$this->_cache = TRUE;

		$this->_relations['codigos'] = array (
			'ref'	=> 'catalogo/m_editorialcodigo',
			'type'	=> DATA_MODEL_RELATION_1N,
			'fk'	=> 'nIdEditorial');
	}

	/**
	 * Análisis de los mivimientos de una editorial
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
		->join('Cat_Fondo', 'Cat_Fondo.nIdLibro=Doc_LineasAlbaranesEntrada.nIdLibro')
		->where('Doc_AlbaranesEntrada.nIdEstado IN (2, 3, 4)')
		->order_by('YEAR(Doc_AlbaranesEntrada.dCierre), MONTH(Doc_AlbaranesEntrada.dCierre)')
		->group_by('MONTH(Doc_AlbaranesEntrada.dCierre),YEAR(Doc_AlbaranesEntrada.dCierre)');

		if(is_numeric($id))
		{
			$this->db->where("Cat_Fondo.nIdEditorial = {$id}");
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
		->join('Cat_Fondo', 'Cat_Fondo.nIdLibro=Doc_LineasDevolucion.nIdLibro')
		->order_by('YEAR(Doc_Devoluciones.dCierre),MONTH(Doc_Devoluciones.dCierre)')
		->group_by('MONTH(Doc_Devoluciones.dCierre), YEAR(Doc_Devoluciones.dCierre)');

		if(is_numeric($id))
		{
			$this->db->where("Cat_Fondo.nIdEditorial = {$id}");
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

		# Ventas
		$this->db->flush_cache();
		$this->db->select_sum('Doc_LineasAlbaranesSalida.nCantidad', 'nCantidad')
		->select('YEAR(Doc_AlbaranesSalida.dCreacion) year')
		->select('MONTH(Doc_AlbaranesSalida.dCreacion) month')
		->select('Cat_Secciones.cNombre')
		->from('Doc_LineasAlbaranesSalida')
		->join('Doc_AlbaranesSalida' ,'Doc_LineasAlbaranesSalida.nIdAlbaran = Doc_AlbaranesSalida.nIdAlbaran')
		->join('Cat_Fondo', 'Cat_Fondo.nIdLibro=Doc_LineasAlbaranesSalida.nIdLibro')
		->join('Cat_Secciones', 'Cat_Secciones.nIdSeccion=Doc_LineasAlbaranesSalida.nIdSeccion')
		->where("Cat_Fondo.nIdEditorial = {$id}")
		->where('Doc_AlbaranesSalida.nIdEstado = 2')
		->group_by('Doc_LineasAlbaranesSalida.nIdSeccion')
		->group_by('Cat_Secciones.cNombre')
		->group_by('YEAR(Doc_AlbaranesSalida.dCreacion)')
		->group_by('MONTH(Doc_AlbaranesSalida.dCreacion)')
		->order_by('YEAR(Doc_AlbaranesSalida.dCreacion), MONTH(Doc_AlbaranesSalida.dCreacion)');

		if(is_numeric($id))
		{
			$this->db->where("Cat_Fondo.nIdEditorial = {$id}");
		}
		if(!empty($desde))
		{
			$this->db->where("Doc_AlbaranesSalida.dCreacion >={$desde}");
		}
		if(!empty($hasta))
		{
			$this->db->where("Doc_AlbaranesSalida.dCreacion < " . $this->db->dateadd('d', 1, $hasta));
		}
		$query = $this->db->get();
		$data = $this->_get_results($query);
		#var_dump($data); die();

		return array('compras' => $compras, 'devoluciones' => $devs, 'ventas' => $data);
	}


	/**
	 * Unificador de editoriales
	 * @param int $id1 Id de la editorial destino
	 * @param int $id2 Id de la editorial repetida
	 * @return bool, TRUE: correcto, FALSE: incorrecto
	 */
	function unificar($id1, $id2)
	{
		// TRANS
		$this->db->trans_begin();

		// Códigos
		$this->db->flush_cache();
		$this->db->where("cCodigo IN (SELECT Cat_Codigos.cCodigo FROM Cat_Codigos WHERE Cat_Codigos.nIdEditorial = {$id1})")
		->where("nIdEditorial = {$id2}")
		->delete('Cat_Codigos');
		if ($this->_check_error())
		{
			$this->db->trans_rollback();
			return FALSE;
		}

		$update = array (
			'nIdEditorial' => (int)$id1
		);

		$this->db->flush_cache();
		$this->db->where("nIdEditorial={$id2}")
		->update('Cat_Codigos', $update);
		if ($this->_check_error())
		{
			$this->db->trans_rollback();
			return FALSE;
		}

		// libros
		$editorial = $this->load($id1);
		if ($editorial)
		{
			$id_proveedor = $editorial['nIdProveedor'];
		}

		$update = array (
			'nIdEditorial' => (int)$id1,
			'nIdProveedor' => $id_proveedor
		);
		
		$audit = array();
		$this->_audit_upd($audit);
		$audit = $this->_filtra_datos($audit, FALSE);
		
		$update = array_merge($update, $audit);

		$this->db->flush_cache();
		$this->db->where("nIdEditorial={$id2}")
		->update('Cat_Fondo', $update);
		if ($this->_check_error())
		{
			$this->db->trans_rollback();
			return FALSE;
		}

		// Proveedores - Editoriales
		$this->db->flush_cache();
		$this->db->select('nIdProveedor, nIdTipo')
		->from('Prv_Proveedores_Editoriales_Tipos')
		->where("nIdEditorial IN ({$id1}, {$id2})")
		->group_by('nIdProveedor, nIdTipo')
		->having('COUNT(*) > 1');
		$query = $this->db->get();
		if ($this->_check_error())
		{
			$this->db->trans_rollback();
			return FALSE;
		}
		$data = $this->_get_results($query);
			
		foreach( $data as $r)
		{
			$this->db->flush_cache();
			$this->db->where("nIdEditorial={$id2}")
			->where("nIdProveedor = {$r['nIdProveedor']}")
			->where("nIdTipo = {$r['nIdTipo']}")
			->delete('Prv_Proveedores_Editoriales_Tipos');
			if ($this->_check_error())
			{
				$this->db->trans_rollback();
				return FALSE;
			}
		}

		$update = array('nIdEditorial' => (int)$id1);
		$this->db->flush_cache();
		$this->db->where("nIdEditorial = {$id2}")
		->update('Prv_Proveedores_Editoriales_Tipos', $update);
		if ($this->_check_error())
		{
			$this->db->trans_rollback();
			return FALSE;
		}

		$this->db->flush_cache();
		$this->db->where("nIdEditorial={$id2}")
		->delete('Prv_Proveedores_Editoriales_Tipos');
		if ($this->_check_error())
		{
			$this->db->trans_rollback();
			return FALSE;
		}

		// Colecciones
		$update = array('nIdEditorial' => (int)$id1);
		$update = array_merge($update, $audit);
		$this->db->flush_cache();
		$this->db->where("nIdEditorial = {$id2}")
		->update('Cat_Colecciones', $update);
		if ($this->_check_error())
		{
			$this->db->trans_rollback();
			return FALSE;
		}

		// Editoriales
		$res = $this->db->flush_cache();
		$this->db->where("nIdEditorial={$id2}")
		->delete('Cat_Editoriales');
		if ($this->_check_error())
		{
			$this->db->trans_rollback();
			return FALSE;
		}

		// Limpieza de caches
		$ci = get_instance();

		$ci->load->model('catalogo/m_articulo');
		$ci->m_articulo->clear_cache();

		$ci->load->model('catalogo/m_proveedoreditorial');
		$ci->m_proveedoreditorial->clear_cache();

		$ci->load->model('catalogo/m_editorialcodigo');
		$ci->m_editorialcodigo->clear_cache();

		$ci->load->model('catalogo/m_coleccion');
		$ci->m_coleccion->clear_cache();

		$this->clear_cache();

		// COMMIT
		$this->db->trans_commit();
		return TRUE;
	}

	/**
	 * @see system/application/libraries/MY_Model#onBeforeSelect($id, $sort, $dir, $where)
	 */
	protected function onBeforeSelect($id = null, &$sort = null, &$dir = null, &$where = null)
	{
		if (parent::onBeforeSelect($id, $sort, $dir, $where))
		{
			#echo '<pre>'; var_dump($where); echo '</pre>'; die();
			$this->db->select('Prv_Proveedores.cNombre cNombreProveedor, Prv_Proveedores.cApellido, Prv_Proveedores.cEmpresa');
			$this->db->join('Prv_Proveedores', "Prv_Proveedores.nIdProveedor = {$this->_tablename}.nIdProveedor", 'left');
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
			$data['cProveedor'] = format_name($data['cNombreProveedor'], $data['cApellido'], $data['cEmpresa']);
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeSearch($where, $fields)
	 */
	protected function onBeforeSearch($query, &$where, &$fields)
	{
		if (parent::onBeforeSearch($query, $where, $fields))
		{
			//Si es un ISBN lo añade a la búsqueda
			$this->load->library('ISBNEAN');
			$query = trim($query);
			if ($this->isbnean->is_publisher($query))
			{
				$where = "{$this->_tablename}.nIdEditorial IN (SELECT Cat_Codigos.nIdEditorial FROM Cat_Codigos WHERE Cat_Codigos.cCodigo = " . $this->db->escape($query) . ")";
			}
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
			if (isset($data['nIdProveedor']))
			{
				//Actualiza el proveedor de los artículos
				$res = $this->db->flush_cache();
				
				$this->db->where("nIdEditorial = {$id}");
				$this->db->where("nIdProveedorManual IS NULL");
				return $this->db->update('Cat_Fondo', array('nIdProveedor' => ($data['nIdProveedor']!=''?$data['nIdProveedor']:null)));
			}
		}
		return TRUE;
	}
}

/* End of file M_editorial.php */
/* Location: ./system/application/models/catalogo/M_editorial.php */