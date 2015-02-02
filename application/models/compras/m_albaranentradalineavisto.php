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
 * Marca la línea como vista o no vista para concurso
 *
 */
class M_albaranentradalineavisto extends MY_Model
{
	/**
	 * Costructor
	 * @return M_aalbaranentradalineavisto
	 */
	function __construct()
	{
		$data_model = array(
			'nIdAlbaran'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE),
			'nIdLibro'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_REQUIRED => TRUE),
		);
		 
		parent::__construct('Doc_AlbaranEntradaLineaVisto', 'nIdLineaVista', 'nIdLineaVista', 'nIdLineaVista', $data_model, TRUE);
	}
}

/* End of file M_albaranentradalineavisto.php */
/* Location: ./system/application/models/ventas/M_albaranentradalineavisto.php */