<?php 
class Vuelos_model extends CI_Model {
			
	function insertVuelo($vuelo){
		$this->db->insert('vuelos', $vuelo);
		
		$id=$this->db->insert_id();
		
	}	
	
	function getVuelo($id=NULL){
		$query = $this->db->query("SELECT * FROM vuelos WHERE vuelos.id_reserva='$id'");
		
		if($query->num_rows() > 0){
			foreach ($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}else{
			return FALSE;
		}
	}	
			
		
} 
?>
