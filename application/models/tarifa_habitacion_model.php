<?php 
class Tarifa_habitacion_model extends CI_Model {
	
	function insertHabitaciones($habitaciones, $id_tarifa_temporal){

		foreach ($habitaciones as $key => $value) {
			$registro=array('id_tarifa_temporal' 	=> $id_tarifa_temporal,
							'id_habitacion' 		=> $value);
				
			$this->db->insert('tarifa_habitacion', $registro); 
            
            $id=$this->db->insert_id();
			
			$query=$this->db->query("SELECT 
								tarifa_habitacion.id_habitacion
								FROM `tarifa_habitacion` 
								WHERE tarifa_habitacion.id_tarifa_habitacion='$id'");
			
			foreach ($query->result() as $fila){
				$tarifa_habitacion[] = $fila->id_habitacion;
			}

		}
		
		return $tarifa_habitacion;
	}
	
	
	function getTarifaNombre($tarifa_temporal){
		$query=$this->db->query("SELECT 
							*
							FROM `tarifa_habitacion`
							INNER JOIN habitaciones 
							ON(tarifa_habitacion.id_habitacion=habitaciones.id_habitacion)
							INNER JOIN tarifas_temporales
							ON(tarifa_habitacion.id_tarifa_temporal=tarifas_temporales.id_tarifa_temporal)
							INNER JOIN tipos_tarifa
							ON(tarifas_temporales.id_tipo_tarifa=tipos_tarifa.id_tipo_tarifa) 
							WHERE tarifas_temporales.tarifa_temporal='$tarifa_temporal'
							ORDER BY tarifas_temporales.entrada");
		if($query->num_rows() > 0){	
			foreach ($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}else{
			return FALSE;
		}
		
	}
	
	function getTarifaID($id){
		$query=$this->db->query("SELECT 
							tarifa_habitacion.id_habitacion
							FROM `tarifa_habitacion` 
							WHERE tarifa_habitacion.id_tarifa_temporal='$id'");
		
		if($query->num_rows() > 0){	
			foreach ($query->result() as $fila){
				$tarifa_habitacion[] = $fila->id_habitacion;
			}
			return $tarifa_habitacion;
		}else{
			return FALSE;
		}
		
	}
	
			
	
		
} 
?>
