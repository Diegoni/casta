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
 * Estados de factura 
 *
 */
class M_estadofactura extends MY_Model
{
	/**
	 * Costructor 
	 * @return M_estadofactura
	 */
	function __construct()
	{
		$data_model = array(
			'cDescripcion'		=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE), 
		);

		parent::__construct('Doc_EstadosFactura', 'nIdEstado', 'cDescripcion', 'cDescripcion', $data_model);	
		$this->_cache = TRUE;
	}
}

/* End of file M_estadofactura.php */
/* Location: ./system/application/models/ventas/M_estadofactura.php */