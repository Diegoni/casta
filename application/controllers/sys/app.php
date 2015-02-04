<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class App extends CI_Controller {
	
	function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
		$this->load->model('usuarios/usuarios_model');
	}

/**********************************************************************************
 **********************************************************************************
 * 
 * 				Login de usuarios
 * 
 * ********************************************************************************
 **********************************************************************************/

	
	function login()
	{
		$this->load->helper(array('form'));
		$db['texto']	= $this->idiomas_model->getIdioma(1);
		$this->load->view('app/login', $db);
	}
	
	function verifylogin()
	{
		//This method will have the credentials validation
		$this->load->library('form_validation');
	
		$this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
		$this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|callback_check_database');
	
		if($this->form_validation->run() == FALSE)
		{
			//Falló la validación, regresa al login
			$db['texto']	= $this->idiomas_model->getIdioma(1);
			$this->load->view('app/login', $db);
		}
		else
		{
			//Login ok, ingresa al sitio
			redirect('sys/app/inicio/','refresh');
		}

	}

	function check_database($password)
	{
		//Validación de datos con éxito, falta validar con la base de datos
		$username = $this->input->post('username');

		//query a la base de datos
		$result = $this->usuarios_model->login($username, $password);

		if($result)
		{
			$sess_array = array();
			$ci = & get_instance();
			foreach($result as $row)
			{
				$sess_array = array(
					'id_usuario' => $row->id_usuario,
					'usuario' => $row->usuario
				);
			}
	 
			$this->session->unset_userdata('logged_in');
			$this->session->set_userdata('logged_in', $sess_array);
     
			return TRUE;
		}
		else
		{
			$this->form_validation->set_message('check_database', 'Invalid username or password');
			return FALSE;
		}
	}
	
/**********************************************************************************
 **********************************************************************************
 * 
 * 				Inicio de la aplicación
 * 
 * ********************************************************************************
 **********************************************************************************/
	function inicio(){
		
	}
}

