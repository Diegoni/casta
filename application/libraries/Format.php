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
 * Utilidades para formateo
 * @author alexl
 *
 */
class Format {
	/**
	 * Instancia de CI
	 * @var CI
	 */
	private $obj;
	/**
	 * Último error
	 * @var string
	 */
	private $_error;

	/**
	 * Constructor
	 * @return Utils
	 */
	function __construct()
	{
		$this->obj =& get_instance();
		log_message('debug', 'Format Class Initialised via '.get_class($this->obj));
	}


	/**
	 * Último error generado
	 * @return string
	 */
	function get_error()
	{
		return $this->_error;
	}

}
/* End of file Utils.php */
/* Location: ./system/libraries/utils.php */