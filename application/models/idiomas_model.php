<?php 
class Idiomas_model extends CI_Model {
	
	function getIdioma($url){
		/*	
		$id		= $this->config->item('idioma');
		
		$query	= $this->db->query("SELECT * FROM idiomas WHERE idiomas.url='$url'");
		
		if($query->num_rows() > 0){
			foreach ($query->result() as $fila){
				$data[] = $fila;
			}
		}else{
			return FALSE;
		}
		
		foreach ($data as $idioma) {
			include_once('idiomas/'.$idioma->archivo);	
		}
		 
		*/
		include_once('idiomas/spanish.php');
		
		return $texto;
	}
	
	function getIdioma_id($id){
		$query	= $this->db->query("SELECT * FROM idiomas WHERE idiomas.id_idioma='$id'");
		
		if($query->num_rows() > 0){
			foreach ($query->result() as $fila){
				$data[] = $fila;
			}
		}else{
			return FALSE;
		}
		
		return $data;
	}
	
	function getIdiomas(){
		$query = $this->db->query("SELECT * FROM `idiomas` WHERE `delete`= 0 AND id_idioma != 0");
		
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
