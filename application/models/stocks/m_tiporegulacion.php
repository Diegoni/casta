<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	stocks
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Tipos de regulación
 *
 */
class M_tiporegulacion extends MY_Model
{
	/**
	 * Costructor 
	 * @return M_tiporegulacion
	 */
	function __construct()
	{
		$data_model = array(
			'cDescripcion'		=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE), 
			'bSigno'			=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN, DATA_MODEL_DEFAULT => FALSE),
		);

		parent::__construct('Gen_Movimiento_Stock', 'nId', 'cDescripcion', 'cDescripcion', $data_model);	
	}
}

/* End of file M_tiporegulacion.php */
/* Location: ./system/application/models/stocks/M_tiporegulacion.php */