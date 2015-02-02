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
class M_informacionproveedor extends MY_Model
{
	/**
	 * Costructor 
	 * @return M_informacionproveedor
	 */
	function __construct()
	{
		$data_model = array(
			'cDescripcion'		=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE)
		);

		parent::__construct('Doc_InformacionProveedor', 'nIdInformacion', 'cDescripcion', 'cDescripcion', $data_model);	
		$this->_cache = TRUE;
	}
}

/* End of file M_informacionproveedor.php */
/* Location: ./system/application/models/compras/M_informacionproveedor.php */