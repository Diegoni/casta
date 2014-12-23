<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	libraries
 * @category	core
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Gestor de mensajes a usuarios
 * @author alexl
 *
 */
class Mensajes {

	/**
	 * Instancia de CI
	 * @var CI
	 */
	var $obj;

	/**
	 * Constructor
	 * @return Mensajes
	 */
	function __construct()
	{
		$this->obj =& get_instance();
		$this->obj->load->model('sys/m_mensaje');
		log_message('debug', 'Mensajes Class Initialised via '.get_class($this->obj));
	}

	/**
	 * Envía un mensaje a un usuario
	 * @param string $destino Usuario destino
	 * @param string $msg Mensaje a enviar
	 * @return int ID del mensaje enviado
	 */
	function usuario($destino, $msg)
	{
		$this->obj->load->library('Userauth');
		$data = array (
			'cDestino'	=> $destino,
			'cOrigen'	=> $this->obj->userauth->get_username(),
			'tMensaje'	=> $msg
		);
		return $this->obj->m_mensaje->insert($data);
	}

	/**
	 * Envía un mensaje a un grupo
	 * @param string $destino Grupo destino
	 * @param string $msg Mensaje a enviar
	 * @return int ID del mensaje enviado
	 */
	function grupo($destino, $msg)
	{
		$this->obj->load->library('Userauth');
		$data = array (
			'nIdGrupo'	=> $destino,
			'cOrigen'	=> $this->obj->userauth->get_username(),
			'tMensaje'	=> $msg
		);
		return $this->obj->m_mensaje->insert($data);
	}

	/**
	 * Envía un mensaje a un todos
	 * @param string $msg Mensaje del usuario
	 * @return int ID del mensaje enviado
	 */
	function todos($msg)
	{
		$this->obj->load->library('Userauth');
		$data = array (
			'cOrigen'	=> $this->obj->userauth->get_username(),
			'tMensaje'	=> $msg
		);
		return $this->obj->m_mensaje->insert($data);
	}
	
	/**
	 * Mensajes noleídos por el usuario logeado actualmente
	 * @param int $last_id Último mensaje leído
	 * @return array
	 */
	function unread($last_id = -1)
	{
		$this->obj->load->library('Userauth');
		$username = $this->obj->userauth->get_username();
		$id = $this->obj->userauth->get_id();
		$this->obj->load->library('Configurator');
		if ($last_id == -1)
		{
			$last_id = (int) $this->obj->configurator->user('bp.mensajes.last_id');
		}
		$data = (isset($username) && ($username != '')) ? $this->obj->m_mensaje->unread($username, $id, $last_id):null; 
		if (count($data) > 0)
		{
			$this->obj->configurator->set_user('bp.mensajes.last_id', (string) $data[0]['nIdMensaje']);
		}

		return $data;
	}
	
	/**
	 * Marca un mensaje como visto
	 * @param int $id Id del mensaje
	 * @return bool
	 */
	function visto($id)
	{
		$data['bVisto'] = 1;		
		$this->obj->m_mensaje->update($id, $data);
		return TRUE;
	}

	function get_last_error()
	{
		return $this->obj->m_mensaje->error_message();
	}
}

/* End of file Mensajes.php */
/* Location: ./system/libraries/Mensajes.php */