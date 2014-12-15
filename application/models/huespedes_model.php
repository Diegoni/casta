<?php 
class Huespedes_model extends CI_Model {
	
	public function insertHuesped($datos)
	{
		$query = $this->db->query("SELECT * FROM `emails_huesped` WHERE `email`='$datos[email]' ");
		
		if($query->num_rows()==0){
			$fecha= date('Y-m-d H:i:s');
			$pass=rand(1000, 9999);
			
			$huesped = array(
		        "nombre" => $datos['nombre'],
		        "apellido" => $datos['apellido'],
		        "dni" => 0,
		        "pass" =>$pass,
		        "id_tipo_huesped" => 1,
		        "fecha_alta" => $fecha,
		        "fecha_modificacion" => $fecha
	    	);
			    	 
		    $this->db->insert('huespedes', $huesped);
			
			$id_huesped=$this->db->insert_id();	
			
			if(isset($datos['telefono'])){		
				$telefono = array(
		        	"id_huesped" => $id_huesped,
		        	"telefono" => $datos['telefono']
		    	);
			
				$this->db->insert('telefonos_huesped',$telefono);
			}
			
			if(isset($datos['email'])){
				$email = array(
		        	"id_huesped" => $id_huesped,
		        	"email" => $datos['email']
		    	);
			
				$this->db->insert('emails_huesped',$email);			
			}
		}else{
			foreach ($query->result() as $row){
  				$id_huesped=$row->id_huesped;
			}
		}
		
		return $id_huesped;
	}

	
	function getHuespedes(){
		$query = $this->db->query("SELECT * FROM huespedes WHERE huespedes.delete = 0");
			
		if($query->num_rows() > 0){	
			foreach ($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}else{
			return FALSE;
		}
	}
	
	
	function getHuesped($id=NULL){
		$query = $this->db->query("SELECT * FROM huespedes WHERE huespedes.id_huesped='$id'");
		
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
