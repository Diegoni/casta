<?php 
class Provincias_model extends CI_Model {
	
	function getProvincias($id_pais=NULL){
		$query = $this->db->query("SELECT * FROM provincias WHERE id_pais='$id_pais' ORDER BY provincia");
		
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
