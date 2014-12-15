<?php 
class Usuarios_model extends CI_Model {
	
	function getUsuario(){
		$session_data = $this->session->userdata('logged_in');
		
		return $session_data['id_usuario'];
	}
	
	
	
	
} 
?>
