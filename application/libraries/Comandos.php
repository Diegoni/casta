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
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Gestor de comandos a usuarios
 * @author alexl
 *
 */
class Comandos {

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
		$this->obj->load->model('sys/m_comando');
		log_message('debug', 'Comandos Class Initialised via '.get_class($this->obj));
	}

	/**
	 * Envía un comando al usuario
	 * @param string $destino Usuario destino
	 * @param string $cmd comando a enviar
	 * @param int @tarea Tarea vinculada al comando
	 * @return int ID del comando enviado
	 */
	function add($destino, $cmd, $tarea = null)
	{
		$this->obj->load->library('Userauth');
		$data = array (
			'cDestino'	=> $destino,
			'nIdTarea'	=> $tarea,
			'cOrigen'	=> $this->obj->userauth->get_username(),
			'tComando'	=> $cmd
		);
		return $this->obj->m_comando->insert($data);
	}
	/**
	 * Mensajes noleídos por el usuario logeado actualmente
	 * @return array
	 */
	function unexec()
	{
		$this->obj->load->library('Userauth');
		$username = $this->obj->userauth->get_username();
		return (isset($username) && ($username != '')) ? $this->obj->m_comando->unexec($username):null;
	}

	/**
	 * Marca un comando como ejecutado
	 * @param int $id Id del comando
	 * @return bool
	 */
	function ejecutado($id)
	{
		$data['bEjecutado'] = 1;
		$this->obj->m_comando->update($id, $data);
		return TRUE;
	}

	/**
	 * Devuelve la lista de comandos
	 * @param int $limit Número de elementos a mostrar
	 *
	 */
	function get_list($limit = 20)
	{
		return $this->obj->m_comando->get(0, $limit, 'nIdComando', 'DESC');
	}

	/**
	 * Devuelve los datos del comando indicado
	 * @param int $id Id del comando
	 */
	function get($id)
	{
		return $this->obj->m_comando->load($id);
	}

}

/* End of file Comandos.php */
/* Location: ./system/libraries/Comandos.php */