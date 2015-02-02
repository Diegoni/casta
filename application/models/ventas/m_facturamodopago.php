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
 * @filesource
 */

/**
 * Modos de pago de factura
 *
 */
class M_facturamodopago extends MY_Model
{
	/**
	 * Costructor
	 * @return M_facturamodopago
	 */
	function __construct($tablename = null)
	{
		$data_model = array(
			'dFecha'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATETIME), 
			'nIdModoPago'		=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'ventas/modopago/search')), 
			'nIdCaja'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'ventas/caja/search')),
			'nIdFactura'		=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT), 
			'fImporte'			=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_FLOAT), 
			'nIdAbono'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT), 
			'bContabilizado'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOL), 
		);

		if (!isset($tablename)) $tablename = 'Doc_FacturasModosPago';
		 
		parent::__construct($tablename, 'nIdFacturaModoPago', 'dFecha DESC', 'nIdFacturaModoPago', $data_model, TRUE);
		//$this->_cache = TRUE;
	}
	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeSelect($id, $sort, $dir)
	 */
	protected function onBeforeSelect($id = null, &$sort = null, &$dir = null)
	{
		if (parent::onBeforeSelect($id, $sort, $dir))
		{
			$this->db->select('Gen_Cajas.cDescripcion cCaja, Gen_ModosPago.cDescripcion cModoPago')
			->select('Gen_Cajas.cCorto cCajaCorto, Gen_ModosPago.cDescripcionCorta cModoPagoCorto')
			->join('Gen_Cajas', "Gen_Cajas.nIdCaja = {$this->_tablename}.nIdCaja");
						$this->db->join('Gen_ModosPago', "Gen_ModosPago.nIdModoPago = {$this->_tablename}.nIdModoPago");
			return TRUE;
		}
		return FALSE;
	}
}

/* End of file M_facturamodopago.php */
/* Location: ./system/application/models/ventas/M_facturamodopago.php */