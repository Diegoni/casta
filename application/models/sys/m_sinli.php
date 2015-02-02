<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	app
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Ficheros SINLI
 *
 */
class M_Sinli extends MY_Model
{
	/**
	 * Constructoir
	 * @return unknown_type
	 */
	function __construct()
	{
		$data_model = array(
			'cTipo'		=> array(DATA_MODEL_REQUIRED => TRUE),
			'cOrigen'	=> array(DATA_MODEL_REQUIRED => TRUE),
			'cAsunto'	=> array(DATA_MODEL_DEFAULT => TRUE),
			'dFecha' 	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATETIME),
			'cFichero'	=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_NO_LIST => TRUE),
			'nIdDocumento' => array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
			'bProcesado' => array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN, DATA_MODEL_DEFAULT_VALUE => 0),
		);

		parent::__construct('Ext_Sinli', 'nIdFichero', 'dFecha DESC', 'cAsunto', $data_model);
		#$this->_cache = TRUE;
	}
	
	/**
	 * Devuelve un listado de proveedores con SINLI
	 * @return array
	 */
	function proveedores($tipo = null)
	{
		$this->db->flush_cache();
		$this->db->select('nIdProveedor id, cNombre, cApellido, cEmpresa, cSINLI')
		->from('Prv_Proveedores')
		->order_by('cNombre, cApellido, cEmpresa');
		if ($tipo == 'ENVIO')
		{
			$this->db->where('cSINLI IN (SELECT DISTINCT cOrigen FROM Ext_Sinli WHERE cTipo=\'ENVIO\' AND nIdDocumento IS NULL)');
		}
		else
		{
			$this->db->where('cSINLI IN (SELECT DISTINCT cOrigen FROM Ext_Sinli)');
		}

		$query = $this->db->get();
		$data = $this->_get_results($query);
		return $data;		
	}
}

/* End of file M_sinli.php */
/* Location: ./system/application/models/sys/M_sinli.php */
