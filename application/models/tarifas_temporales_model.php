<?php 
class Tarifas_temporales_model extends CI_Model {
		
	function getTarifas($habitaciones, $consulta){
		
		foreach ($habitaciones as $habitacion) {
			$query=$this->db->query("SELECT 
							tarifa_habitacion.id_habitacion as id_habitacion,
							tarifas_temporales.valor as valor,
							tarifas_temporales.id_tipo_tarifa as id_tipo_tarifa,
							tarifas_temporales.id_tarifa_temporal as id_tarifa_temporal
							FROM `tarifa_habitacion` 
							INNER JOIN tarifas_temporales ON(tarifa_habitacion.id_tarifa_temporal=tarifas_temporales.id_tarifa_temporal)
							INNER JOIN tipos_tarifa ON(tarifa_habitacion.id_tarifa_temporal=tarifas_temporales.id_tarifa_temporal)
							WHERE (DATE_FORMAT(tarifas_temporales.salida, '%d-%m-%Y')  >= '$consulta[entrada]' 
							AND DATE_FORMAT(tarifas_temporales.entrada, '%d-%m-%Y') <= '$consulta[salida]')
							AND tarifa_habitacion.id_habitacion = '$habitacion->id_habitacion' 
							GROUP BY tarifa_habitacion.id_habitacion
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

	function calcular_precio($datos){
		if($datos['id_tipo_tarifa']==1){
			$valor=$datos['precio']+($datos['precio']*$datos['valor']/100);
		}else if($datos['id_tipo_tarifa']==2){
			$valor=$datos['precio']-($datos['precio']*$datos['valor']/100);	
		}else if($datos['id_tipo_tarifa']==3){
			$valor=$datos['valor'];
		}
		
		return $valor;
	}
	
	function getFechas($id){
		$query=$this->db->query("SELECT 
							tarifas_temporales.entrada,
							tarifas_temporales.salida
							FROM tarifas_temporales 
							WHERE tarifas_temporales.id_tarifa_temporal = '$id'
							");
							
		if($query->num_rows() > 0){
			foreach ($query->result() as $fila){
				$data[] = $fila;
			}
		}	
			
		return $data;				
		
	}
	
	function insertTarifa($registro){
		$this->db->insert('tarifas_temporales', $registro);
		
		$id=$this->db->insert_id();
		
		return $id;	
	}	
	
	function getTarifaID($id){
		$query=$this->db->query("SELECT 
							*
							FROM `tarifas_temporales`
							WHERE tarifas_temporales.id_tarifa_temporal='$id'");
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
