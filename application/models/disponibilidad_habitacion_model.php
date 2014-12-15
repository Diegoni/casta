<?php 
class Disponibilidad_habitacion_model extends CI_Model {
	
	function insertHabitaciones($habitaciones, $id_disponibilidad){

		foreach ($habitaciones as $key => $value) {
			$registro=array('id_disponibilidad' 	=> $id_disponibilidad,
							'id_habitacion' 		=> $value);
				
			$this->db->insert('disponibilidad_habitacion', $registro); 
            
            $id=$this->db->insert_id();
			
			$query=$this->db->query("SELECT 
								disponibilidad_habitacion.id_habitacion
								FROM `disponibilidad_habitacion` 
								WHERE disponibilidad_habitacion.id_disponibilidad_habitacion='$id'");
			
			foreach ($query->result() as $fila){
				$disponibilidad_habitacion[] = $fila->id_habitacion;
			}

		}
		
		return $disponibilidad_habitacion;
	}
	
	
	function getDisponibilidadNombre($disponibilidad){
		$query=$this->db->query("SELECT 
							*
							FROM `disponibilidad_habitacion`
							INNER JOIN habitaciones 
							ON(disponibilidad_habitacion.id_habitacion=habitaciones.id_habitacion)
							INNER JOIN disponibilidades
							ON(disponibilidad_habitacion.id_disponibilidad=disponibilidades.id_disponibilidad) 
							WHERE disponibilidades.disponibilidad='$disponibilidad'
							ORDER BY disponibilidades.entrada");
		if($query->num_rows() > 0){	
			foreach ($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}else{
			return FALSE;
		}
		
	}
	
	function getDisponibilidadID($id){
		$query=$this->db->query("SELECT 
							disponibilidad_habitacion.id_habitacion
							FROM `disponibilidad_habitacion` 
							WHERE disponibilidad_habitacion.id_disponibilidad='$id'");
		
		if($query->num_rows() > 0){	
			foreach ($query->result() as $fila){
				$disponibilidad_habitacion[] = $fila->id_habitacion;
			}
			return $disponibilidad_habitacion;
		}else{
			return FALSE;
		}
		
	}
	
			
	
		
} 
?>
