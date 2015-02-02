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
 * Ubicaciones temporales para contar el Stock
 *
 */
class M_ubucaciontemporal extends MY_Model
{
	/**
	 * Costructor
	 * @return M_ubucaciontemporal
	 */
	function __construct()
	{
		$data_model = array(
			'nIdUbicacion'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'catalogo/ubicacion/search', 'cDescripcion')),		
			'nIdLibro'		=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_SEARCH, 'catalogo/articulo/search', 'cTitulo')),		
		);

		parent::__construct('Tmp_Ubicaciones', 'nIdLibroUbicacion', 'nIdLibroUbicacion', 'nIdLibroUbicacion', $data_model);
	}	
}

/* End of file M_ubucaciontemporal.php */
/* Location: ./system/application/models/stocks/M_ubucaciontemporal.php */