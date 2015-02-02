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
class M_documentocamara extends MY_Model
{
	/**
	 * Costructor 
	 * @return M_documentocamara
	 */
	function __construct()
	{
		$data_model = array(
			'dFecha'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATE),
			'nIdPais'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'perfiles/pais/search', 'cPais')), 
			'nIdTipoMercancia'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'compras/tipomercancia/search', 'cTipoMercancia')),
			'cFormaEnvio'		=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE)
		);

		$this->_relations['albaranes'] = array (
			'ref'	=> 'compras/m_albaranentrada',
			'type'	=> DATA_MODEL_RELATION_1N,
			'fk_other' => 'nIdDocumentoCamara',
			'fk'	=> 'nIdDocumento');

		parent::__construct('Doc_DocumentosCamara', 'nIdDocumento', 'dFecha', 'nIdDocumento', $data_model, TRUE);	
		$this->_cache = TRUE;
	}

	/**
	* Formas de envío de la mercancía
	* @return array
	*/
	function formasenvio()
	{
		$this->db->flush_cache();
		$this->db->select('cFormaEnvio id, cFormaEnvio text')
		->from('Doc_DocumentosCamara')
		->where('cFormaEnvio IS NOT NULL')
		->where('cFormaEnvio <> \'\'')
		->group_by('cFormaEnvio');
		$query = $this->db->get();
		$data = $this->_get_results($query);
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
		$this->obj->load->model('compras/m_divisacamara');
		$this->obj->load->model('compras/m_albaranentrada');
		# Coge el cambio de la cámara
		$cambios = $this->obj->m_divisacamara->get();
		$currency = array();
		foreach ($cambios as $c)
		{
			$currency[$c['cSimbolo']] = $c;
		}
		# Actualiza los albaranes
		$this->db->trans_begin();
		foreach ($doc['albaranes'] as $albaran) 
		{
			if (!isset($currency[$albaran['cSimbolo']]['fVenta']))
			{
				$this->_set_error_message(sprintf($this->lang->line('documento-camara-no-divisa'), $albaran['nIdAlbaran']));
				$this->db->trans_rollback();
				return FALSE;
			}
			#var_dump($currency[$albaran['cSimbolo']]['fVenta'],$albaran['cSimbolo']);
			if (!$this->obj->m_albaranentrada->update($albaran['nIdAlbaran'], array('fCambioCamara' => $currency[$albaran['cSimbolo']]['fVenta'])))
			{
				$this->_set_error_message($this->obj->m_albaranentrada->error_message());
				$this->db->trans_rollback();
				return FALSE;
			}
		}

		# Actualiza el documento
		if (!$this->update($id, array('dFecha' => time())))
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
			return FALSE;
		}
		$this->obj->load->model('compras/m_albaranentrada');
		# Actualiza los albaranes
		$this->db->trans_begin();
		foreach ($doc['albaranes'] as $albaran) 
		{
			if (!$this->obj->m_albaranentrada->update($albaran['nIdAlbaran'], array('fCambioCamara' => null)))
			{
				$this->_set_error_message($this->obj->m_albaranentrada->error_message());
				$this->db->trans_rollback();
				return FALSE;
			}
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
			$this->db->select('Gen_TiposMercancia.cDescripcion cTipoMercancia');
			$this->db->join('Gen_TiposMercancia', 'Doc_DocumentosCamara.nIdTipoMercancia = Gen_TiposMercancia.nIdTipoMercancia');
			$this->db->select('Gen_Paises.cNombre cPais');
			$this->db->join('Gen_Paises', 'Doc_DocumentosCamara.nIdPais= Gen_Paises.nIdPais', 'left');
			return TRUE;
		}
		return FALSE;
	}

}

/* End of file M_documentocamara.php */
/* Location: ./system/application/models/compras/M_documentocamara.php */