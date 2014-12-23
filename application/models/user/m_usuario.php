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
 * Usuarios
 *
 */
class M_Usuario extends MY_Model
{
	/**
	 * Constructor
	 *
	 * @return M_Usuario
	 */
	function __construct()
	{
		echo "test";
		$data_model = array(
			'cUsername'		=> array(DATA_MODEL_REQUIRED => TRUE),
			'cNombre'		=> array(DATA_MODEL_DEFAULT => TRUE),
			'cPassword'		=> array(),
			'dLastlogin'	=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_DATETIME),
			'bEnabled'		=> array(DATA_MODEL_TYPE => DATA_MODEL_TYPE_BOOLEAN)
		);

		$this->_relations['grupos'] = array (
				'table' => 'Usr_UsuariosGrupos',
				'ref'	=> 'user/m_grupo',
				'fk'	=> 'nIdGrupo');

		$this->_relations['permisos'] = array (
				'table' => 'Usr_PermisosUsuario',
				'ref'	=> 'user/m_permiso',
				'fk'	=> 'nIdPermiso');

		parent::__construct('Usr_Usuarios', 'nIdUsuario', 'cUsername', array('cUsername', 'cNombre'), $data_model);
		$this->_cache = TRUE;
		
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeInsert($data)
	 */
	protected function onBeforeInsert(&$data)
	{
		if (parent::onBeforeInsert($data))
		{
			// Encripta password
			if (isset($data['cPassword']))
			{
				$data['cPassword'] = sha1($data['cPassword']);
			}
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Model#onBeforeUpdate($id, $data)
	 */
	protected function onBeforeUpdate($id, &$data)
	{
		if (parent::onBeforeUpdate($id, $data))
		{
			// Encripta password
			if (isset($data['cPassword']))
			{
				$data['cPassword'] = sha1($data['cPassword']);
			}
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * test if valid user
	 *
	 * @param string $username
	 * @param string $password
	 * @param string $pwdclean
	 * @return bool
	 */
	function check_login($username, $password, $pwdclean = null)
	{
		// Se usa autentificación por tabla
		$data = $this->get(null, null, null, null, array('cUsername' => $username, 'cPassword' => $password), 'nIdUsuario');
		if (count($data) > 0)
		{
			return $data[0]['nIdUsuario'];
		}
		return null;
	}
	
	/**
	 * Actualiza último logueo
	 * @param int $id Id del usuario
	 */
	function stamp_login($id)
	{
		$this->update($id, array('dLastlogin' => time()));
	}

	/**
	 * Obtiene las autorizaciónes del usuario
	 * @param int $id Id del usuario
	 * @return array
	 */
	function get_auth($id)
	{
		// Permisos de grupo
		$this->db->flush_cache();
		$permisos = array();
		$this->db->select('Usr_Permisos.cDescripcion text')
		->from('Usr_PermisosGrupo')
		->join('Usr_Permisos', 'Usr_PermisosGrupo.nIdPermiso = Usr_Permisos.nIdPermiso')
		->join('Usr_UsuariosGrupos', 'Usr_UsuariosGrupos.nIdGrupo = Usr_PermisosGrupo.nIdGrupo')
		->where('Usr_UsuariosGrupos.nIdUsuario', (int) $id)
		->group_by('Usr_Permisos.cDescripcion');
		$data = $this->_get_results($this->db->get());
		foreach ($data as $d)
		{
			$permisos[$d['text']] = TRUE;
		}

		// Permisos de usuario
		$this->db->flush_cache();
		$this->db->select('Usr_Permisos.cDescripcion text, Usr_PermisosUsuario.nIdPermiso bYes')
		->from('Usr_Permisos')
		->join('Usr_PermisosUsuario', 'Usr_PermisosUsuario.nIdPermiso = Usr_Permisos.nIdPermiso', 'left')
		->where('Usr_PermisosUsuario.nIdUsuario', (int) $id);
		$data = $this->_get_results($this->db->get());
		foreach ($data as $d)
		{
			if (isset($d['bYes']))
			{
				$permisos[$d['text']] = TRUE;
			}
			else
			{
				if (!isset($permisos[$d['text']]))
				{
					$permisos[$d['text']] = FALSE;
				}
			}
		}
		uksort($permisos, 'strcmp');
		return $permisos;
	}
}

/* End of file M_user.php */
/* Location: ./system/application/models/M_user.php */
