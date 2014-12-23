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
 * Cola de mensajes
 * @author alexl
 *
 */
class Messages {

	/**
	 * Instancia de CI
	 * @var CI
	 */
	private $obj;

	/**
	 * Mensajes
	 * @var array
	 */
	private $mesages;

	/**
	 * Constructor
	 * @return Messages
	 */
	function __construct()
	{
		$this->obj =& get_instance();
		log_message('debug', 'Message Class Initialised via '.get_class($this->obj));
		$this->messages = array();
	}

	/**
	 * Añade un mensaje a la lista de mensajes
	 * @param string $message Texto del mensaje
	 * @param string $type Tipo de mensaje
	 * @param int $level Nivel de profundidad
	 */
	protected function _add($message, $type, $level)
	{
		$this->messages[] = array(
			'type' 		=> $type , 
			'message' 	=> $message,
			'level'		=> $level
		);
	}

	/**
	 * Añade un mensaje de información
	 * @param strint $message Texto del mensaje
	 * @param int $level Nivel de profundidad
	 */
	function info($message, $level = 0)
	{
		$this->_add($message, 'info', $level);

	}

	/**
	 * Añade un mensaje de error
	 * @param strint $message Texto del mensaje
	 * @param int $level Nivel de profundidad
	 */
	function error($message, $level = 0)
	{
		$this->_add($message, 'error', $level);

	}

	/**
	 * Añade un mensaje de warning
	 * @param strint $message Texto del mensaje
	 * @param int $level Nivel de profundidad
	 */
	function warning($message, $level = 0)
	{
		$this->_add($message, 'warning', $level);
	}

	/**
	 * Devuelve todos los mensajes en un array
	 */
	function get()
	{
		return $this->messages;
	}
	
	/**
	 * Elimina los mensajes
	 */
	function clear()
	{
		$this->messages = array();
	}
	
	/**
	 * Devuelve todos los mensajes formateados
	 * @param string $title Título a añadir la listado de mensajes
	 */
	function out($title)
	{
		$data = array(
			'messages' 	=> $this->messages,
			'title'		=> $title
		);
		
		return $this->obj->load->view('main/messages', $data, TRUE);
	}
}

/* End of file messages.php */
/* Location: ./system/libraries/messages.php */