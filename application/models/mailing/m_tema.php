<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	mailing
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Temas de mailing
 *
 */
class M_tema extends MY_Model
{
	/**
	 * Costructor
	 * @return M_tema
	 */
	function __construct()
	{
		$data_model = array(
			'cDescripcion'		=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE), 
		);

		parent::__construct('Sus_Temas', 'nIdTema', 'cDescripcion', 'cDescripcion', $data_model);
		$this->_cache = TRUE;
	}
}

/* End of file M_tema.php */
/* Location: ./system/application/models/mailing/M_tema.php */