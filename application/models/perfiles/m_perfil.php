<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Models
 * @category	perfiles
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @filesource
 */

/**
 * Unifica direcciones, emails, teléfonos y contactos
 *
 */
class M_perfil extends MY_Model
{
	protected $_id;
	protected $_direcciones;
	protected $_emails;
	protected $_telefonos;
	protected $_contactos;

	/**
	 * Costructor
	 * @return M_perfil
	 */
	function __construct()
	{
		parent::__construct();
	}

	function init($id, $direcciones, $emails, $telefonos, $contactos)
	{
		$this->_id = $id;
		$this->_direcciones = $direcciones;
		$this->_emails = $emails;
		$this->_telefonos = $telefonos;
		$this->_contactos = $contactos;
	}	
}

/* End of file M_perfil.php */
/* Location: ./system/application/models/perfiles/M_perfil.php */