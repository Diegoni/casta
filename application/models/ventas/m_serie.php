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
 * Series
 *
 */
class M_Serie extends MY_Model
{
	/**
	 * Constructor
	 *
	 * @return M_Serie
	 */
	function __construct()
	{
		$data_model = array(
			'cDescripcion'	=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE),
			'nNumero'		=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
			'nContador'		=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_DEFAULT_VALUE => 0),
			'nIdSatelite'	=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT , DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'generico/satelite/search')),		
			'dDesde'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATE),		
			'dHasta'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATE)		
		);
		
		parent::__construct('Doc_Series', 'nIdSerie', 'cDescripcion', 'cDescripcion', $data_model, true);
		$this->_cache = TRUE;
	}

	/**
	 * Asigna el último Id de factura
	 * @param int $id ID de la serie
	 */
	function set_last($id)
	{
		$sql = 'SELECT MAX(nNumero) nNumero FROM Doc_Facturas WHERE nIdSerie=' . $id;
		$query = $this->db->query($sql);
		$data = $this->_get_results($query);

		$sql = 'SELECT MAX(nNumero) nNumero FROM Doc_Facturas2 WHERE nIdSerie=' . $id;
		$query = $this->db->query($sql);
		$data2 = $this->_get_results($query);

		$sql = 'SELECT nContador FROM Doc_Series WHERE nIdSerie=' . $id;
		$query = $this->db->query($sql);
		$data3 = $this->_get_results($query);

		$num = max(isset($data[0]['nNumero'])?($data[0]['nNumero'] + 1):1, isset($data2[0]['nNumero'])?($data2[0]['nNumero'] + 1):1);
		$num = max($num, isset($data3[0]['nContador'])?($data3[0]['nContador'] + 1):1);
		#var_dump($data, $data2, $num);
		if (!$this->update($id, array('nContador' => $num))) 
			return FALSE;
		return $num; 
	}
}

/* End of file M_serie.php */
/* Location: ./system/application/models/ventas/M_serie.php */