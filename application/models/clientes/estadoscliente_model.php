<?php 
class Estadoscliente_model extends CI_Model {
	
	function getEstados(){
		$query = $this->db->query("SELECT * FROM cli_estadoscliente 
									WHERE cli_estadoscliente.delete = 0
									ORDER BY cli_estadoscliente.cDescripcion");
			
		if($query->num_rows() > 0){	
			foreach ($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}else{
			return FALSE;
		}
	}
	
	
	function getEstado($id=NULL){
		$query = $this->db->query("SELECT * FROM cli_estadoscliente WHERE cli_estadoscliente.nIdEstado = '$id'");
			
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
