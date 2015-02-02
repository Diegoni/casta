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

/**
 * Documentos de la cámara del libro
 *
 */
class M_liquidaciondepositos extends MY_Model
{
	/**
	 * Costructor 
	 * @return M_liquidaciondepositos
	 */
	function __construct()
	{
		$data_model = array(
            'nIdProveedor' 		=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'proveedores/proveedor/search', 'cProveedor')),
            'nIdDireccion' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'proveedores/direccion/search')),
			'cRefProveedor' 	=> array(), 
			'cRefInterna'		=> array(),
			'dFecha'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATE),
            'tNotasExternas' 	=> array(),
            'tNotasInternas' 	=> array(),
            'nLibros' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
            'fTotal' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_MONEY),
		);

		$this->_alias = array(
			'cProveedor' 	=> array('(Prv_Proveedores.cEmpresa + Prv_Proveedores.cNombre + Prv_Proveedores.cApellido)'),
		);

		$this->_relations['proveedor'] = array (
			'ref'	=> 'proveedores/m_proveedor',
			'fk'	=> 'nIdProveedor');

		$this->_relations['direccion'] = array (
			'ref'	=> 'proveedores/m_direccion',
			'fk'	=> 'nIdDireccion');

		$this->_relations['lineas'] = array (
			'ref'	=> 'ventas/m_albaransalidalineadeposito',
			'type'	=> DATA_MODEL_RELATION_1N,
			'fk_other' => 'nIdDocumentoDeposito',
			'fk'	=> 'nIdDocumento');

		parent::__construct('Doc_LiquidacionDepositos', 'nIdDocumento', 'dFecha', 'nIdDocumento', $data_model, TRUE);	
		$this->_cache = TRUE;
	}

	/**
	 * Busca las ventas no liquidadas del proveedor indicado
	 * @param int $id Id del proveedor
	 * @param int $desde Fecha máxima (timespam)
	 */
	function get_items($id, $desde = null, $linea = null, $art = null)
	{
		set_time_limit(0);
		$this->db->flush_cache();
		$this->db
		->select('Cat_Fondo.nIdLibro, Cat_Fondo.cTitulo, Cat_Editoriales.cNombre')
		->select('Cat_Tipos.fIVA')
		->select($this->_date_field('Doc_LineasAlbaranesSalida.dCreacion', 'dCreacion'))
		->from('Doc_LineasAlbaranesSalida')
		->join('Cat_Fondo', 'Cat_Fondo.nIdLibro=Doc_LineasAlbaranesSalida.nIdLibro')
		->join('Cat_Tipos', 'Cat_Tipos.nIdTipo=Cat_Fondo.nIdTipo')
		->join('Cat_Editoriales', 'Cat_Fondo.nIdEditorial=Cat_Editoriales.nIdEditorial', 'left')
		->where('Doc_LineasAlbaranesSalida.bLiquidado=0')
		->where('Doc_LineasAlbaranesSalida.nIdDocumentoDeposito IS NULL')
		->where('Doc_LineasAlbaranesSalida.nEnDeposito<>0')
		->where($this->db->isnull('Cat_Fondo.nIdProveedor', 'Cat_Editoriales.nIdProveedor') . '=' . $id)
		->order_by('Doc_LineasAlbaranesSalida.dCreacion');

		$group = TRUE;

		if (isset($art))
		{
			$this->db->where("Doc_LineasAlbaranesSalida.nIdLibro=" . $art);
			$group = FALSE;
		}
		if (isset($linea))
		{
			$this->db->where("Doc_LineasAlbaranesSalida.nIdLineaAlbaran<=" . $linea);
			$group = FALSE;
		}

		if (isset($desde))
		{
			$desde = format_mssql_date($desde);
			$this->db->where("Doc_LineasAlbaranesSalida.dCreacion < " . $this->db->dateadd('d', 1, $desde));
		}
		if ($group)
		{
			$this->db
			->select_sum('Doc_LineasAlbaranesSalida.nEnDeposito', 'nEnDeposito')
			->select('MAX(Doc_LineasAlbaranesSalida.fPrecio) fPrecio')
			->select('MAX(Doc_LineasAlbaranesSalida.nIdLineaAlbaran) nIdLineaAlbaran')
			->group_by('Cat_Fondo.nIdLibro, Cat_Fondo.cTitulo, Cat_Editoriales.cNombre, Cat_Tipos.fIVA')
			->having('SUM(Doc_LineasAlbaranesSalida.nEnDeposito) > 0');
		}
		else
		{
			$this->db->select('Doc_LineasAlbaranesSalida.nIdLineaAlbaran')
			->select('Doc_LineasAlbaranesSalida.fPrecio')
			->select('Doc_LineasAlbaranesSalida.nEnDeposito');
		}
		$query = $this->db->get();
		$data = $this->_get_results($query);
		#echo array_pop($this->db->queries); die();
		foreach ($data as $key => $value) 
		{
			$data[$key]['fPVP'] = format_add_iva($value['fPrecio'], $value['fIVA']);
		}
		#var_dump($data); die();
		return $data;
	}

	/**
	 * Cierra un documento. Asigna los cambios de la divisa
	 * @param int $id Id del documento
	 * @return bool
	 */
	function cerrar($id)
	{
		$doc = $this->load($id, TRUE);
		if (isset($doc['dFecha']))
		{
			$this->_set_error_message($this->lang->line('documento-cerrado'));
			return FALSE;
		}

		$this->obj->load->model('compras/m_albaranentradalinea');
		$this->obj->load->model('compras/m_liquidaciondepositoslinea');
		$this->obj->load->model('ventas/m_albaransalidalinea');		
		#$this->obj->load->model('compras/m_devolucionlinea');
		#$this->obj->load->model('catalogo/m_articuloseccion');

		#var_dump($doc); die();

		$this->db->trans_begin();
		
		$total = 0;
		$libros = 0;
		foreach ($doc['lineas'] as $linea)
		{
			$data = null;
			if ($linea['nEnDeposito'] > 0)
			{
				// Busca el albarán de entrada de estas líneas
				$this->db->flush_cache();
				$this->db->select("Doc_LineasAlbaranesEntrada.nIdLinea")
				->select('Doc_LineasAlbaranesEntrada.nCantidad, Doc_LineasAlbaranesEntrada.nCantidadLiquidada')
				->select('Doc_LineasAlbaranesEntrada.fPrecio, Doc_LineasAlbaranesEntrada.fDescuento, Doc_LineasAlbaranesEntrada.fIVA, Doc_LineasAlbaranesEntrada.fRecargo')
				->from('Doc_LineasAlbaranesEntrada')
				->join('Doc_AlbaranesEntrada', 'Doc_LineasAlbaranesEntrada.nIdAlbaran=Doc_AlbaranesEntrada.nIdAlbaran')
				->where('Doc_LineasAlbaranesEntrada.nCantidad - Doc_LineasAlbaranesEntrada.nCantidadLiquidada > 0')
				->where("Doc_LineasAlbaranesEntrada.nIdLibro={$linea['nIdLibro']}")
				->where('Doc_LineasAlbaranesEntrada.fDescuento < 100')
				->where('Doc_AlbaranesEntrada.nIdEstado <> 1')
				->where("Doc_AlbaranesEntrada.bDeposito = 1")
				->order_by('Doc_LineasAlbaranesEntrada.dCreacion', 'DESC')
				->limit($linea['nCantidad']);
				$query = $this->db->get();
				#print array_pop($this->db->queries); die();
				$data = $this->_get_results($query);
			}
			$ct = $linea['nCantidad'];
			$libros += $linea['nCantidad'];

			if (isset($data))
			{
				foreach ($data as $reg)
				{
					// Asigna las unidades devueltas
					$libre = $reg['nCantidad'] - $reg['nCantidadLiquidada'];
					$asignar = min($libre, $ct);
					$upd = array(
                    	'nCantidadLiquidada' => $reg['nCantidadLiquidada'] + $asignar,
					);
					if (!$this->obj->m_albaranentradalinea->update($reg['nIdLinea'], $upd))
					{
						$this->_set_error_message($this->obj->m_albaranentradalinea->error_message());
						$this->db->trans_rollback();
						return FALSE;
					}

					$upd = array(
		            	'bLiquidado' 	=> TRUE,
					);
					
					if (!$this->obj->m_albaransalidalinea->update($linea['nIdLineaAlbaran'], $upd))
					{
						$this->_set_error_message($this->obj->m_albaransalidalinea->error_message());
						$this->db->trans_rollback();
						return FALSE;
					}

					// Crea la línea de liquidación
					// Cantidad la asignada
					$ins = array(
						'nIdDocumento'		=> $id,
	                    'fPrecio' 			=> $reg['fPrecio'],
	                    'fDescuento' 		=> $reg['fDescuento'],
	                    'nIdLineaEntrada' 	=> $reg['nIdLinea'],
	                    'nIdLineaSalida' 	=> $linea['nIdLineaAlbaran'],
	                    'nCantidad' 		=> $asignar,
						'fIVA' 				=> $reg['fIVA'],
						'fRecargo' 			=> $reg['fRecargo'],
						'nCantidad' 		=> $asignar,
					);

					if ($this->obj->m_liquidaciondepositoslinea->insert($ins) < 0)
					{
						$this->_set_error_message($this->obj->m_liquidaciondepositoslinea->error_message());
						$this->db->trans_rollback();
						return FALSE;
					}

					$totales = format_calculate_importes($ins);
					$total += $totales['fTotal'];

					$ct -= $asignar;

					// Se han asignado todas?
					if ($ct == 0)
						break;
				}
			}
			// No hay albaranes de entrada suficientes para esta liquidación...?
			/*if ($ct != 0)
			{
				$upd = array(
                    'nIdEstado' => DEVOLUCION_LINEA_STATUS_CERRADA,
					'nCantidad' => $ct
				);
				if (!$this->obj->m_devolucionlinea->update($linea['nIdLinea'], $upd))
				{
					$this->_set_error_message($this->obj->m_devolucionlinea->error_message());
					$this->db->trans_rollback();
					return FALSE;
				}
				$linea['nCantidad'] = $ct;
				$totales = format_calculate_importes($linea);
				$total += $totales['fTotal'];
			}*/
		}


		# Actualiza el documento
		if (!$this->update($id, array(
			'dFecha' => time(),
			'fTotal'		=> $total,
			'nLibros'		=> $libros			
			)))
		{
			$this->db->trans_rollback();
			return FALSE;
		}
		$this->db->trans_commit();
		#die();

		return TRUE;
	}


	/**
	 * Abri un documento. Quita la asignación de la divisa
	 * @param int $id Id del documento
	 * @return bool
	 */
	function abrir($id)
	{
		$doc = $this->load($id, TRUE);
		if (!isset($doc['dFecha']))
		{
			$this->_set_error_message($this->lang->line('documento-abierto'));
			$this->db->trans_rollback();
			return FALSE;
		}
		$this->obj->load->model('compras/m_albaranentrada');
		$this->obj->load->model('compras/m_albaranentradalinea');
		$this->obj->load->model('ventas/m_albaransalidalinea');		
		$this->obj->load->model('compras/m_liquidaciondepositoslinea');
		# Actualiza los albaranes
		$this->db->trans_begin();

		foreach ($doc['lineas'] as $linea) 			
		{
			$upd = array(
            	'nCantidadLiquidada' 	=> $linea['nCantidadLiquidada'] - $linea['nEnDeposito'],
			);

			if (!$this->obj->m_albaranentradalinea->update($linea['nIdLineaAlbaranEntrada'], $upd))
			{
				$this->_set_error_message($this->obj->m_albaranentradalinea->error_message());
				$this->db->trans_rollback();
				return FALSE;
			}

			$upd = array(
            	'bLiquidado' 	=> FALSE,
			);

			if (!$this->obj->m_albaransalidalinea->update($linea['nIdLineaAlbaran'], $upd))
			{
				$this->_set_error_message($this->obj->m_albaransalidalinea->error_message());
				$this->db->trans_rollback();
				return FALSE;
			}
		}

		if (!$this->obj->m_liquidaciondepositoslinea->delete_by('nIdDocumento=' . $id))
		{
			$this->db->trans_rollback();
			$this->_set_error_message($this->m_liquidaciondepositoslinea->error_message());
			return FALSE;
		}

		# Actualiza el documento
		if (!$this->update($id, array('dFecha' => null)))
		{
			$this->db->trans_rollback();
			return FALSE;
		}
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
			$this->db->join('Prv_Proveedores', "{$this->_tablename}.nIdProveedor = Prv_Proveedores.nIdProveedor");
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

}

/* End of file M_documentocamara.php */
/* Location: ./system/application/models/compras/M_documentocamara.php */