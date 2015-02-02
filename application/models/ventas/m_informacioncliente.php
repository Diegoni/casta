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
 * Información sobre la línea de pedido de cliente. 
 * nIdTipo = 1 -> Cancelados
 * nIdTipo = 2 -> Esperando 
 *
 */
class M_informacioncliente extends MY_Model
{
	/**
	 * Costructor 
	 * @return M_informacioncliente
	 */
	function __construct()
	{
		$data_model = array(
			'cDescripcion'	=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE),
			'nIdTipo'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE)
		);

		parent::__construct('Doc_InformacionCliente', 'nIdInformacion', 'cDescripcion', 'cDescripcion', $data_model);	
		$this->_cache = TRUE;
	}
}

/* End of file M_informacioncliente.php */
/* Location: ./system/application/models/compras/M_informacioncliente.php */