<?php 
class Habitacion_servicio_model extends CI_Model {
	
	function getServicios($id){
		$query = $this->db->query("SELECT * FROM habitacion_servicio INNER JOIN servicios ON(habitacion_servicio.id_servicio=servicios.id_servicio) WHERE habitacion_servicio.id_habitacion='$id' ORDER BY servicio");
		
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
