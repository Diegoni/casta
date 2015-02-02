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

class Usuario extends MY_Controller {

	/**
	 * Constructor
	 *
	 * @return App
	 */
	function __construct()
	{
		parent::__construct('user.usuario', 'user/m_usuario', TRUE, 'user/usuario.js', 'Usuarios');		
	}
}

/* End of file usuario.php */
/* Location: ./system/application/controllers/usuario.php */