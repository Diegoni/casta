<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	concursos
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Lineas Pedido  Concurso
 *
 */
class M_pedidoconcursolinea extends MY_Model
{
	/**
	 * Costructor
	 * @return M_pedidoconcursolinea
	 */
	function __construct()
	{
		$data_model = array(
   			'cConcurso'		 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_ALIAS, DATA_MODEL_READONLY => TRUE),
			'nIdBiblioteca'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'concursos/biblioteca/search', 'cBiblioteca')),
			'nIdSala'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'concursos/sala/search', 'cSala')),
			'nIdEstado' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'concursos/estadolineaconcurso/search')),
			'cISBN' 			=> array(),
			'cEAN' 				=> array(),
			'cAutores' 			=> array(),
			'cTitulo' 			=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_DEFAULT => TRUE),
			'nIdLibro' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'catalogo/articulo/search', 'cTitulo2')),
			'cRefCli' 			=> array(),
			'cEdicion' 			=> array(),
			'cSignatura' 		=> array(),
			'tTitolVolum' 		=> array(),
			'tTitolUniforme' 	=> array(),
			'tMateries' 		=> array(),
			'cBibid' 			=> array(),
			'cEditorial1a' 		=> array(),
			'cEditorial2a' 		=> array(),
			'fPrecio' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_MONEY),
			'cElxurro' 			=> array(),
			'cCDU' 				=> array(),
			'cCDU2' 			=> array(),
			'cProveedor' 		=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_ALIAS, DATA_MODEL_READONLY => TRUE),

			'nIdLineaPedidoProveedor' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
			'nIdLineaAlbaranEntrada'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
			'nIdLineaPedidoCliente' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
			'nIdLineaAlbaranSalida' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
			'nIdLineaDevolucion' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
		
			'nIdCambioLibro' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
			'nIdAlternativa' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),

			'nCaja' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
			'bNuevo' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),
			'bAnticipado' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),
			'nAlternativas' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
			'bValidado' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN, DATA_MODEL_DEFAULT => FALSE),
		);
		$this->_alias = array(
			'idc' 		=> array('Ext_Concursos.nIdConcurso', DATA_MODEL_TYPE_INT),
			'idg' 		=> array('Ext_EstadosConcurso.nIdGrupoEstado', DATA_MODEL_TYPE_INT),			
			);

		parent::__construct('Ext_LineasPedidoConcurso', 'nIdLineaPedidoConcurso', 'cTitulo', array('nIdLibro', 'cTitulo', 'cAutores', 'Cat_Fondo.cTitulo'), $data_model, TRUE);
	}	

	/**
	 * Muestra el histórico de cambios de estado
	 * @param  int $id Id de la línea
	 * @return array
	 */
	function estados($id)
	{
		$this->db->flush_cache();
		$this->db->select('Ext_EstadosConcurso.cDescripcion, Ext_CambiosEstadoLineaConcurso.cCUser')
		->select($this->db->date_field('Ext_CambiosEstadoLineaConcurso.dCreacion', 'dCreacion'))
		->from('Ext_CambiosEstadoLineaConcurso')
		->join('Ext_EstadosConcurso', 'Ext_CambiosEstadoLineaConcurso.nIdEstado=Ext_EstadosConcurso.nIdEstado')
		->where('nIdLineaPedidoConcurso='. $id)
		->order_by('Ext_CambiosEstadoLineaConcurso.dCreacion');
		$query = $this->db->get();
		return $this->_get_results($query);
	}

	/**
	 * Obtiene el último albarán de entrada de un artículo
	 * @param  int $id Id del artículo
	 * @return int Id de la línea, null si no hay albaranes de entrada
	 */
	function get_last_albaran($id)
	{
		$this->obj->load->model('compras/m_albaranentrada');
		// Entrada
		$this->db->flush_cache();
		$this->db->select_max('la.nIdLinea', 'nIdLinea')
		->from('Doc_AlbaranesEntrada a')
		->join('Doc_LineasAlbaranesEntrada la', 'a.nIdAlbaran = la.nIdAlbaran')
		->where("la.nIdLibro = {$id}")
		->where('a.nIdEstado=' . ALBARAN_ENTRADA_STATUS_ASIGNADO)
		->group_by('la.nIdLibro');
		$query = $this->db->get();
		$d = $this->_get_results($query);
		if (isset($d[0]))
		{
			return $d[0]['nIdLinea'];
		}
		return null;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeSelect($id, $sort, $dir, $where)
	 */
	protected function onBeforeSelect($id = null, &$sort = null, &$dir = null, &$where = null)
	{
		if (parent::onBeforeSelect($id, $sort, $dir, $where))
		{
			$sort = str_replace('cProveedor', "Prv_Proveedores.cNombre {$dir}, Prv_Proveedores.cApellido {$dir}, Prv_Proveedores.cEmpresa", $sort);

			$this->db->select('Ext_Bibliotecas.cDescripcion cBiblioteca')
			->select('Ext_Concursos.cDescripcion cConcurso, Ext_Concursos.nIdConcurso')
			->select('Ext_Concursos.fDescuento')
			->select('Ext_Salas.cDescripcion cSala')
			->select('Ext_EstadosConcurso.cDescripcion cEstado')
			->select('Ext_GrupoEstadosConcurso.cDescripcion cEstadoGrupo')
			->join('Ext_Bibliotecas', "Ext_Bibliotecas.nIdBiblioteca = {$this->_tablename}.nIdBiblioteca", 'left')
			->join('Ext_Concursos', "Ext_Concursos.nIdConcurso = Ext_Bibliotecas.nIdConcurso", 'left')
			->join('Ext_Salas', "Ext_Salas.nIdSala = {$this->_tablename}.nIdSala", 'left')
			->join('Ext_EstadosConcurso', "Ext_EstadosConcurso.nIdEstado = {$this->_tablename}.nIdEstado", 'left')
			->join('Ext_GrupoEstadosConcurso', "Ext_EstadosConcurso.nIdGrupoEstado = Ext_GrupoEstadosConcurso.nIdGrupoEstado", 'left');
			
			$this->db->select('Cat_Fondo.cTitulo cTitulo2')
			->select('Cat_Fondo.cCUser cCUser2')
			->select('Cat_Fondo.cISBN cISBN2')
			->select('Cat_Fondo.nEAN nEAN')
			->select('Cat_Fondo.cAutores cAutores2')
			->select('Cat_Fondo.fPrecio fPrecio2')
			->select('Cat_Tipos.fIVA fIVA')
			->select('Cat_Fondo.fPrecioCompra fCoste')
			->select('Cat_Fondo.cEdicion cEdicion')
			->select('YEAR(Cat_Fondo.dEdicion) dEdicion')
			->select('Prv_Proveedores.nIdProveedor, Prv_Proveedores.cNombre, Prv_Proveedores.cApellido, Prv_Proveedores.cEmpresa')
			->select($this->_date_field('Cat_Fondo.dCreacion', 'dCreacion2'))
			->select('Cat_Editoriales.cNombre cEditorial')
			->join('Cat_Fondo', "Cat_Fondo.nIdLibro = {$this->_tablename}.nIdLibro", 'left')
			->join('Cat_Tipos', "Cat_Fondo.nIdTipo = Cat_Tipos.nIdTipo", 'left')
			->join('Cat_Editoriales', "Cat_Fondo.nIdEditorial = Cat_Editoriales.nIdEditorial", 'left')
			->join('Prv_Proveedores', "Prv_Proveedores.nIdProveedor = " .$this->db->isnull('Cat_Fondo.nIdProveedor', 'Cat_Editoriales.nIdProveedor'), 'left');


			$this->db->select('Doc_LineasPedidoProveedor.nIdPedido nIdPedidoProveedor')
			->select('Doc_LineasAlbaranesEntrada.nIdAlbaran nIdAlbaranEntrada')
			->select('Doc_LineasPedidoCliente.nIdPedido nIdPedidoCliente')
			->select('Doc_LineasAlbaranesSalida.nIdAlbaran nIdAlbaranSalida')
			->select('Doc_LineasDevolucion.nIdDevolucion nIdDevolucion')
			->join('Doc_LineasPedidoProveedor', "Doc_LineasPedidoProveedor.nIdLinea = {$this->_tablename}.nIdLineaPedidoProveedor", 'left')
			->join('Doc_LineasAlbaranesEntrada', "Doc_LineasAlbaranesEntrada.nIdLinea = {$this->_tablename}.nIdLineaAlbaranEntrada", 'left')
			->join('Doc_LineasPedidoCliente', "Doc_LineasPedidoCliente.nIdLinea = {$this->_tablename}.nIdLineaPedidoCliente", 'left')
			->join('Doc_LineasAlbaranesSalida', "Doc_LineasAlbaranesSalida.nIdLineaAlbaran = {$this->_tablename}.nIdLineaAlbaranSalida", 'left')
			->join('Doc_LineasDevolucion', "Doc_LineasDevolucion.nIdLinea = {$this->_tablename}.nIdLineaDevolucion", 'left');

			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Devuelve las sala con alternativas
	 * @param  int $concurso ID del concurso
	 * @return array
	 */
	function alternativas_salas($concurso)
	{
		# Ventas
		$this->db->flush_cache();
		$this->db->select('Ext_Salas.cDescripcion, Ext_Salas.nIdSala')
		->select('count(*) nContador')
		->from($this->_tablename)
		->join('Ext_Bibliotecas', "Ext_Bibliotecas.nIdBiblioteca = {$this->_tablename}.nIdBiblioteca", 'left')
		->join('Ext_Concursos', "Ext_Concursos.nIdConcurso = Ext_Bibliotecas.nIdConcurso", 'left')
		->join('Ext_Salas', "Ext_Salas.nIdSala = {$this->_tablename}.nIdSala", 'left')
		->where("Ext_Concursos.nIdConcurso = {$concurso}")
		->where('nIdEstado=10')
		->group_by('Ext_Salas.cDescripcion, Ext_Salas.nIdSala')
		->order_by('Ext_Salas.cDescripcion');

		$query = $this->db->get();
		$data = $this->_get_results($query);
		return $data;
	}

	/**
	 * Devuelve las sala con alternativas
	 * @param  int $concurso ID del concurso
	 * @param  int  $tipo, 0 = todo, 1 = decartadas, 2 = aceptadas
	 * @param  array $estado Id de los estados 
	 * @return array
	 */
	function estadisticas_salas($concurso, $tipo, $estado = null)
	{
		# Ventas
		$this->db->flush_cache();
		$this->db->select('Ext_Salas.cDescripcion, Ext_Salas.nIdSala')
		->select('count(*) nContador')
		->from($this->_tablename)
		->join('Ext_Bibliotecas', "Ext_Bibliotecas.nIdBiblioteca = {$this->_tablename}.nIdBiblioteca", 'left')
		->join('Ext_Concursos', "Ext_Concursos.nIdConcurso = Ext_Bibliotecas.nIdConcurso", 'left')
		->join('Ext_Salas', "Ext_Salas.nIdSala = {$this->_tablename}.nIdSala", 'left')
		->where("Ext_Concursos.nIdConcurso = {$concurso}")
		->group_by('Ext_Salas.cDescripcion, Ext_Salas.nIdSala')
		->order_by('Ext_Salas.cDescripcion');
		if ($tipo == 0) 
		{
			$this->db->where('((nIdEstado=8 AND nIdCambioLibro IS NOT NULL) OR nIdEstado=12)');
		}
		elseif ($tipo == 1) 
		{
			$this->db->where('(nIdEstado=8 AND nIdCambioLibro IS NOT NULL)');
		}
		elseif ($tipo == 2) 
		{
			$this->db->where('nIdEstado = 12');
		}
		if (isset($estado))
		{
			if (!is_array($estado)) 
				$estado = "$estado";
			else
				$estado = implode(',', $estado);
			$this->db->where("nIdEstado IN ({$estado})");
		}

		$query = $this->db->get();
		$data = $this->_get_results($query);
		#echo '<pre>'; print_r($this->db->queries); die();
		return $data;
	}

	/**
	 * Devuelve las sala con alternativas
	 * @param  int $concurso ID del concurso
	 * @param  int  $tipo, 0 = todo, 1 = decartadas, 2 = aceptadas
	 * @param  array $estado Id de los estados 
	 * @return array
	 */
	function estadisticas_bibliotecas($concurso, $tipo, $estado = null)
	{
		# Ventas
		$this->db->flush_cache();
		$this->db->select('Ext_Bibliotecas.cDescripcion, Ext_Bibliotecas.nIdBiblioteca')
		->select('count(*) nContador')
		->from($this->_tablename)
		->join('Ext_Bibliotecas', "Ext_Bibliotecas.nIdBiblioteca = {$this->_tablename}.nIdBiblioteca", 'left')
		->join('Ext_Concursos', "Ext_Concursos.nIdConcurso = Ext_Bibliotecas.nIdConcurso", 'left')
		->where("Ext_Concursos.nIdConcurso = {$concurso}")
		->group_by('Ext_Bibliotecas.cDescripcion, Ext_Bibliotecas.nIdBiblioteca')
		->order_by('Ext_Bibliotecas.cDescripcion');
		if ($tipo == 0) 
		{
			$this->db->where('((nIdEstado=8 AND nIdCambioLibro IS NOT NULL) OR nIdEstado=12)');
		}
		elseif ($tipo == 1) 
		{
			$this->db->where('(nIdEstado=8 AND nIdCambioLibro IS NOT NULL)');
		}
		elseif ($tipo == 2) 
		{
			$this->db->where('nIdEstado = 12');
		}
		if (isset($estado))
		{
			if (!is_array($estado)) 
				$estado = "$estado";
			else
				$estado = implode(',', $estado);
			$this->db->where("nIdEstado IN ({$estado})");
		}

		$query = $this->db->get();
		$data = $this->_get_results($query);
		#echo '<pre>'; print_r($this->db->queries); die();
		return $data;
	}


	function percentatges($concurso)
	{
		$this->db->flush_cache();
		$this->db->select('Ext_Bibliotecas.cDescripcion cBiblioteca, Ext_Bibliotecas.nIdBiblioteca')
		->select('Ext_Salas.cDescripcion cSala, Ext_Salas.nIdSala')
		->select("{$this->_tablename}.nIdEstado")
		->select('count(*) nContador')
		->select_sum('fPrecio', 'fPrecio')
		->from($this->_tablename)
		->join('Ext_Bibliotecas', "Ext_Bibliotecas.nIdBiblioteca = {$this->_tablename}.nIdBiblioteca", 'left')
		->join('Ext_Concursos', "Ext_Concursos.nIdConcurso = Ext_Bibliotecas.nIdConcurso", 'left')
		->join('Ext_Salas', "Ext_Salas.nIdSala = {$this->_tablename}.nIdSala", 'left')
		->where("Ext_Concursos.nIdConcurso = {$concurso}")
		->group_by('Ext_Bibliotecas.cDescripcion, Ext_Bibliotecas.nIdBiblioteca, Ext_Salas.cDescripcion, Ext_Salas.nIdSala')
		->group_by("{$this->_tablename}.nIdEstado")
		->order_by('Ext_Bibliotecas.cDescripcion, Ext_Salas.cDescripcion');

		$query = $this->db->get();
		$data = $this->_get_results($query);
		#echo '<pre>'; print_r($this->db->queries); die();
		return $data;
	}

	function albarans($concurso)
	{
		$this->db->flush_cache();
		$this->db->select('Ext_Bibliotecas.cDescripcion cBiblioteca')
		->select('Ext_Salas.cDescripcion cSala')
		->select('Doc_AlbaranesSalida.nIdAlbaran')
		->select($this->db->date_field('Doc_AlbaranesSalida.dCreacion', 'dCreacion'))
		->from($this->_tablename)
		->join('Doc_LineasAlbaranesSalida', "Doc_LineasAlbaranesSalida.nIdLineaAlbaran={$this->_tablename}.nIdLineaAlbaranSalida")
		->join('Doc_AlbaranesSalida', "Doc_LineasAlbaranesSalida.nIdAlbaran=Doc_AlbaranesSalida.nIdAlbaran")
		->join('Ext_Bibliotecas', "Ext_Bibliotecas.nIdBiblioteca = {$this->_tablename}.nIdBiblioteca", 'left')
		->join('Ext_Salas', "Ext_Salas.nIdSala = {$this->_tablename}.nIdSala", 'left')
		->join('Ext_Concursos', "Ext_Concursos.nIdConcurso = Ext_Bibliotecas.nIdConcurso", 'left')
		->where("Ext_Concursos.nIdConcurso = {$concurso}")
		->group_by('Doc_AlbaranesSalida.nIdAlbaran, Doc_AlbaranesSalida.cRefCliente')	
		->group_by($this->db->date_field('Doc_AlbaranesSalida.dCreacion'))
		->group_by('Ext_Bibliotecas.cDescripcion')
		->group_by('Ext_Salas.cDescripcion')
		->order_by('Ext_Bibliotecas.cDescripcion')
		->order_by('Ext_Salas.cDescripcion')
		->order_by('Doc_AlbaranesSalida.nIdAlbaran');

		$query = $this->db->get();
		$data = $this->_get_results($query);
		#echo '<pre>'; print_r($this->db->queries); die();
		return $data;
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
			$data['fPVP'] = isset($data['fPrecio'])?format_add_iva($data['fPrecio'], $data['fIVA']):0;
			$data['fPVP2'] = isset($data['fPrecio2'])?format_add_iva($data['fPrecio2'], $data['fIVA']):0;
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * @see system/application/libraries/MY_Model#onParseWhere
	 */
	protected function onParseWhere(&$where)
	{
		parent::onParseWhere($where);
		#print '<pre>'; print_r($where); print '</pre>'; die();
		if (isset($where['cTitulo2'])) 
		{
			$where['Cat_Fondo.cTitulo'] = $where['cTitulo2'];
			unset($where['cTitulo2']);
		}
		if (isset($where['cAutores2'])) 
		{
			$where['Cat_Fondo.cAutores'] = $where['cAutores2'];
			unset($where['cAutores2']);
		}
		if (isset($where['cProveedor'])) 
		{
			$value = $this->db->escape_str($where['cProveedor']);
			$w = boolean_sql_where($value, $this->_complete_field('Cli_Cliente.cliente'), $this->_get_type_parser('Cli_Cliente.cliente'));
			$w = str_replace('Cli_Cliente.cliente', $this->db->concat(array('Prv_Proveedores.cEmpresa', 'Prv_Proveedores.cNombre', 'Prv_Proveedores.cApellido')), $w);
			$where[count($where)] = $w;
			unset($where['cProveedor']);
		}
		if (isset($where['cEditorial'])) 
		{
			$where['Cat_Editoriales.cNombre'] = $where['cEditorial'];
			unset($where['cEditorial']);
		}
		if (!empty($where['cISBN']) || !empty($where['cEAN']))
		{
			$this->load->library('ISBNEAN');
			$ean = $this->isbnean->to_ean(!empty($where['cEAN'])?$where['cEAN']:$where['cISBN']);
			if ($ean)
			{
				$where[] = 'cEAN='. $this->db->escape($ean) . " OR nEAN={$ean}";
				unset($where['cISBN']);
				unset($where['cEAN']);
			}
		}
		#var_dump($where); die();
		return TRUE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeUpdate($data)
	 */
	protected function onBeforeUpdate($id, &$data)
	{
		if (parent::onBeforeUpdate($id, $data))
		{
			if (isset($data['nIdEstado']))
			{
				if (!isset($old)) $old = $this->load($id);
				if ($old['nIdEstado'] != $data['nIdEstado'])
				{
					$this->obj->load->model('concursos/m_cambioestado');
					$ins = array(
						'nIdLineaPedidoConcurso' => (int)$id,
						'nIdEstado' =>  $data['nIdEstado']
					);
					if (!$this->obj->m_cambioestado->insert($ins))
					{
						$this->_set_error_message($this->obj->m_cambioestado->error_message());
						return FALSE;
					}
				}	
			}			
		}
		return TRUE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeDelete($id)
	 */
	protected function onBeforeDelete($id) 
	{
		if(parent::onBeforeDelete($id))
		{
			$this->obj->load->model('concursos/m_cambioestado');
			if (!$this->obj->m_cambioestado->delete_by("nIdLineaPedidoConcurso={$id}"))
			{
				$this->_set_error_message($this->obj->m_cambioestado->error_message());
				return FALSE;
			}
			
			return TRUE;
		}
		return FALSE;
	}
}

/* End of file M_pedidoconcursolinea.php */
/* Location: ./system/application/models/concursos/M_pedidoconcursolinea.php */