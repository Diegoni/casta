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
 * Lugares de entrea de un curso
 *
 */
class M_entrega extends MY_Model
{
	/**
	 * Constructor
	 *
	 * @return M_tentrega
	 */
	function __construct()
	{
		$data_model = array(
			'nIdCurso'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'eoi/curso/search', 'cCurso')),
			'cDescripcion'	=> array(),
		);

		parent::__construct('Ext_EOISEntregas', 'nIdEntrega', 'cDescripcion', 'cDescripcion', $data_model, TRUE);
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
			$this->db->select('Ext_EOISCursos.cDescripcion cCurso');
			$this->db->join('Ext_EOISCursos', "{$this->_tablename}.nIdCurso=Ext_EOISCursos.nIdCurso");
			$this->db->select('Ext_EOIS.cDescripcion cEscuela');
			$this->db->join('Ext_EOIS', "Ext_EOISCursos.nIdEOI=Ext_EOIS.nIdEOI");
			return TRUE;
		}
		return FALSE;
	}
}

/* End of file M_entrega.php */
/* Location: ./system/application/models/eoi/M_entrega.php */