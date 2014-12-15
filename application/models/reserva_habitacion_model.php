<?php 
class Reserva_habitacion_model extends CI_Model {
	
	function insertReserva_habitacion($id_reserva, $habitaciones){
		$habitaciones_post=array();
		
		foreach ($habitaciones as $id => $cantidad) {
			$datos=array('id_reserva'=>$id_reserva,
						 'id_habitacion'=>$id,
						 'cantidad'=> $cantidad, 
						 'prioridad'=>0					
							);
			$this->db->insert('reserva_habitacion', $datos);
			array_push($habitaciones_post, $this->db->insert_id());				
		}
		
		return $habitaciones_post;	
	}
	
	
	
	function getReserva($id_reserva){
			$query=$this->db->query("SELECT 
								reservas.entrada as entrada,
								reservas.salida as salida,
								reservas.adultos as adultos,
								reservas.menores as menores,
								reservas.fecha_alta as fecha_alta,
								reservas.id_nota as id_nota,
								reservas.id_reserva as id_reserva,
								reservas.id_huesped as id_huesped,
								reservas.total as total,
								reservas.id_estado_reserva as id_estado_reserva,
								habitaciones.habitacion as habitacion,
								habitaciones.id_habitacion as id_habitacion,
								reserva_habitacion.cantidad as cantidad,
								reserva_habitacion.id_reserva_habitacion as id_reserva_habitacion,
								hoteles.hotel as hotel,
								hoteles.id_hotel as id_hotel
								FROM `reserva_habitacion` 
								INNER JOIN reservas ON(reserva_habitacion.id_reserva=reservas.id_reserva)
								INNER JOIN habitaciones ON(reserva_habitacion.id_habitacion=habitaciones.id_habitacion)
								INNER JOIN hoteles ON(habitaciones.id_hotel=hoteles.id_hotel)
								WHERE reserva_habitacion.id_reserva='$id_reserva'");
		
		if($query->num_rows() > 0){
			foreach ($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}else{
			return FALSE;
		}
		
		return $data;
	}
	
	
	
	function getReservas_habitacion($habitaciones, $consulta){
				
		foreach ($habitaciones as $habitacion) {
			$query=$this->db->query("SELECT 
							reserva_habitacion.cantidad as cantidad,
							reserva_habitacion.id_habitacion as id_habitacion
							FROM `reserva_habitacion` 
							INNER JOIN reservas ON(reserva_habitacion.id_reserva=reservas.id_reserva)
							INNER JOIN estados_reserva ON(estados_reserva.id_estado_reserva=reservas.id_estado_reserva)
							WHERE (DATE_FORMAT(reservas.salida, '%d-%m-%Y') >= '$consulta[entrada]' 
							AND DATE_FORMAT(reservas.entrada, '%d-%m-%Y') <= '$consulta[salida]')
							AND reserva_habitacion.id_habitacion = '$habitacion->id_habitacion' 
							AND (estados_reserva.reserva_lugar=1)
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
	
	function cambioHabitaciones($habitaciones, $id_reserva){
			
		$query_reserva=$this->db->query("SELECT *
							FROM `reserva_habitacion` 
							WHERE reserva_habitacion.id_reserva='$id_reserva'");
		$cantidad_reserva=$query_reserva->num_rows();
		$nuevas=0;
			
		foreach ($habitaciones as $key => $value) {
			$query=$this->db->query("SELECT *
							FROM `reserva_habitacion` 
							WHERE reserva_habitacion.id_reserva='$id_reserva'
							AND reserva_habitacion.id_habitacion='$value'
							");
							
			if($query->num_rows()==0){
				$registro=array('id_reserva' 	=>$id_reserva,
								'id_habitacion' =>$value,
								'cantidad'		=>1);
				
				$this->db->insert('reserva_habitacion', $registro); 
                $id_reserva_habitacion=$this->db->insert_id();  
                $nuevas=$nuevas+1;  
			}
		}
		
		$cantidad_reserva=$cantidad_reserva+$nuevas;
		
		if(count($habitaciones)!=$cantidad_reserva){
			foreach ($query_reserva->result() as $row) {
				if(!in_array($row->id_habitacion, $habitaciones)){
					$this->db->delete('reserva_habitacion', array('id_reserva_habitacion' => $row->id_reserva_habitacion)); 
				}
			}
			
		}
		
		return $nuevas;
	}
	
		
} 
?>
