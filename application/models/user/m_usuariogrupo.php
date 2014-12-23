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
 * Usuarios de grupo
 *
 */
class M_usuariogrupo extends MY_Model
{
	/**
	 * Constructor
	 *
	 * @return M_usuariogrupo
	 */
	function __construct()
	{
		$data_model = array(
			'nIdGrupo'		=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
			'nIdUsuario'	=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT)
		);
		
		parent::__construct('Usr_UsuariosGrupos', 'nIdUsuarioGrupo', 'nIdUsario', 'nIdUsuario', $data_model);
		$this->_cache = TRUE;
	}
}

/* End of file M_usuariogrupo */
/* Location: ./system/application/models/user/M_usuariogrupo.php */