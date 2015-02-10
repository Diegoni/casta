<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	proveedores
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @filesource
 */

/**
 * Direcciones
 *
 */
class M_direccion extends MY_Model
{
	/**
	 * Costructor
	 * @return M_direccion
	 */
	function __construct()
	{
		$data_model = array(
			'cDescripcion'	=> array(), 
			'cTitular'		=> array(),	 
			'cCalle'		=> array(DATA_MODEL_REQUIRED => TRUE),
			'cCP' 			=> array(),
			'cPoblacion' 	=> array(),
			'nIdRegion' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'perfiles/region/search')),
			'nIdTipo'		=> array(DATA_MODEL_DEFAULT_VALUE => 1, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'perfiles/tipoperfil/search')), 
			'cRegionOtro' 	=> array(),
			'cPaisOtro' 	=> array(),
		#'bBorrada' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),
			'nIdProveedor'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT), 
		);

		parent::__construct('Prv_Direcciones', 'nIdDireccion', 'cDescripcion', 'cDescripcion', $data_model);
		#$this->_cache = TRUE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeSelect($id, $sort, $dir, $where)
	 */
	protected function onBeforeSelect($id = null, &$sort = null, &$dir = null, &$where = null)
	{
		if (parent::onBeforeSelect($id, $sort, $dir, $where))
		{
			$this->db->select('r.nIdPais, r.cNombre cRegion, p.cNombre cPais')
			->select($this->db->isnull('r.cIdioma', 'p.cIdioma', 'cIdioma'))
			->join('Gen_Regiones r', 'Prv_Direcciones.nIdRegion = r.nIdRegion', 'left')
			->join('Gen_Paises p', 'r.nIdPais = p.nIdPais', 'left');
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Obtiene un listado de direcciones
	 * @param int $id Id de cruce
	 * @return array
	 */
	function get_list($id, $long = FALSE)
	{
		$this->db->flush_cache();
		$this->db->select('d.*, p.cDescripcion cPerfil, r.cNombre cRegion, ps.cNombre cPais, ps.nIdPais, r.nIdRegion')
		->select($this->db->isnull('r.cIdioma', 'ps.cIdioma', 'cIdioma'))
		->from("{$this->_tablename} d")
		->join("Gen_TiposPerfil p", "d.nIdTipo = p.nIdTipo", 'left')
		->join("Gen_Regiones r", "d.nIdRegion = r.nIdregion", 'left')
		->join("Gen_Paises ps", "ps.nIdPais = r.nIdPais", 'left')
		->where("d.nIdProveedor = {$id}");

		$r = $this->db->get();
		$dir = $this->_get_results($r);
		$perfiles = array();
		foreach($dir as $d)
		{
			$dir = format_address($d, $long);
			$dir['nIdPais'] = $d['nIdPais'];
			$dir['nIdRegion'] = $d['nIdRegion'];
			$dir['cIdioma'] = isset($d['cIdioma'])?$d['cIdioma']:null;
			$perfiles[] = $dir;
		}

		return $perfiles;
	}

	/**
	 * Unificador de direcciones
	 * @param int $id1 Id del dirección destino
	 * @param int $id2 Id del dirección repetida
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
		$tablas[] = array('tabla' => 'Prv_Profiles');
		$tablas[] = array('tabla' => 'Doc_Devoluciones', 'model' => 'compras/m_devolucion');
		$tablas[] = array('tabla' => 'Doc_CancelacionesPedidoProveedor', 'model' => 'compras/m_cancelacion');
		$tablas[] = array('tabla' => 'Doc_ReclamacionesPedidoProveedor', 'model' => 'compras/m_reclamacion');
		$tablas[] = array('tabla' => 'Doc_Devoluciones', 'model' => 'compras/m_devolucion');
		$tablas[] = array('tabla' => 'Doc_LiquidacionDepositos', 'model' => 'compras/m_liquidaciondepositos');
		#$tablas[] = array('tabla' => 'Doc_FacturasProveedor');
		$tablas[] = array('tabla' => 'Gen_Observaciones', 'id' => 'nIdRegistro', 'where' => 'cTabla=\'Prv_Direcciones\'');

		// TRANS
		$this->db->trans_begin();

		foreach ($id_or as $id)
		{
			// Tablas
			if (!unificar_do($this, $tablas, $id1, $id, 'nIdDireccion'))
			{
				$this->db->trans_rollback();
				return FALSE;
			}
			if (!unificar_do($this, array(array('tabla' => 'Sus_Reclamaciones')), $id1, $id, 'nIdDireccionProveedor'))
			{
				$this->db->trans_rollback();
				return FALSE;
			}
		}

		// Borrado
		$this->db->flush_cache();
		$this->db->where("nIdDireccion IN ({$id2})")
		->delete('Prv_Direcciones');
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
}

/* End of file M_direccion.php */
/* Location: ./system/application/models/cliente/M_direccion.php */