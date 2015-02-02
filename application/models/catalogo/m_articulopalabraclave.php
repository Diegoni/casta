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
 * Palabras clave de un artículo
 *
 */
class M_Articulopalabraclave extends MY_Model
{
	/**
	 * Constructor
	 * @return M_Articulopalabraclave
	 */
	function __construct()
	{
		$obj = get_instance();
		$data_model = array(
			'nIdPalabraClave'	=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'catalogo/palabraclave/search', 'cPalabraClave')),
			'nIdLibro'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'catalogo/articulo/search')),		
		);

		parent::__construct('Cat_PalabrasClave_Libros', 'nIdPalabraClaveLibro', 'nIdPalabraClaveLibro', 'nIdPalabraClaveLibro', $data_model, TRUE);
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeSelect($id, $sort, $dir, $where)
	 */
	protected function onBeforeSelect($id = null, &$sort = null, &$dir = null, $where = null)
	{
		if (parent::onBeforeSelect($id, $sort, $dir, $where))
		{
			$this->db->select("Cat_PalabrasClave.cPalabraClave");
			$this->db->join('Cat_PalabrasClave', 'Cat_PalabrasClave.nIdPalabraClave = Cat_PalabrasClave_Libros.nIdPalabraClave');
			return TRUE;
		}
		return FALSE;
	}
}

/* End of file M_Articulopalabraclave.php */
/* Location: ./system/application/models/catalogo/M_Articulopalabraclave.php */
