<?php 
class Tarjetas_model extends CI_Model {
			
	function insertTarjeta($registro){
		$query = $this->db->query("SELECT * FROM tarjetas WHERE tarjetas.tarjeta='$registro[tarjeta]' AND tarjetas.id_huesped='$registro[id_huesped]'");
		
		if($query->num_rows()==0){
			$this->db->insert('tarjetas', $registro);
		
			$id=$this->db->insert_id();
		
			return $id;	
		}else{
			return 0;
		}
		
		
	}		
	
	function getTarjeta($id=NULL){
		$query = $this->db->query("SELECT * FROM tarjetas WHERE tarjetas.id_huesped='$id'");
			
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
