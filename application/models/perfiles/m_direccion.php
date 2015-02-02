<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	perfiles
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @filesource
 */

/**
 * Direcciones
 *
 */
class M_direccion extends MY_Model
{
	/**
	 * Costructor
	 * @return M_direccion
	 */
	function __construct()
	{
		$data_model = array(
			'cDescripcion'	=> array(DATA_MODEL_DEFAULT => TRUE), 
			'cTitular'		=> array(),	 
			'cCalle'		=> array(DATA_MODEL_REQUIRED => TRUE),
			'cCP' 			=> array(),
			'cPoblacion' 	=> array(),
			'nIdRegion' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'perfiles/region/search')),
			'nIdTipo'		=> array(DATA_MODEL_DEFAULT_VALUE => 1, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'perfiles/tipoperfil/search')), 
			'cRegionOtro' 	=> array(),
			'cPaisOtro' 	=> array(),
			'bBorrada' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN),
		);

		parent::__construct('Gen_Direcciones', 'nIdDireccion', 'cDescripcion', 'cDescripcion', $data_model);
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
			$this->db->select('r.nIdPais, r.cNombre cRegion, p.cNombre cPais');
			$this->db->join('Gen_Regiones r', 'Gen_Direcciones.nIdRegion = r.nIdRegion', 'left');
			$this->db->join('Gen_Paises p', 'r.nIdPais = p.nIdPais', 'left');
			return TRUE;
		}
		return FALSE;
	}
}


/* End of file M_direccion.php */
/* Location: ./system/application/models/perfiles/M_direccion.php */