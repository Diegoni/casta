<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	catalogo
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Autores de un artículo
 *
 */
class M_Articulomateria extends MY_Model
{
	/**
	 * Constructor
	 * @return M_Articulomateria
	 */
	function __construct()
	{
		$obj = get_instance();
		$data_model = array(
			'nIdMateria'	=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'catalogo/materia/search', 'cNombre')),
			'nIdLibro'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'catalogo/articulo/search')),		
			'bAutomatico'	=> array(DATA_MODEL_READONLY => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),		
		);

		parent::__construct('Cat_Libros_Materias', 'nIdLibroMateria', 'nIdLibroMateria', 'nIdLibroMateria', $data_model);
		$this->_deleted = TRUE;
	}
		
	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeSelect($id, $sort, $dir, $where)
	 */
	protected function onBeforeSelect($id = null, &$sort = null, &$dir = null, &$where = null)
	{
		if (parent::onBeforeSelect($id, $sort, $dir, $where))
		{
			$this->db->select('Cat_Materias.cNombre, Cat_Materias.cCodMateria');
			$this->db->join('Cat_Materias', 'Cat_Materias.nIdMateria = Cat_Libros_Materias.nIdMateria');
			return TRUE;
		}
		return FALSE;
	}
}

/* End of file M_Articulomateria.php */
/* Location: ./system/application/models/catalogo/M_Articulomateria.php */
