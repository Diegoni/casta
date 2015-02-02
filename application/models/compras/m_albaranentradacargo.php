<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	compras
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Cargos en albaranes de entrada
 *
 */
class M_albaranentradacargo extends MY_Model
{
	/**
	 * Costructor
	 * @return M_albaranentradacargo
	 */
	function __construct()
	{
		$data_model = array(
			'nIdAlbaran'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'compras/albaranentrada/search')),
			'nIdTipoCargo'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'compras/tipocargo/search', 'cTipoCargo')),
			'fImporte'		=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_MONEY), 
		);
		 
		parent::__construct('Albaranes_TiposCargo', 'nIdCargo', 'nIdCargo', 'nIdCargo', $data_model, TRUE);
		$this->_cache = TRUE;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeSelect($id, $sort, $dir)
	 */
	protected function onBeforeSelect($id = null, &$sort = null, &$dir = null)
	{
		if (parent::onBeforeSelect($id, $sort, $dir))
		{
			$this->db->select('TiposCargo.cDescripcion cTipoCargo');
			$this->db->join('TiposCargo', "TiposCargo.nIdTipoCargo = {$this->_tablename}.nIdTipoCargo");
			return TRUE;
		}
		return FALSE;
	}	
}

/* End of file M_albaranentradacargo.php */
/* Location: ./system/application/models/ventas/M_albaranentradacargo.php */