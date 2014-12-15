<?php 
class Reservas_model extends CI_Model {
	
	function insertReserva($datos){
			
		$this->db->insert('reservas', $datos);
		
		$id_reserva=$this->db->insert_id();
		
		return $id_reserva;	
	}
	
	function getReserva($id){
		$query = $this->db->query("SELECT 
									reservas.entrada as entrada,
									reservas.salida as salida,
									reservas.adultos as adultos,
									reservas.menores as menores,
									reservas.total as total,
									reservas.fecha_alta as fecha_alta,
									reservas.id_huesped as id_huesped,
									reservas.id_estado_reserva,
									reservas.id_nota
									FROM 
									reservas
									WHERE reservas.id_reserva='$id' ");
		if($query->num_rows() > 0){
			foreach ($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}else{
			return FALSE;
		}
	}
	
	function getCantNuevas(){
		$query = $this->db->query("SELECT * FROM reservas WHERE id_estado_reserva=1 ");
		
		return $query->num_rows();
	}
	
	
	function getNuevas(){
		$query = $this->db->query("SELECT * FROM reservas
									INNER JOIN huespedes
									ON(reservas.id_huesped=huespedes.id_huesped) 
									WHERE id_estado_reserva=1 ");

		if($query->num_rows()>0){
			foreach ($query->result() as $fila){
				$data[] = $fila;
				
			}	
			return $data;
		}else{
				return 0;
		}
		
	}
	
	function getReservas($consulta=NULL){
		$entrada_array = explode("/", $consulta['entrada']);	
		$entrada=$entrada_array['2'].'/'.$entrada_array['1'].'/'.$entrada_array['0'];
		$salida_array = explode("/", $consulta['salida']);	
		$salida=$salida_array['2'].'/'.$salida_array['1'].'/'.$salida_array['0'];
				
		$query=$this->db->query("SELECT * FROM reservas WHERE entrada>='$entrada' AND salida<='$salida'");
		if($query->num_rows()>0){
			foreach ($query->result() as $fila){
				$data[] = $fila;
				
			}	
				
				return $data;
		}else{
				return $data=0;
		}
		
	
	}
	
	function updateReserva($reserva){
		$this->db->update('reservas', $reserva, array('id_reserva' => $reserva['id_reserva']));
	}	
	
	
} 
?>
