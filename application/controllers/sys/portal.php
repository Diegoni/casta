<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	app
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Controlador principal de la aplicación
 *
 */
class Portal extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Portal
	 */
	function __construct()
	{
		parent::__construct(null, null, TRUE);
	}
	
		/**
	 * Ventana principal de la aplicación
	 *
	 */
	function index()
	{
		$this->load->library('Portlets');
		#echo '<pre>';echo $this->portlets->get_portlets_user('form');echo '</pre>';die();
		$this->_show_form(null, 'sys/portal.js', $this->lang->line('Portal'));
	}
	
}
/* End of file portal.php */
/* Location: ./system/application/controllers/sys/portal.php */
