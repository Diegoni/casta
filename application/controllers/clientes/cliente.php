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
 * @copyright	Copyright (c) 2008-2010, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Controlador de clientes
 *
 */
class Cliente extends MY_Controller
{
	/**
	 * Constructor
	 *
	 * @return Cliente
	 */
	function __construct()
	{
		//parent::__construct('clientes.cliente', 'clientes/M_cliente', TRUE, 'clientes/cliente.js', 'Clientes');
		parent::__construct();
		$this->load->library('userauth');
		$this->load->library('out');		
	}

	/**
	 * Gestión Cliente
	 * @return FORM
	 */
	function abm_clientes()
	{
		$db['texto']	= $this->idiomas_model->getIdioma(1);
		$db['test']		= array('test'=>'test1','test2'=>'test2');
		
		$this->load->helpers('vistas');
		$this->load->view('head', $db);
		$this->load->view('menu');
		$this->load->view('clientes/abm_clientes');
	}

}

/* End of file cliente.php */
/* Location: ./system/application/controllers/clientes/cliente.php */