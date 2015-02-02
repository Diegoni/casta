<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	app
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Registro de traspasos
 *
 */
class M_bp2lc extends MY_Model
{
	/**
	 * Constructor
	 * @return m_bp2lc
	 */
	function __construct()
	{
		$data_model = array(
			'tDatos'		=> array(DATA_MODEL_NO_LIST => TRUE, DATA_MODEL_NO_GRID => TRUE),
			#'bSuccess' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN, DATA_MODEL_DEFAULT_VALUE => FALSE),
			'bTraspasado'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN, DATA_MODEL_DEFAULT_VALUE => FALSE),
			'cDescripcion'	=> array(DATA_MODEL_DEFAULT => TRUE),
			'cFichero'		=> array(),
		);

		parent::__construct('Ext_BP2LC', 'nIdBP2LC', 'dCreacion DESC', 'dCreacion', $data_model, TRUE);
		$this->_cache = TRUE;
	}
}

/* End of file M_bp2lc.php */
/* Location: ./system/application/models/sys/M_bp2lc.php */
