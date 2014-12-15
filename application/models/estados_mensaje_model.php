<?php 
class Estados_mensaje_model extends CI_Model {
	
	function getEstados(){
		$query = $this->db->query("SELECT * FROM estados_mensaje ORDER By estado_mensaje");
		
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
