<?php 
class Estados_reserva_model extends CI_Model {
	
	function getEstados(){
		$query = $this->db->query("SELECT * FROM estados_reserva ORDER By estado_reserva");
		
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
