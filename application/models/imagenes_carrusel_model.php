<?php 
class Imagenes_carrusel_model extends CI_Model {
	
	function getImagenes($id=NULL){
		$query = $this->db->query("	SELECT * FROM imagenes_carrusel 
									WHERE imagenes_carrusel.id_hotel='$id'
									ORDER BY orden");
		
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
