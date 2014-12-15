<?php 
class Tipos_tarifa_model extends CI_Model {
	
	function getTipos(){
		$query=$this->db->query("SELECT * FROM tipos_tarifa");
		if($query->num_rows()>0){
			foreach ($query->result() as $fila){
				$data[] = $fila;
			}	
				return $data;
		}else{
				return $data=0;
		}
	}
	
	function getTipo($id){
		$query=$this->db->query("SELECT * FROM tipos_tarifa WHERE id_tipo_tarifa='$id'");
		if($query->num_rows()>0){
			foreach ($query->result() as $fila){
				$data[] = $fila;
			}	
				return $data;
		}else{
				return $data=0;
		}
	}
	
	
} 
?>
