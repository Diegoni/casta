<?php 
class Tarifascliente_model extends CI_Model {
	
	function getTarifas(){
		$query = $this->db->query("SELECT * FROM cat_tipostarifa 
									WHERE cat_tipostarifa.delete = 0
									ORDER BY cat_tipostarifa.cDescripcion");
			
		if($query->num_rows() > 0){	
			foreach ($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}else{
			return FALSE;
		}
	}
	
	
	function getTarifa($id=NULL){
		$query = $this->db->query("SELECT * FROM cat_tipostarifa WHERE cat_tipostarifa.nIdTipoTarifa = '$id'");
			
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
