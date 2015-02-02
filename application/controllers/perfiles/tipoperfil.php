<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	perfiles
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Zonas
 *
 */
class Tipoperfil extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return App
	 */
	function __construct()
	{
		parent::__construct('perfiles.tipoperfil', 'perfiles/M_Tipoperfil', TRUE, null, 'Tipos Perfil');
	}
}

/* End of file tipoperfil.php */
/* Location: ./system/application/controllers/perfiles/tipoperfil.php */