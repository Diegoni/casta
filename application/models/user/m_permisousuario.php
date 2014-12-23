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
 * Permisos de Usuario
 *
 */
class M_permisousuario extends MY_Model
{
	/**
	 * Constructor
	 *
	 * @return M_permisousuario
	 */
	function __construct()
	{
		$data_model = array(
			'nIdPermiso'	=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT),
			'nIdUsuario'	=> array(DATA_MODEL_REQUIRED => TRUE, DATA_MODEL_TYPE => DATA_MODEL_TYPE_INT)
		);
		
		parent::__construct('Usr_PermisosUsuario', 'nIdPermisoUsuario', 'nIdPermiso', 'nIdPermiso', $data_model);
		$this->_cache = TRUE;
	}
	
	/**
	 * Obtiene el listado de temas de un contacto
	 * @param int $id Id contacto
	 * @return array
	 */
	function get_list($id)
	{
		$this->db->select('d2.nIdPermisoUsuario id, d.nIdPermiso, d.cDescripcion')
		->from('Usr_Permisos d')
		->join("{$this->_tablename} d2", "d.nIdPermiso = d2.nIdPermiso AND d2.nIdUsuario = {$id}", 'left');

		$r = $this->db->get();
		$temas = $this->_get_results($r);

		return $temas;
	}

	/**
	 * Añade/elimina un permiso a un usuario
	 * @param int $id ID del usuario
	 * @param int $idpermiso ID del permiso
	 * @param string $value Añadir o quitar
	 * @return unknown_type
	 */
	function add($id, $idpermiso, $value)
	{
		$this->db->trans_begin();
		// Borra el anterior
		$this->db->where("nIdUsuario = {$id} AND nIdPermiso = {$idpermiso}");
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
					'nIdPermiso'	=> $idpermiso
				);
				$this->insert($datos);
			}
		}
		$this->db->trans_commit();
		return TRUE;
	}
}

/* End of file M_permisousuario */
/* Location: ./system/application/models/user/M_permisousuario.php */