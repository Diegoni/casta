<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	generico
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Notas en documentos
 *
 */
class M_Nota extends MY_Model
{
	/**
	 * Constructor
	 *
	 * @return M_Nota
	 */
	function __construct()
	{
		$data_model = array(
			'nIdRegistro'	=> array(DATA_MODEL_REQUIRED => TRUE),
			'nIdTipoObservacion'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'generico/tiponota/search')),
			#'cTipo'			=> array(), 
			'cTabla'		=> array(DATA_MODEL_REQUIRED => TRUE), 
			'tObservacion'	=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE), 
		);
		
		parent::__construct('Gen_Observaciones', 'nIdObservacion', 'dCreacion DESC', 'tObservacion', $data_model, true);
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
			$this->db->select('Gen_TiposObservacion.cDescripcion cTipoObservacion');
			$this->db->join('Gen_TiposObservacion', "Gen_TiposObservacion.nIdTipoObservacion = {$this->_tablename}.nIdTipoObservacion", 'left');
			return TRUE;
		}
		return FALSE;
	}
}

/* End of file M_nota.php */
/* Location: ./system/application/models/generico/M_nota.php */