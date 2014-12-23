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
 * Grupos de Usuario
 *
 */
class M_grupousuario extends MY_Model
{
	/**
	 * Constructor
	 *
	 * @return M_grupousuario
	 */
	function __construct()
	{
		$data_model = array(
			'nIdGrupo'	=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
			'nIdUsuario'	=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT)
		);

		parent::__construct('Usr_UsuariosGrupos', 'nIdUsuarioGrupo', 'nIdGrupo', 'nIdGrupo', $data_model);
		$this->_cache = TRUE;
	}

	/**
	 * Obtiene el listado de temas de un contacto
	 * @param int $id Id contacto
	 * @return array
	 */
	function get_list($id)
	{
		$this->db->select('d2.nIdUsuarioGrupo id, d.nIdGrupo, d.cDescripcion')
		->from('Usr_Grupos d')
		->join("{$this->_tablename} d2", "d.nIdGrupo = d2.nIdGrupo AND d2.nIdUsuario = {$id}", 'left');

		$r = $this->db->get();
		$temas = $this->_get_results($r);

		return $temas;
	}

	function get_list_usr($id)
	{
		$this->db->select('d2.nIdUsuarioGrupo id, d.nIdUsuario, d.cUsername')
		->from('Usr_Usuarios d')
		->join("{$this->_tablename} d2", "d.nIdUsuario = d2.nIdUsuario AND d2.nIdGrupo = {$id}", 'left');

		$r = $this->db->get();
		$temas = $this->_get_results($r);

		return $temas;
	}

	/**
	 * Añade/elimina un grupo a un usuario
	 * @param int $id ID del usuario
	 * @param int $idgrupo ID del grupo
	 * @param string $value Añadir o quitar
	 * @return unknown_type
	 */
	function add($id, $idgrupo, $value)
	{
		$this->db->trans_begin();
		// Borra el anterior
		$this->db->where("nIdUsuario = {$id} AND nIdGrupo = {$idgrupo}");
		$this->db->delete($this->_tablename);

		// Añade el nuevo
		$value = $this->_tobool($value);
		if ($value === FALSE)
		{
			$this->db->trans_rollback();
			return FALSE;
		}
		else
		{
			if ($value == 1)
			{
				$datos = array (
					'nIdUsuario' 	=> $id, 
					'nIdGrupo'		=> $idgrupo
				);
				$this->insert($datos);
			}
		}
		$this->db->trans_commit();
		return TRUE;
	}
}

/* End of file M_grupousuario */
/* Location: ./system/application/models/user/M_grupousuario.php */