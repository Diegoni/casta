<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Controllers
 * @category	pedidos cliente
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @filesource
 */

/**
 * Buscador Libros
 *
 */
class Buscadorlibros extends MY_Controller
{

	/**
	 * Constructor
	 *
	 * @return App
	 */
	function Buscadorlibros()
	{
		parent::__construct('buscadorlibros', 'M_Buscadorlibros');
		$this->userauth->check_login(null, null, null, 'user/auth_error');
	}

	/**
	 * Formulario principal
	 *
	 */
	function index()
	{
		$this->_show_form('index', 'buscadorlibros/buscador.js', $this->lang->line('Buscador de Libros'));
	}

	function get_robots()
	{
		$this->userauth->roleCheck(('buscadorlibros.index'));
		$code	= isset($code)?$code:$this->input->get_post('code', null);

		$robots = $this->reg->get_robots();
		$datos['robots'] = $robots;
		$this->load->view('buscadorlibros/listrobots', $datos);
		
	}

	function search($codes = null)
	{
		$this->userauth->roleCheck(('buscadorlibros.buscar'));
		$codes	= isset($codes)?$codes:$this->input->get_post('codes', null);

		$data = $this->reg->search($codes);

		$success = TRUE;

		$res = array(
			'success' 		=> $success,
			'total_data' 	=> count($data),
			'value_data' 	=> $data
		);

		echo $this->out->send($res);
	}
	
	function test()
	{
		//@todo Hay que hacer un test de cada robot para comprobar si ha cambiado o no
		echo 'Hay que hacer el test de cada robot, usando códigos que funcionan y comprobando el resultado';
	}
}

/* End of file buscadorlibros.php */
/* Location: ./system/application/controllers/buscadorlibros.php */