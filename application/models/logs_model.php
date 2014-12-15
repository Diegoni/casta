<?php 
class Logs_model extends CI_Model {
	
	function insertLogs($log_tabla, $registro){
		$this->db->insert($log_tabla, $registro);
	}
	
	
} 
?>
