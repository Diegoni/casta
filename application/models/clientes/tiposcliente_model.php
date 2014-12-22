<?php 
class Tiposcliente_model extends CI_Model {
	
	function getTipos(){
		$query = $this->db->query("SELECT * FROM cli_tiposcliente 
									WHERE cli_tiposcliente.delete = 0
									ORDER BY cli_tiposcliente.cDescripcion");
			
		if($query->num_rows() > 0){	
			foreach ($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}else{
			return FALSE;
		}
	}
	
	
	function getTipo($id=NULL){
		$query = $this->db->query("SELECT * FROM cli_tiposcliente WHERE cli_tiposcliente.nIdTipoCliente = '$id'");
			
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
