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
 * Mensajes del sistema
 *
 */
class M_Mensaje extends MY_Model
{
	/**
	 * Constructoir
	 * @return unknown_type
	 */
	function __construct()
	{
		$data_model = array(
			'cOrigen'		=> array(),
			'cDestino'		=> array(),
			'nIdGrupo'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT, DATA_MODEL_EDITOR => array(DATA_MODEL_EDITOR_COMBO, 'sys/mensajegrupo/search')), 
			'tMensaje'		=> array(DATA_MODEL_DEFAULT => TRUE, DATA_MODEL_REQUIRED => TRUE),
			'bVisto' 		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN, DATA_MODEL_DEFAULT_VALUE => 0),
		);

		parent::__construct('Ext_Mensajes', 'nIdMensaje', 'cOrigen', 'dCreacion', $data_model, TRUE);
		$this->_cache = TRUE;
	}

	/**
	 * Mensajes no leídos por el usuario
	 * @param string $username
	 * @param int $id Id del usuario
	 * @param int $last_id Último mensaje leído
	 * @return array
	 */
	function unread($username, $id, $last_id = -1)
	{
		if (isset($username))
		{
			$username= $this->db->escape((string)$username);
			$id = 
			$filter = "((bVisto = 0 OR bVisto IS NULL) AND 
					(cDestino = {$username} 
						OR (cDestino IS NULL AND nIdGrupo IS NULL)
						OR (nIdGrupo IS NOT NULL AND nIdGrupo IN (SELECT nIdGrupo FROM Ext_MensajesGruposUsuarios WHERE nIdUsuario={$id}))
						))";
			if ($last_id > 0)
				$filter .= " AND nIdMensaje > {$last_id} ";


			$data = $this->get(null, $this->config->item('bp.mensajes.limit'), 'dCreacion', 'ASC', $filter);
			return $data;
		}
		return null;
	}

	/**
	 * Mensajes no leídos por el usuario
	 * @param string $username
	 * @return array
	 */
	function vistos($id)
	{
		return $this->db->where_in('nIdMensaje', $id)
		->update($this->_tablename, array('bVisto' => TRUE));
	}

	/**
	 * @see system/application/libraries/MY_Model#onBeforeSelect($id, $sort, $dir, $where)
	 */
	protected function onBeforeSelect($id = null, &$sort = null, &$dir = null, &$where = null)
	{
		if (parent::onBeforeSelect($id, $sort, $dir, $where))
		{
			$this->db->select('Usr_Usuarios.cNombre cOrigenNombre')
			->select('us.cNombre cDestinoNombre')
			->select('Ext_MensajesGrupos.cDescripcion cGrupo')
			->join('Usr_Usuarios', "Usr_Usuarios.cUsername = {$this->_tablename}.cOrigen", 'left')
			->join('Usr_Usuarios us', "us.cUsername = {$this->_tablename}.cDestino", 'left')
			->join('Ext_MensajesGrupos', "Ext_MensajesGrupos.nIdGrupo = {$this->_tablename}.nIdGrupo", 'left');
			return TRUE;
		}
		return FALSE;
	}

}

/* End of file M_mensaje.php */
/* Location: ./system/application/models/sys/M_mensaje.php */
