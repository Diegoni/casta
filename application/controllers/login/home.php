<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI
class Home extends CI_Controller {

 function __construct()
 {
   	parent::__construct();
   	//$this->load->library('grocery_CRUD');  
 }

	function index(){
		if($this->session->userdata('logged_in')){
			$data = $this->session->userdata('logged_in');
					
			$this->load->view(ADMIN_LOGIN.'home_view', $data);
			
		}else{
			redirect(ADMIN_LOGIN.'home/logout/','refresh');
		}
	}

	function logout(){
		$this->session->unset_userdata('logged_in');
		session_destroy();
		
		$this->load->helper(array('form'));
		$this->load->view(ADMIN_LOGIN.'login_view');
	}

}

?>