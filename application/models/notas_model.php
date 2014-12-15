<?php 
class Notas_model extends CI_Model {
			
	function insertNota($nota){
		$this->db->insert('notas', $nota);
		
		$id_nota=$this->db->insert_id();
		
		return $id_nota;
	}	
	
	
	function getNotas(){
		$query = $this->db->query("SELECT * FROM notas");
			
		if($query->num_rows() > 0){	
			foreach ($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}else{
			return FALSE;
		}
	}	
	
	function getNota($id=NULL){
		$query = $this->db->query("SELECT * FROM notas WHERE id_nota='$id' ");
			
		if($query->num_rows() > 0){	
			foreach ($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}else{
			return FALSE;
		}
	}	
	
	function updateNota($nota){
		$this->db->update('notas', $nota, array('id_nota' => $nota['id_nota']));
	}	
			
		
} 
?>
