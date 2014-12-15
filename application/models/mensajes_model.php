<?php 
class Mensajes_model extends CI_Model {
	
	function insertMensaje($datos){
			
		$this->db->insert('mensajes', $datos);
		
		$id_mensaje=$this->db->insert_id();
		
		return $id_mensaje;	
	}
	
	function getCantNuevos(){
		$query = $this->db->query("SELECT * FROM mensajes WHERE id_estado_mensaje=1 ");
		
		return $query->num_rows();
	}
	
	function getNuevos(){
		$query = $this->db->query("SELECT * FROM mensajes WHERE id_estado_mensaje=1 ");
		
		foreach ($query->result() as $fila){
			$data[] = $fila;
		}
		
		return $data;
	}
			
} 
?>
