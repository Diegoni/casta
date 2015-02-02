<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	generico
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Registros eliminados
 *
 */
class M_deleted extends MY_Model
{
	/**
	 * Constructor
	 *
	 * @return M_Nota
	 */
	function __construct()
	{
		$data_model = array(
			'nIdRegistro'	=> array(DATA_MODEL_REQUIRED => TRUE),
			'cTabla'		=> array(DATA_MODEL_REQUIRED => TRUE), 
		);
		
		parent::__construct('Gen_Deleted', 'nIdDeleted', 'dCreacion DESC', 'cTabla', $data_model, true);
		$this->_cache = TRUE;
	}
}

/* End of file M_deleted.php */
/* Location: ./system/application/models/generico/M_deleted.php */