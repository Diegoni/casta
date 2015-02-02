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
 * EOI - Cursos de venta por internet
 *
 */
class M_curso extends MY_Model
{
	/**
	 * Constructor
	 *
	 * @return M_curso
	 */
	function __construct()
	{
		$data_model = array(
			'nIdEOI'		=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'eoi/escuela/search', 'cEscuela')), 			
			'cDescripcion'	=> array(DATA_MODEL_REQUIRED => TRUE),
			'dDesde'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATE),
			'dHasta'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATE),
			'bMostrarWeb' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN, DATA_MODEL_DEFAULT_VALUE => FALSE),
		);

		parent::__construct('Ext_EOISCursos', 'nIdCurso', 'dDesde DESC', 'cDescripcion', $data_model, TRUE);

		$this->_cache = TRUE;
	}

	/**
	 * Devuelve las escuelas activas con títulos
	 * @return array
	 */
	function get_escuelas()
	{
		$fecha = format_mssql_date(time());
		$this->db->flush_cache();
		$this->db->select('Ext_EOISCursos.nIdCurso, Ext_EOIS.cDescripcion cEscuela, Ext_EOISCursos.cDescripcion')
		->from('Ext_EOISCursos')
		->join('Ext_EOIS', 'Ext_EOISCursos.nIdEOI=Ext_EOIS.nIdEOI')
		->join('Ext_EOISTitulos', 'Ext_EOISTitulos.nIdCurso = Ext_EOISCursos.nIdCurso AND Ext_EOISTitulos.nTipo IN (3,4,5)')
		->where('Ext_EOISCursos.bMostrarWeb = 1')
		->where("(Ext_EOISCursos.dHasta >= {$fecha} OR Ext_EOISCursos.dHasta IS NULL)")
		->where("(Ext_EOISCursos.dDesde < " . $this->db->dateadd('d', 1, $fecha) . " OR Ext_EOISCursos.dDesde IS NULL)")
		->group_by('Ext_EOISCursos.nIdCurso, Ext_EOIS.cDescripcion, Ext_EOISCursos.cDescripcion')
		->order_by('Ext_EOIS.cDescripcion, Ext_EOISCursos.cDescripcion');
		$query = $this->db->get();
		$data = $this->_get_results($query);
		return $data;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeSelect($id, $sort, $dir, $where)
	 */
	protected function onBeforeSelect($id = null, &$sort = null, &$dir = null, &$where = null)
	{
		if (parent::onBeforeSelect($id, $sort, $dir, $where))
		{
			$this->db->select('Ext_EOIS.cDescripcion cEscuela');
			$this->db->join('Ext_EOIS', "{$this->_tablename}.nIdEOI=Ext_EOIS.nIdEOI");
			return TRUE;
		}
		return FALSE;
	}
}

/* End of file M_curso.php */
/* Location: ./system/application/models/eoi/M_curso.php */