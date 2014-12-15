<?php 
class Disponibilidades_model extends CI_Model {
		
	function getDisponibilidad($habitaciones, $consulta){
		
		foreach ($habitaciones as $habitacion) {
			$query=$this->db->query("SELECT 
							disponibilidad_habitacion.id_habitacion as id_habitacion,
							disponibilidades.id_disponibilidad as id_disponibilidad
							FROM `disponibilidad_habitacion` 
							INNER JOIN disponibilidades ON(disponibilidad_habitacion.id_disponibilidad=disponibilidades.id_disponibilidad)
							WHERE (DATE_FORMAT(disponibilidades.salida, '%d-%m-%Y')  >= '$consulta[entrada]' 
							AND DATE_FORMAT(disponibilidades.entrada, '%d-%m-%Y') <= '$consulta[salida]')
							AND disponibilidad_habitacion.id_habitacion = '$habitacion->id_habitacion'
							AND disponibilidades.delete=0 
							");
			if($query->num_rows() > 0){
				foreach ($query->result() as $fila){
					$data[] = $fila;
				}
			}	
			
		}
		
		if(isset($data)){
			return $data;	
		}
				
	}	
	
	function insertDisponibilidad($registro){
		$this->db->insert('disponibilidades', $registro);
		
		$id=$this->db->insert_id();
		
		return $id;	
		
	}
	
	function getDisponibilidadId($id){
		$query=$this->db->query("SELECT 
							*
							FROM `disponibilidades`
							WHERE disponibilidades.id_disponibilidad='$id'");
		if($query->num_rows() > 0){
			foreach ($query->result() as $fila){
					$data[] = $fila;
			}
			return $data;
		}else{
			return false;
		}
	}
	
		
} 
?>
