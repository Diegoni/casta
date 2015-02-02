<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
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
class GrupoUsuario extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return GrupoUsuario
	 */
	function __construct()
	{
		parent::__construct('user.grupousuario', 'user/m_grupousuario', TRUE, null, 'Grupos Usuario');
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Controller#get_list($start, $limit, $sort, $dir, $where)
	 */
	function get_list($id = null)
	{
		$this->userauth->roleCheck(($this->auth .'.get_list'));

		$id	= isset($id)?$id:$this->input->get_post('id');

		if (isset($id) && ($id != ''))
		{
			$data = $this->reg->get_list($id);
			$res = array(
				'success' 		=> TRUE,
				'value_data' 	=> $data
			);
		}
		else
		{
			$res = array(
				'success' 		=> FALSE,
				'message' 		=> sprintf($this->lang->line('registro_no_encontrado'), $id)
			);
		}
		// Respuesta
		echo $this->out->send($res);
	}

	function get_list_usr($id = null)
	{
		$this->userauth->roleCheck(($this->auth .'.get_list'));

		$id	= isset($id)?$id:$this->input->get_post('id');

		if (isset($id) && ($id != ''))
		{
			$data = $this->reg->get_list_usr($id);
			$res = array(
				'success' 		=> TRUE,
				'value_data' 	=> $data
			);
		}
		else
		{
			$res = array(
				'success' 		=> FALSE,
				'message' 		=> sprintf($this->lang->line('registro_no_encontrado'), $id)
			);
		}
		// Respuesta
		echo $this->out->send($res);
	}

	/**
	 * (non-PHPdoc)
	 * @see system/application/libraries/MY_Controller#upd()
	 */
	function upd($id = null, $idgrupo = null, $idusuario = null, $value = null)
	{
		$this->userauth->roleCheck(($this->auth .'.upd'));

		$id			= isset($id)?$id:$this->input->get_post('id');
		$idgrupo	= isset($idgrupo)?$idgrupo:$this->input->get_post('nIdGrupo');
		$idusuario	= isset($idusuario)?$idusuario:$this->input->get_post('nIdUsuario');
		$value		= isset($value)?$value:$this->input->get_post('value');

		if (isset($idusuario) && ($idusuario != ''))
		{
			$res = $this->reg->add($idusuario, $id, $value);
		}
		else
		{
			$res = $this->reg->add($id, $idgrupo, $value);
		}

		// Respuesta
		if ($res === TRUE)
		{
			$success = TRUE;
			$message = sprintf($this->lang->line('registro_actualizado'), isset($idusuario)?$idusuario:$idusuario);
		}
		else
		{
			$success = FALSE;
			$message = $res;
		}
		echo $this->out->message($success, $message);
	}
}

/* End of file Grupousuario.php */
/* Location: ./system/application/controllers/user/Grupousuario.php */