<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	user
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Permisos
 *
 */
class M_Permiso extends MY_Model
{
	/**
	 * Constructor
	 *
	 * @return M_Group
	 */
	function __construct()
	{
		$data_model = array(
			'cDescripcion'	=> array(DATA_MODEL_DEFAULT => TRUE),
		); 
		
		parent::__construct('Usr_Permisos', 'nIdPermiso', 'cDescripcion', 'cDescripcion', $data_model);
		$this->_cache = TRUE;
	}
}

/* End of file M_permiso.php */
/* Location: ./system/application/models/user/M_permiso.php */
