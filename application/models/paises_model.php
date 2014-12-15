<?php 
class Paises_model extends CI_Model {
	
	function getPaises(){
		$query = $this->db->query("SELECT * FROM paises ORDER BY pais");
		
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
