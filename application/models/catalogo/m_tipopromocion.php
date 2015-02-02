<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	catalogo
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Tipos de promocion
 *
 */
class M_tipopromocion extends MY_Model
{
	/**
	 * Costructor 
	 * @return M_tipopromocion
	 */
	function __construct()
	{
		$data_model = array(
			'cDescripcion'	=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE), 
		);

		parent::__construct('Cat_TiposPromocion', 'nIdTipoPromocion', 'cDescripcion', 'cDescripcion', $data_model, TRUE);	
		$this->_cache = TRUE;
	}
}

/* End of file M_tipopromocion.php */
/* Location: ./system/application/models/catalogo/M_tipopromocion.php */