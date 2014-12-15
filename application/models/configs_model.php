<?php 
class Configs_model extends CI_Model {
	
	function getConfigs(){
		$query = $this->db->query("SELECT * FROM config");
		
		if($query->num_rows() > 0){
			foreach ($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}else{
			return FALSE;
		}
	}
	
	function getConfigArticulos(){
		$query = $this->db->query("SELECT * FROM config_articulos where id_config_articulos=1");
		
		if($query->num_rows() > 0){
			foreach ($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}else{
			return FALSE;
		}
	}
	
} 
?>
