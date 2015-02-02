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
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Grupos para mensajes del sistema y usuarios
 *
 */
class M_MensajeGrupoUsuario extends MY_Model
{
	/**
	 * Constructoir
	 * @return unknown_type
	 */
	function __construct()
	{
		$data_model = array(
			'nIdGrupo'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'sys/mensajegrupo/search')), 
			'nIdUsuario'	=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT)
		);

		parent::__construct('Ext_MensajesGruposUsuarios', 'nIdMensajeGrupoUsuario', 'nIdMensajeGrupoUsuario', 'nIdMensajeGrupoUsuario', $data_model);
		$this->_cache = TRUE;
	}
}

/* End of file M_MensajeGrupoUsuario.php */
/* Location: ./system/application/models/sys/M_MensajeGrupoUsuario.php */
