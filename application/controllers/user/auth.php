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

class Auth extends MY_Controller {

	/**
	 * Constructor
	 *
	 * @return Auth
	 */
	function __construct()
	{
		parent::__construct();

		$this->obj->load->library('Userauth');
	}

	/**
	 * Realiza el login del usuario
	 *
	 * @param string $username Usuario
	 * @param string $password Contraseña
	 * @param string $url Url de redirección cuando login Ok
	 * @param bool $reload Indica si se debe mandar una URL para redireccionar
	 * @return JSON
	 */
	function login($username = null, $password = null, $url = null, $reload = null)
	{
		$username 		= isset($username)?$username:$this->input->get_post('username');
		$password 		= isset($password)?$password:$this->input->get_post('password');
		$url 			= isset($url)?$url:$this->input->get_post('url');
		$reload 		= isset($reload)?$reload:$this->input->get_post('q-reload');

		if ($this->userauth->check_login($username, $password))
		{
			if ($reload == 'true')
			{
				$this->out->redirect($url);
			}
			else
			{
				$data = array(
					'success' 	=> TRUE,
					'message'	=> $this->lang->line('login_ok'),
					'session_id'=> $this->session->get_session_id()
				);
				$this->out->send($data);
				#$this->out->message(TRUE, $this->lang->line('login_ok'));
			}
		}
	}

	/**
	 * Logout del usuario
	 *
	 * @return JSON
	 */
	function logout()
	{
		$this->userauth->logout();
		echo $this->out->redirect($this->session->flashdata('uri'));
	}

	/**
	 * Cambia el password del usuario actual
	 * @param string $old Antiguo password
	 * @param string $new Nuevo password
	 * @return JSON
	 */
	function passwd($old = null, $new = null)
	{
		$this->userauth->check_login();

		$new = isset($new)?$new:$this->input->get_post('new');
		$old = isset($old)?$old:$this->input->get_post('old');

		if (isset($new) && ($old!= ''))
		{

			$res = $this->userauth->set_password($old, $new);
			if ($res)
			{
				$this->out->message(TRUE, $this->lang->line('password_actualizado'));
			}
			else
			{
				$this->out->message(FALSE, $this->lang->line('password_incorrecto'));
			}
		}
		else
		{
			$this->out->message(FALSE, $this->lang->line('mensaje_faltan_datos'));
		}
	}

	/**
	 * Muestra un error de autorización
	 *
	 * @return HTML
	 */
	function auth_error()
	{
		$this->session->keep_flashdata('uri');
		$url = site_url($this->session->flashdata('uri'));
		$message = $this->session->flashdata('message');
		$message = sprintf($this->lang->line('user_no_login'), $message);
		//$message .= sprintf($this->lang->line('user_no_login_url'), site_url('user/show_login'));

		$datos['message'] = $message;
		$this->load->helper('asset');
		$this->load->view('main/auth_error', $datos);
	}

	/**
	 * Recarga las autorizaciones
	 * @return JSON
	 */
	function auth_reload()
	{
		$this->userauth->check_login();
		
		$auth = $this->userauth->auth_reload();
		$res = array(
			'success' 	=> TRUE,
			'message' 	=> $this->lang->line('ua_auth_reload'),
			'auth'		=> $auth
		);

		$this->out->send($res);
	}
	
	/**
	 * Consultar permisos
	 * @return HTML
	 */
	function show()
	{
		$this->userauth->check_login();
		
		$auth = $this->userauth->get_auths();
		$data = array('auth' => $auth);
		$message = $this->load->view('user/auth', $data, TRUE);
		$this->out->html_file($message, $this->lang->line('Permisos'), 'iconoPermisosTab');
	}	
}

/* End of file auth.php */
/* Location: ./system/application/controllers/user/auth.php */