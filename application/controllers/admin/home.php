<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI
class Home extends CI_Controller {

 function __construct()
 {
   	parent::__construct();
   	
	$this->load->helper('menu');
	$this->load->model('reserva_habitacion_model');
	$this->load->library('grocery_CRUD');  
	 
	
 }

 function index()
 {
   	if($this->session->userdata('logged_in')){
		$data = $this->session->userdata('logged_in');
		$reservas=buscarReservas();
		$mensajes=buscarMensajes();
		
		$db=array_merge($reservas, $mensajes);
	 
	 	$this->load->view('backend/head.php');
		$this->load->view('backend/menu.php', $db);
		$this->load->view('backend/modal.php');   
     	$this->load->view('backend/home_view', $data);
		$this->load->view('backend/footer.php');
   }else{
   	 redirect('/admin/home/logout/','refresh');
   }
 }

 function logout()
 {
   $this->session->unset_userdata('logged_in');
   session_destroy();
    $this->load->helper(array('form'));
	$this->load->view('backend/head');
   	$this->load->view('backend/login_view');
 }

}

?>
