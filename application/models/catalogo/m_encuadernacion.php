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
 * Encuadernaciones de libro
 *
 */
class M_encuadernacion extends MY_Model
{
	/**
	 * Costructor 
	 * @return M_estadolibro
	 */
	function __construct()
	{
		$data_model = array(
			'cDescripcion'	=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE), 
		);

		parent::__construct('Cat_Encuadernaciones', 'nIdEncuadernacion', 'cDescripcion', 'cDescripcion', $data_model);	
		$this->_cache = TRUE;
	}
}

/* End of file M_encuadernacion.php */
/* Location: ./system/application/models/catalogo/M_encuadernacion.php */