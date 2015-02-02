<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	ventas
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Tarifas de envío gramos
 *
 */
class M_Tarifasenviogramos extends MY_Model
{
	/**
	 * Constructor
	 *
	 * @return M_Tarifasenviogramos
	 */
	function __construct()
	{
		$data_model = array(
			'nIdZona'	=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'perfiles/zona/search')),
			'nPeso'		=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),		
			'fPrecio'	=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT)		
		);
		
		parent::__construct('Ext_TarifasEnvioGramos', 'nIdPrecio', 'nIdPrecio', 'nIdZona,nPeso', $data_model);
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
			$this->db->select('Web_Zonas.cNombre + \': \' + Web_Zonas.cDescripcion cZona');
			$this->db->join('Web_Zonas', "{$this->_tablename}.nIdZona = Web_Zonas.nIdZona");
			return TRUE;
		}
		return FALSE;
	}
}

/* End of file M_tarifasenviogramos.php */
/* Location: ./system/application/models/ventas/M_tarifasenviogramos.php */