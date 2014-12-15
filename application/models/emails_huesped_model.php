<?php 
class Emails_huesped_model extends CI_Model {
	
	function getEmail($id=NULL){
		$query = $this->db->query("SELECT * FROM emails_huesped WHERE id_huesped='$id'");
			
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
