<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	clientes
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

require_once(APPPATH . 'controllers' . DIRECTORY_SEPARATOR . 'perfilescontroller.php');

/**
 * Perfiles de un cliente
 *
 */
class PerfilCliente extends PerfilController
{

	/**
	 * Constructor
	 *
	 * @return PerfilCliente
	 */
	function __construct()
	{
		parent::__construct('clientes.perfilcliente');

		$this->_idref = 'nIdCliente';
		$this->_config['E'] = array('perfiles/M_email', 'clientes/M_clienteemail', 'nIdEmail');
		$this->_config['C'] = array('perfiles/M_contacto', 'clientes/M_clientecontacto', 'nIdContacto');
		$this->_config['T'] = array('perfiles/M_telefono', 'clientes/M_clientetelefono', 'nIdTelefono');
		$this->_config['D'] = array('perfiles/M_direccion', 'clientes/M_clientedireccion', 'nIdDireccion');
	}
}
/* End of file perfilcliente.php */
/* Location: ./system/application/controllers/clientes/perfilcliente.php */