<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	eoi
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @filesource
 */

/**
 * Importes EOI
 * Los tipos son
 * 1: Idioma
 * 2: Curso dentro del idioma
 * 3: Título obligatorio
 * 4: Lecturas 
 * 5: Material complementario
 *
 */
class M_titulo extends MY_Model
{
	/**
	 * Constructor
	 *
	 * @return M_titulo
	 */
	function __construct()
	{
		$data_model = array(
			'nIdCurso'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'eoi/curso/search', 'cCurso')),
			'cDescripcion'	=> array(),
			'nTipo'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE),
			'nIdRegistro'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'catalogo/articulo/search', 'cTitulo')),
			'nIdTituloPadre'=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'eoi/titulo/search', 'cTituloPadre')),
		);

		parent::__construct('Ext_EOISTitulos', 'nIdTitulo', 'cDescripcion', 'cDescripcion', $data_model, TRUE);
		$this->_cache = TRUE;
	}

	/**
	 * Devuelve las secciones de un padre
	 * @param $id ID del pader
	 * @return array
	 */
	function get_by_padre($id = null, $padre = null)
	{
		if ($padre)
		{
			return $this->get(null, null, 'nTipo, cDescripcion', null, "nIdCurso={$id} AND nIdTituloPadre={$padre}");
		}
		else
		{
			return $this->get(null, null, 'nTipo, cDescripcion', null, "nIdCurso={$id} AND nIdTituloPadre IS NULL");
		}
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeSelect($id, $sort, $dir, $where)
	 */
	protected function onBeforeSelect($id = null, &$sort = null, &$dir = null, &$where = null)
	{
		if (parent::onBeforeSelect($id, $sort, $dir, $where))
		{
			$this->db->select('Cat_Fondo.cTitulo');
			$this->db->join('Cat_Fondo', "Cat_Fondo.nIdLibro = {$this->_tablename}.nIdRegistro", 'left');
			$this->db->select('Ext_EOISCursos.cDescripcion cCurso');
			$this->db->join('Ext_EOISCursos', "{$this->_tablename}.nIdCurso=Ext_EOISCursos.nIdCurso");
			$this->db->select("{$this->_tablename}2.cDescripcion cTituloPadre");
			$this->db->join("{$this->_tablename} {$this->_tablename}2", "{$this->_tablename}.nIdTituloPadre={$this->_tablename}2.nIdTitulo", 'left');
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
		// Borra los hijos
		$data = $this->get(null, null, null, null, 'nIdTituloPadre=' . $id);
		foreach($data as $r)
		{
			if (!$this->delete($r['nIdTitulo'])) return FALSE;
		}
		
		return parent::onBeforeDelete($id);
	}


}

/* End of file M_titulo.php */
/* Location: ./system/application/models/eoi/M_titulo.php */