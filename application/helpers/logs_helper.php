<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	
	
	function insert_control_fechas($datos, $id){
		if($datos['entrada']>$datos['salida']){
			return false;
		}else{
			return true;	
		} 
	}
	

	function helper_insert_log($datos, $id, $tabla, $tabla_log){
		$session_data = $this->session->userdata('logged_in');
		
	    $registro = array(
	        "tabla" => $tabla,
	        "id_tabla" => $id,
	        "accion" => 'insert',
	        "fecha" => date('Y-m-d H:i:s'),
	        "id_usuario" => $session_data['id_usuario']
	    );
	 
	    $this->db->insert($tabla_log, $registro);
	 
	    return true;
	}
	
	function update_log($datos, $id){
		$CI =& get_instance();
		$CI->load->model('logs_model');
		$CI->load->model('usuarios_model');
		
		$log_tabla='logs_'.$_COOKIE['tabla'];
		$id_usuario=$CI->usuarios_model->getUsuario();
						
    	$registro = array(
	        "tabla" => $_COOKIE['tabla'],
	        "id_tabla" => $id,
	        "accion" => 'update',
	        "fecha" => date('Y-m-d H:i:s'),
	        "id_usuario" => $id_usuario
	    );
 		
		$CI->logs_model->insertLogs($log_tabla, $registro);
    	 
    	return true;
	}
	
	
	function delete_log($id){
    	$session_data = $this->session->userdata('logged_in');
		
		$registro = array(
	        "tabla" => $_COOKIE['tabla'],
	        "id_tabla" => $id,
	        "accion" => 'delete',
	        "fecha" => date('Y-m-d H:i:s'),
	        "id_usuario" => $session_data['id_usuario']
	    );
 
    	$this->db->insert('logs_mensajes',$registro);
			
    	return $this->db->update($_COOKIE['tabla'], array('delete' => 1), array($_COOKIE['id'] => $id));
	}
	

