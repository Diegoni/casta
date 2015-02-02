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
 * Tipos de stock
 *
 */
class M_tipostock extends MY_Model
{
	/**
	 * Costructor 
	 * @return M_tipostock
	 */
	function __construct()
	{
		$data_model = array(
			'cDescripcion'		=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE), 
		);

		parent::__construct('Cat_TiposStock', 'nIdTipoStock', 'cDescripcion', 'cDescripcion', $data_model);	
	}
}

/* End of file M_tipostock.php */
/* Location: ./system/application/models/stocks/M_tipostock.php */