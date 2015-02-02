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
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Ubicaciones de un artículo
 *
 */
class M_Articuloubicacion extends MY_Model
{
	/**
	 * Constructor
	 * @return M_Articuloubicacion
	 */
	function __construct()
	{
		$data_model = array(
			'nIdUbicacion'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'catalogo/ubicacion/search')),
			'nIdLibro'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'catalogo/articulo/search')),		
		);

		parent::__construct('Cat_Ubicaciones_Libros', 'nIdUbicacionLibro', 'nIdUbicacionLibro', 'nIdUbicacionLibro', $data_model, TRUE);
	}
		
	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeSelect($id, $sort, $dir, $where)
	 */
	protected function onBeforeSelect($id = null, &$sort = null, &$dir = null, &$where = null)
	{
		if (parent::onBeforeSelect($id, $sort, $dir, $where))
		{
			$this->db->select('m.cDescripcion');
			$this->db->join('Cat_Ubicaciones m', 'm.nIdUbicacion = Cat_Ubicaciones_Libros.nIdUbicacion');
			return TRUE;
		}
		return FALSE;
	}
}

/* End of file M_Articuloubicacion.php */
/* Location: ./system/application/models/catalogo/M_Articuloubicacion.php */
