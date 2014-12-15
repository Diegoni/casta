<?php 
class Tipos_tarjeta_model extends CI_Model {
	
	function getTipos(){
		$query=$this->db->query("SELECT * FROM `tipos_tarjeta` WHERE `delete`=0");
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
		$query=$this->db->query("SELECT * FROM `tipos_tarjeta` where tipos_tarjeta.id_tipo_tarjeta='$id'");
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
