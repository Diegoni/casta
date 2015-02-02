<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	mailing
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Libros en un boletín
 *
 */
class M_Boletinlibro extends MY_Model
{
	/**
	 * Constructor
	 * @return M_Boletinlibro
	 */
	function __construct()
	{
		$data_model = array(
			'nIdBoletin'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'mailing/boletin/search')),
			'nIdLibro'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'catalogo/articulo/search')),		
		);

		parent::__construct('Sus_Boletines_Libros', 'nIdBoletinLibro', 'nIdBoletinLibro', 'nIdBoletinLibro', $data_model, TRUE);
	}

	/**
	 * Añade libros por tema o por materia
	 * @param int $id Id del boletín
	 * @param int $tema Id del tema
	 * @param int $materia Id de la materia
	 * @param date $desde Fecha de edición mínima
	 * @param int $libros Número de libros
	 * @return int Número de artículos añadidos
	 */
	protected function _add_temamateria($id, $tema = null, $materia = null, $desde = null, $libros = null)
	{
		set_time_limit(0);

		$this->db->select("ml.nIdLibro");

		if ($tema)
		{
			$this->db->from('Sus_Temas_Materias tm');
			$this->db->join('Cat_Materias m', "tm.nIdMateria = m.nIdMateria AND tm.nIdTema = {$tema}");
		}
		else
		{
			$this->db->from('Cat_Materias m');
			$this->db->where("m.nIdMateria = {$materia}");
		}

		$this->db->join('Cat_Materias m2', "m2.cCodMateria LIKE m.cCodMateria + '.%' OR m2.cCodMateria = m.cCodMateria")
		->join('Cat_Libros_Materias ml', 'm2.nIdMateria = ml.nIdMateria')
		->join('Cat_Fondo f', 'ml.nIdLibro = f.nIdLibro')
		->where("ml.nIdLibro NOT IN (SELECT nIdLibro
				FROM Sus_Boletines_Libros
				WHERE nIdBoletin = {$id})")
		->group_by('ml.nIdLibro, f.dEdicion')
		->order_by("f.dEdicion DESC");
		
		if ($libros) 
			$this->db->limit($libros);


		if ($desde)
		{
			$desde = format_mssql_date($desde);
			$this->db->where("f.dEdicion >= {$desde}");
		}
		$query = $this->db->get();
		$data = $this->_get_results($query);

		foreach($data as $l)
		{
			$insert = array (
				'nIdBoletin'	=> $id,
				'nIdLibro'		=> $l['nIdLibro']);
			$this->insert($insert);
		}

		return count($data);
	}

	/**
	 * Añade libros por materia
	 * @param int $id Id del boletín
	 * @param int $materia Id de la materia
	 * @param date $desde Fecha de edición mínima
	 * @param int $libros Número de libros
	 * @return int Número de artículos añadidos
	 */
	function add_materia($id, $materia, $desde = null, $libros = null)
	{
		return $this->_add_temamateria($id, null, $materia, $desde, $libros);
	}

	/**
	 * Añade libros por tema
	 * @param int $id Id del boletín
	 * @param int $tema Id del tema
	 * @param date $desde Fecha de edición mínima
	 * @param int $libros Número de libros
	 * @return int Número de artículos añadidos
	 */
	function add_tema($id, $tema, $desde = null, $libros = null)
	{
		return $this->_add_temamateria($id, $tema, null, $desde, $libros);
	}

	/**
	 * Añade libros que tienen stock o están pendientes de llegar
	 * @param int $id Id del boletín
	 * @param int $seccion Id de la sección
	 * @param bool $pendientes TRUE: Añadir también los pendientes, FALSE: solo los que tienen stock
	 * @return int Número de artículos añadidos
	 */
	function add_stock($id, $seccion = null, $pendientes = null)
	{
		set_time_limit(0);

		$this->db->select('sl.nIdLibro')
		->from('Cat_Secciones_Libros sl')
		->where("sl.nIdSeccion = {$seccion}")
		->where("sl.nIdLibro NOT IN (SELECT nIdLibro
				FROM Sus_Boletines_Libros
				WHERE nIdBoletin = {$id})")
		->where('( ' . (($pendientes)?'nStockRecibir > 0 OR':'') . ' sl.nStockFirme > 0 OR sl.nStockDeposito > 0)');
		
		$query = $this->db->get();
		$data = $this->_get_results($query);

		foreach($data as $l)
		{
			$insert = array (
				'nIdBoletin'	=> $id,
				'nIdLibro'		=> $l['nIdLibro']);
			$this->insert($insert);
		}

		return count($data);
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeSelect($id, $sort, $dir, $where)
	 */
	protected function onBeforeSelect($id = null, &$sort = null, &$dir = null, &$where = null)
	{
		if (parent::onBeforeSelect($id, $sort, $dir, $where))
		{
			$this->db->select('l.cTitulo, l.cAutores');
			$this->db->join('Cat_Fondo l', 'l.nIdLibro = Sus_Boletines_Libros.nIdLibro');
			return TRUE;
		}
		return FALSE;
	}
}

/* End of file M_boletinlibro.php */
/* Location: ./system/application/models/mailing/M_boletinlibro.php */
