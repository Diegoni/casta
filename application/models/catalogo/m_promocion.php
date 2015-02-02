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
 * Promociones
 *
 */
class M_promocion extends MY_Model
{
	/**
	 * Costructor 
	 * @return M_promocion
	 */
	function __construct()
	{
		$data_model = array(
			'cDescripcion'		=> array(), 
			'nIdTipoPromocion'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'catalogo/tipopromocion/search')),
			'nIdLibro'			=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'catalogo/articulo/search', 'cTitulo')),
			'dInicio' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATETIME),
			'dFinal' 			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATETIME),
		);

		parent::__construct('Cat_Promociones', 'nIdPromocion', 'dInicio DESC', 'cDescripcion', $data_model, TRUE);	
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
			$this->db->select('Cat_Fondo.cTitulo');
			$this->db->join('Cat_Fondo', 'Cat_Fondo.nIdLibro = Cat_Promociones.nIdLibro');
			$this->db->select('Cat_TiposPromocion.cDescripcion cTipoPromocion');
			$this->db->join('Cat_TiposPromocion', 'Cat_TiposPromocion.nIdTipoPromocion = Cat_Promociones.nIdTipoPromocion');
			return TRUE;
		}
		return FALSE;
	}
}

/* End of file M_promocion.php */
/* Location: ./system/application/models/catalogo/M_promocion.php */