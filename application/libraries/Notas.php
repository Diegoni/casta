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
 * Envio de datos
 * @author alexl
 *
 */
class Notas {
	/**
	 * Instancia de CI
	 * @var CI
	 */
	private $obj;

	/**
	 * Constructor
	 * @return Notas
	 */
	function __construct()
	{
		$this->obj =& get_instance();
		$this->obj->load->model('generico/m_nota');
		
		log_message('debug', 'Notas Class Initialised via '.get_class($this->obj));
	}

	/**
	 * Añade una tarea al sistema
	 * @param string $descripcion Descripción
	 * @param string $cmd Comando
	 * @return int Id de la tarea
	 */
	function add($tabla, $id, $texto, $tipo = null)
	{
		$data = array (
			'tObservacion'	=> $texto,
			'cTipo'			=> $tipo,
			'cTabla'		=> $tabla,
			'nIdRegistro'	=> $id
		);
		return $this->obj->m_nota->insert($data);
	}
}

/* End of file notas.php */
/* Location: ./system/application/libraries/notas.php */