<?php 
class Config_email_habitacion_model extends CI_Model {
	
	function getConfig($id=NULL){
		$query = $this->db->query("SELECT * FROM config_email_habitacion
									INNER JOIN hoteles ON(config_email_habitacion.id_hotel=hoteles.id_hotel)
									INNER JOIN tipos_correo ON(config_email_habitacion.id_tipo_correo=tipos_correo.id_tipo_correo)
									WHERE id_config_email_habitacion=$id");
		
		if($query->num_rows() > 0){
			foreach ($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}else{
			return FALSE;
		}
	}
	
	function updateConfig($registro){
		$this->db->update('config_email_habitacion', $registro, array('id_config_email_habitacion' => $registro['id_config_email_habitacion']));
	}
} 
?>
