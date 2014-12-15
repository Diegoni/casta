<?php 
class Imagenes_hotel_model extends CI_Model {
	
	function getImagenes($id){
		$query = $this->db->query("SELECT * FROM imagenes_hoteles WHERE id_hotel='$id' ORDER BY orden");
		
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
