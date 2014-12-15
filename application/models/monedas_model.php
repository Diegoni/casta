<?php 
class Monedas_model extends CI_Model {
	
	function getMonedas(){
		$query = $this->db->query("SELECT * FROM monedas WHERE monedas.delete = 0");
		
		if($query->num_rows()>0){
			foreach ($query->result() as $fila){
				$data[] = $fila;
			}
			
			return $data;	
		}else{
			return false;
		}
		
	}
	
	
	function getMoneda($id_moneda){
		$query = $this->db->query("SELECT * FROM monedas WHERE id_moneda='$id_moneda'");
		
		if($query->num_rows()>0){
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
