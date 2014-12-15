<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

function __construct(){
	parent::__construct();
	$this->load->helper('url');
}

function index(){
   redirect(base_url().'index.php/es/admin/login/','refresh');
}

}

?>