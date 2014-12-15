<?php 
class Estados_traduccion_model extends CI_Model {
	
	function getEstados(){
		$query = $this->db->query("SELECT * FROM estados_traduccion ORDER By estado_traduccion");
		
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
