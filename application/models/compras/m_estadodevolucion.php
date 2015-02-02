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
 * Estados de una devolución
 *
 */
class M_estadodevolucion extends MY_Model
{
	/**
	 * Costructor 
	 * @return M_estadolibro
	 */
	function __construct()
	{
		$data_model = array(
			'cDescripcion'		=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE), 
			'bProtegido'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN)
		);

		parent::__construct('Doc_EstadosDevolucion', 'nIdEstado', 'cDescripcion', 'cDescripcion', $data_model);	
		$this->_cache = TRUE;
	}
}

/* End of file M_estadodevolucion.php */
/* Location: ./system/application/models/compras/M_estadodevolucion.php */