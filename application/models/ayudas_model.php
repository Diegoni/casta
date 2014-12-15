<?php 
class Ayudas_model extends CI_Model {
	
	function getAyuda($ayuda){
		$query = $this->db->query("SELECT * FROM ayudas WHERE sector='$ayuda[sector]' and id_idioma='$ayuda[id_idioma]'");
				
		if($query->num_rows()== 0){
			$query = $this->db->query("SELECT * FROM ayudas WHERE sector='$ayuda[sector]' and id_idioma=0");
		}	
			
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
