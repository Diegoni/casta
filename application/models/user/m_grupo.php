<?php
/**
 * Bibliopola
 *
 * Gesti�n de librer�as
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	user
 * @author		Alejandro L�pez
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Grupos
 *
 */
class M_Grupo extends MY_Model
{
	/**
	 * Constructor
	 *
	 * @return M_Grupo
	 */
	function __construct()
	{
		$data_model = array(
			'cDescripcion'	=> array(DATA_MODEL_DEFAULT => TRUE),
		);
		
		parent::__construct('Usr_Grupos', 'nIdGrupo', 'cDescripcion', 'cDescripcion', $data_model);
		$this->_cache = TRUE;
	}
}

/* End of file M_Grupo.php */
/* Location: ./system/application/models/user/M_Grupo.php */
