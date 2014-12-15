<?php 
class Aerolineas_model extends CI_Model {
	
	function getAerolineas(){
		$query = $this->db->query("SELECT * FROM aerolineas WHERE aerolineas.delete = 0");
			
		if($query->num_rows() > 0){	
			foreach ($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}else{
			return FALSE;
		}
	}
	
	
	function getAerolinea($id=NULL){
		$query = $this->db->query("SELECT aerolinea FROM aerolineas WHERE aerolineas.id_aerolinea = '$id'");
			
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
