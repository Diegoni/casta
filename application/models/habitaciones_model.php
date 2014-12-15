<?php 
class Habitaciones_model extends CI_Model {
	
	function getHabitaciones($consulta=NULL){
		if(isset($consulta['adultos'])){
			if($consulta['adultos']>=$consulta['menores']){
				if($consulta['adultos']==1 && $consulta['menores']==1){
					$consulta['adultos']=$consulta['adultos']+1;
				}
				$query = $this->db->query("SELECT * FROM habitaciones
									INNER JOIN tarifas ON(habitaciones.id_tarifa=tarifas.id_tarifa) 
									WHERE id_hotel='$consulta[hotel]'
									AND adultos<='$consulta[adultos]'
									AND habitaciones.delete=0
									ORDER BY habitaciones.orden");					
			}else{
				$query = $this->db->query("SELECT * FROM habitaciones
									INNER JOIN tarifas ON(habitaciones.id_tarifa=tarifas.id_tarifa) 
									WHERE id_hotel='$consulta[hotel]'
									AND menores<='$consulta[menores]'
									AND habitaciones.delete=0
									ORDER BY habitaciones.orden");				
			}
			
			
		}else if(isset($consulta['id_hotel'])){
			$query = $this->db->query("SELECT * FROM habitaciones
									INNER JOIN tarifas ON(habitaciones.id_tarifa=tarifas.id_tarifa) 
									INNER JOIN monedas ON(tarifas.id_moneda=monedas.id_moneda)
									WHERE id_hotel='$consulta[id_hotel]' 
									AND habitaciones.delete=0
									ORDER BY habitaciones.orden");
		}else{
			$query = $this->db->query("SELECT 
									habitaciones.id_habitacion,
									habitaciones.habitacion,
									hoteles.hotel 
									FROM habitaciones
									INNER JOIN hoteles ON(habitaciones.id_hotel=hoteles.id_hotel) 
									WHERE habitaciones.delete=0
									ORDER BY habitaciones.id_hotel");
			
		}
		
		
		
		if($query->num_rows() > 0){
			foreach ($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}else{
			$data=array();
			return $data;
		}
	}
	
	function getHabitacion($id=NULL){
		$query = $this->db->query("SELECT * FROM habitaciones
									INNER JOIN tipos_habitacion ON(habitaciones.id_tipo_habitacion=tipos_habitacion.id_tipo_habitacion)
									WHERE id_habitacion='$id'");
									
		if($query->num_rows() > 0){
			foreach ($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}else{
			$data=array();
			return $data;
		}									
	}

	function getHabitaciones_post($consulta){
		$habitaciones=$this->getHabitaciones($consulta);
		if($habitaciones){
			$habitaciones_post=array();
			foreach ($habitaciones as $habitacion) {
				if($this->input->post('habitacion'.$habitacion->id_habitacion)!=0){
					$habitaciones_post[$habitacion->id_habitacion]= $this->input->post('habitacion'.$habitacion->id_habitacion);					
				}
			}	
		}
		
		return $habitaciones_post;
		
	}
	

} 
?>
