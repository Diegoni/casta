<?php 
class Gruposcliente_model extends CI_Model {
	
	function getGrupos(){
		$query = $this->db->query("SELECT * FROM cli_gruposcliente 
									WHERE cli_gruposcliente.delete = 0
									ORDER BY cli_gruposcliente.cDescripcion");
			
		if($query->num_rows() > 0){	
			foreach ($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}else{
			return FALSE;
		}
	}
	
	
	function getGrupo($id=NULL){
		$query = $this->db->query("SELECT * FROM cli_gruposcliente WHERE cli_gruposcliente.nIdGrupoCliente = '$id'");
			
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
