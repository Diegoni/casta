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
 * Artículos relacionados
 *
 */
class M_relacionados extends MY_Model
{
	/**
	 * Constructor
	 * @return M_relacionados
	 */
	function __construct()
	{
		$obj = get_instance();
		$data_model = array(
				'nIdLibro1' => array(
						DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT,
						DATA_MODEL_REQUIRED => TRUE,
						DATA_MODEL_EDITOR => array(
								DATA_MODEL_EDITOR_COMBO,
								'catalogo/articulo/search'
						)
				),
				'nIdLibro2' => array(
						DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT,
						DATA_MODEL_REQUIRED => TRUE,
						DATA_MODEL_EDITOR => array(
								DATA_MODEL_EDITOR_COMBO,
								'catalogo/articulo/search'
						)
				),
		);

		parent::__construct('Cat_Relacionados', 'nIdRelacion', 'nIdRelacion', 'nIdRelacion', $data_model, TRUE);
		$this->_cache = TRUE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeSelect($id, $sort, $dir, $where)
	 */
	protected function onBeforeSelect($id = null, &$sort = null, &$dir = null, &$where = null)
	{
		if (parent::onBeforeSelect($id, $sort, $dir, $where))
		{
			$this->db->select('f1.cTitulo cTitulo1, f1.cAutores cAutores1, f1.cISBN cISBN1, e1.cNombre cEditorial1, f1.fPrecio fPrecio1, f1.nIdTipo nIdTipo1, t1.fIVA fIVA1');
			$this->db->join('Cat_Fondo f1', "f1.nIdLibro = {$this->_tablename}.nIdLibro1");
			$this->db->join('Cat_Tipos t1', "f1.nIdTipo = t1.nIdTipo");
			$this->db->join('Cat_Editoriales e1', 'f1.nIdEditorial = e1.nIdEditorial', 'left');
			
			$this->db->select('f2.cTitulo cTitulo2, f2.cAutores cAutores2, f2.cISBN cISBN2, e2.cNombre cEditorial2, f2.fPrecio fPrecio2, f2.nIdTipo nIdTipo2, t2.fIVA fIVA2');
			$this->db->join('Cat_Fondo f2', "f2.nIdLibro = {$this->_tablename}.nIdLibro2");
			$this->db->join('Cat_Tipos t2', "f2.nIdTipo = t2.nIdTipo");
			$this->db->join('Cat_Editoriales e2', 'f2.nIdEditorial = e2.nIdEditorial', 'left');

			return TRUE;
		}
		return FALSE;
	}
}

/* End of file M_relacionados.php */
/* Location: ./system/application/models/catalogo/M_relacionados.php */
