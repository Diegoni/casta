<?php 
class Terminos_model extends CI_Model {
	
	function getTerminos(){
		$query = $this->db->query("SELECT * FROM terminos");
		
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
