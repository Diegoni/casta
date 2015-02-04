<?php
if(!defined('BASEPATH'))
	exit('No direct script access allowed');
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	libraries
 * @category	user
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @version		$Rev: 435 $
 * @filesource
 */

/**
 * Autentificación de usuarios
 */
class Userauth
{

	/**
	 * Instancia de CI
	 * @var CI
	 */
	var $obj;

	/**
	 * Asignación de username temporal
	 * @var string
	 */
	var $_username = null;

	/**
	 * Autorizaciones en cache
	 * @var array
	 */
	var $_auth = null;

	/**
	 * Contraseña actual
	 * @var string
	 */
	#var $_password = null;

	/**
	 * Constructor
	 * @return Userauth
	 */
	function __construct()
	{
		$this->obj = &get_instance();
		$this->obj->load->model('user/m_usuario');
		//$this->obj->load->library('Authorize');
		log_message('debug', 'User Authentication Class Initialised via ' . get_class($this->obj));
	}

	/**
	 * Comprueba si está logeado
	 * @return bool
	 */
	function loggedin()
	{
		$session_username = $this->get_username();
		$session_bool = $this->obj->session->userdata('loggedin');

		if((isset($session_username) && $session_username != '') && (isset($session_bool) && $session_bool == TRUE))
		{
			log_message('debug', 'Userauth:loggedin = ' . $this->get_username());
			return TRUE;
		}
		else
		{
		
			#var_dump($this->obj->config->item('bp.user.autologin'));
		 	#die();
			if ($this->obj->config->item('bp.user.autologin'))
			{
				return $this->login($this->obj->config->item('bp.user.autologin.username'), 
					$this->obj->config->item('bp.user.autologin.password'));
			}
			log_message('debug', 'Userauth:loggedin = false');
			return FALSE;
		}
	}

	/**
	 * Se desconecta
	 * @return null
	 */
	function logout()
	{
		log_message('debug', 'Userauth: Logout: ' . $this->get_username());

		$sessdata = array(
			'username' => null,
			'password' => null,
			'loggedin' => FALSE,
			'data' => null,
			'auth' => null);

		$this->obj->session->set_userdata($sessdata);
	}

	/**
	 * Devuelve el password codificado
	 * @param string $username Usuario
	 * @return string
	 */
	function get_password($username = null)
	{
		if (!isset($username)) return $this->obj->session->userdata('password');
		
		// Se usa autentificación por tabla
		$data = $this->obj->m_usuario->get(null, null, null, null, array('cUsername' => $username), 'nIdUsuario, cPassword');
		return (count($data) > 0) ? $data[0]['cPassword'] : null;
	}

	/**
	 * Login del usuario
	 *
	 * @param string $username Username to login
	 * @param string $password Password to match user
	 */
	function login($username, $password)
	{
		// make sure session will be seen as active at check()
		$this->obj->session->set_userdata('last_activity', time());

		if($username != '' && $password != '')
		{

			// Only continue if user and pass are supplied
			// SHA1 the password if it isn't already
			$p = $password;
			if(strlen($password) != 40)
			{
				$password = sha1($password);
			}

			$id = $this->obj->m_usuario->check_login($username, $password, $p);

			if(isset($id))
			{
				// Carga los datos
				$user = $this->obj->m_usuario->load($id, TRUE);
				// Carga los permisos
				$auth = $this->obj->m_usuario->get_auth($id);
				$this->_auth = $auth;

				// Actualiza última conexión
				$this->obj->m_usuario->stamp_login($id);
				$this->_password = $password;

				// Set session data array
				$sessdata = array(
					'username' => $username,
					'password' => $password,
					'loggedin' => TRUE,
					'data' => $user,
					'auth' => $auth);

				log_message('debug', "Userauth: login: setting session data");
				log_message('debug', "Userauth: login: Session: " . var_export($sessdata, TRUE));

				// Set the session
				$this->obj->session->set_userdata($sessdata);
				return TRUE;
			}
			else
			{
				log_message('debug', "Userauth: login: no match in db for user / password");
				return FALSE;
			}
		}
		else
		{
			log_message('debug', "Userauth: login: missing username or password");
			return FALSE;
		}
	}

	/**
	 * Cambia el password del usuario actual
	 * @param string $old Antiguo password
	 * @param string $new Nuevo password
	 * @return JSON
	 */
	function set_password($old, $new)
	{
		// Only continue if user and pass are supplied
		// SHA1 the password if it isn't already
		$p = $old;
		if(strlen($old) != 40)
		{
			$password = sha1($old);
		}

		$id = $this->obj->m_usuario->check_login($this->get_username(), $password, $p);

		if(isset($id))
		{
			// OK
			$data['cPassword'] = $new;
			return $this->obj->m_usuario->update($id, $data);
		}
		else
		{
			log_message('debug', "Userauth: login: no match in db for user / password");
			return FALSE;
		}
	}

	/**
	 * Comprueba si está logeado y si no intenta logearse por los parámetros
	 * @param string $username Usuario
	 * @param string $password Contraseña
	 * @param bool $remember_me Indica si se almacena una cookie para recordar al
	 * usuario
	 * @param bool $ret URL de envío en caso de error. Si es null se envía un JSON
	 * @return bool TRUE si está logeado, sino el resultado de error
	 */
	function check_login($username =null, $password =null, $remember_me =null, $ret =null)
	{
		$username = isset($username) ? $username : $this->obj->input->get_post('username');
		$password = isset($password) ? $password : $this->obj->input->get_post('password');
		$remember_me = isset($url) ? $remember_me : $this->obj->input->get_post('remember_me');

		if($username && $password)
		{
			$this->obj->load->library('Logger');
			if($this->login($username, $password))
			{
				$this->obj->logger->log("Userauth: check_login: login correcto: $username", 'login');
				log_message('debug', "Userauth: check_login: login correcto: $username");
				return TRUE;
			}
			else
			{
				$message = $this->obj->lang->line('ua_log_error');
				$this->obj->logger->log("Userauth: check_login: login incorrecto: $username", 'login');
				log_message('debug', "Userauth: check_login: login incorrecto: $username");
			}
		}
		else
			if($this->loggedin())
		{
			log_message('debug', "Userauth: check_login: estaba conectado: " . $this->get_username());
			return TRUE;
		}
		else
		{
			$message = $this->obj->lang->line('ua_auth_not_logged');
			log_message('debug', "Userauth: check_login: no logueado");
		}

		if(!isset($ret))
		{
			$res = array(
				'success'	=> FALSE,
				'message'	=> $message,
				'error_code' => 401
				);
			$this->obj->out->send($res);
			//echo $this->obj->out->message(FALSE, $message);
		}
		else
		{
			$this->obj->session->set_flashdata('message', $message);
			redirect($ret);
		}
	}

	/**
	 * Comprueba si se cumple un rol
	 *
	 * @param string $role Rol
	 * @param string $uri Dirección de vuelta
	 * @param bool $return Devuelve el resultado como función
	 */
	function roleCheck($role, $uri =null, $return =FALSE)
	{
		$this->check_login(null, null, null, null);
		$auth = isset($this->_auth)?$this->_auth:$this->obj->session->userdata('auth');
		#var_dump($return); die();
		#$this->obj->out->success($role);
		#$this->obj->out->success(print_r($this->_auth, TRUE));

		//echo '<pre>'; print_r($auth); echo '</pre>';
		$allow = FALSE;
		if(isset($auth[$role]))
		{
			$allow = $auth[$role];
		}
		else
		{
			$this->obj->config->item($role);
		}
		$this->obj->session->set_flashdata('uri', $uri);

		log_message('debug', 'roleCheck: Role = ' . $role);

		if(!$allow)
		{
			log_message('debug', 'roleCheck: Role = ' . $role . ' no permitido');
			if($return)
				return FALSE;

			$message = sprintf($this->obj->lang->line('ua_auth_denied'), $role);
			$res = array('success' => FALSE,
				'message' => $message,
				'url' => $uri);
			$this->obj->out->send($res);
		}
		log_message('debug', 'roleCheck: Role = ' . $role . ' permitido');
		return TRUE;
	}

	/**
	 * Devuelve las autorizaciones del usuario
	 * @return array
	 */
	function get_auths()
	{
		return (isset($this->_auth))?$this->_auth:$this->obj->session->userdata('auth');
	}

	/**
	 * Devuelve el login de usuario
	 * @return string
	 */
	function get_username()
	{
		if(isset($this->username))
		{
			return $this->username;
		}
		else
		{
			return $this->obj->session->userdata('username');
		}
	}

	/**
	 * Devuelve el nombre de usuario
	 * @return string
	 */
	function get_name()
	{
		$data = $this->obj->session->userdata('data');
		return $data['cNombre']; 
	}

	/**
	 * Devuelve el ID del usuario
	 * @return string
	 */
	function get_id()
	{
		$data = $this->obj->session->userdata('data');
		return $data['nIdUsuario']; 
	}

	/**
	 * Asigna el usuario a mano
	 * @param string $username Usuario
	 */
	function set_username($username =null)
	{
		$this->username = $username;
	}

	/**
	 * Recarga los permisos
	 * @return bool
	 */
	function auth_reload()
	{
		$id = $this->obj->session->userdata('data');
		if(isset($id['nIdUsuario']))
		{
			// Carga los permisos
			$this->obj->m_usuario->clear_cache();
			$auth = $this->obj->m_usuario->get_auth($id['nIdUsuario']);
			$this->_auth = $auth;
			$this->obj->session->set_userdata('auth', $auth);
			log_message('debug', "Userauth: reload auth ok");
			return $auth;
		}
		else
		{
			log_message('debug', "Userauth: reload auth fail");
			return FALSE;
		}
	}

}

/* End of file Userauth.php */
/* Location: ./system/libraries/Userauth.php */
