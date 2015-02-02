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
 * Tipos de suscripción de revista
 *
 */
class M_tiposuscripcionrevista extends MY_Model
{
	/**
	 * Costructor 
	 * @return M_tiposuscripcionrevista
	 */
	function __construct()
	{
		$data_model = array(
			'cDescripcion'	=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE), 
		);

		parent::__construct('Cat_TiposSuscripcionRevista', 'nIdTipoSuscripcion', 'cDescripcion', 'cDescripcion', $data_model);	
		$this->_cache = TRUE;
	}
}

/* End of file M_tiposuscripcionrevista.php */
/* Location: ./system/application/models/catalogo/M_tiposuscripcionrevista.php */