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
 * Iva
 *
 */
class M_modopagocaja extends MY_Model
{
	/**
	 * Constructor
	 *
	 * @return M_modopagocaja
	 */
	function __construct()
	{
		$data_model = array(
			'nIdModoPago'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'ventas/modopago/search', 'cModoPago')),
			'nIdCaja'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'ventas/caja/search', 'cCaja')),
			'nIdCuenta' 	=> array(DATA_MODEL_REQUIRED => TRUE),
		);
		
		parent::__construct('Ext_ModosPagoCaja', 'nIdModoPagoCaja', 'nIdModoPagoCaja', 'nIdModoPagoCaja', $data_model, TRUE);
		
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
			$this->db->select('Gen_ModosPago.cDescripcion cModoPago')
			->select('Gen_ModosPago.cDescripcionCorta cModoPagoCorto')
			->select('Gen_Cajas.cDescripcion cCaja')
			->select('Gen_Cajas.cCorto cCajaCorto')
			->join('Gen_ModosPago', "Gen_ModosPago.nIdModoPago = {$this->_tablename}.nIdModoPago")
			->join('Gen_Cajas', "Gen_Cajas.nIdCaja = {$this->_tablename}.nIdCaja");

			return TRUE;
		}
		return FALSE;
	}
}

/* End of file m_modopagocaja.php */
/* Location: ./system/application/models/ventas/m_modopagocaja.php */