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
class Proveedor extends MY_Controller
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
		$this->load->helpers('vistas');
		$this->load->model('proveedores/m_proveedor');
	}

	/**
	 * Gestión Cliente
	 * @return FORM
	 */
	function abm_proveedores()
	{
		$db['texto']	= $this->m_idiomas->getIdioma(1);
		
		//carga de select
		$db['idiomas']	= $this->m_idiomas->getSelect();
		
		// para helper_abm_clientes
		$db['proveedores']	= $this->m_proveedor->getRegistros();
		
		
		$this->load->view('head', $db);
		$this->load->view('menu');
		$this->load->view('proveedor/abm_proveedores');
		$this->load->view('footer');
	}

}

/* End of file cliente.php */
/* Location: ./system/application/controllers/clientes/cliente.php */