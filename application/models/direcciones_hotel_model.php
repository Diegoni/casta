<?php 
class Direcciones_hotel_model extends CI_Model {
	
	function getDirecciones($id=NULL){
		$query = $this->db->query("SELECT * FROM direcciones_hotel
									INNER JOIN provincias
									ON(direcciones_hotel.id_provincia=provincias.id_provincia)");
		
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
