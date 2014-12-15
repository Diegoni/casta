<?php 
class Modulos_idioma_model extends CI_Model {
	/*
	function getModulos(){
		$query = $this->db->query("SELECT * FROM modulos WHERE modulos.delete = 0");
			
		if($query->num_rows() > 0){	
			foreach ($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}else{
			return FALSE;
		}
	}
	*/
	
	function getModulo($datos=NULL){
		
		$query	= $this->db->query("SELECT * FROM modulos WHERE modulos.id_modulo = '$datos[modulo]'");
		
		if($query->num_rows() > 0){
			
			foreach ($query->result() as $row){
				if($row->usa_titulo==1 && $row->usa_descripcion==1){
					$query_2 = $this->db->query("SELECT 
												$row->id_tabla as id, 
												$row->titulo AS titulo, 
												$row->descripcion AS descripcion 
												FROM $row->tabla 
												WHERE $row->tabla.delete = 0");
				}else if($row->usa_titulo==1){
					$query_2 = $this->db->query("SELECT 
												$row->id_tabla as id, 
												$row->titulo AS titulo
												FROM $row->tabla 
												WHERE $row->tabla.delete = 0");
				}else if($row->descripcion==1){
					$query_2 = $this->db->query("SELECT 
												$row->id_tabla as id, 
												$row->descripcion AS descripcion 
												FROM $row->tabla 
												WHERE $row->tabla.delete = 0");
			
				}
				
				if($query_2->num_rows() > 0){
					foreach ($query_2->result() as $row_2){
						$query_3 = $this->db->query("	SELECT * FROM 
														modulos_idioma 
														WHERE modulos_idioma.id_modulo = '$row->id_modulo' 
														AND modulos_idioma.id_tabla = '$row_2->id' 
														AND modulos_idioma.id_idioma = '$datos[idioma]'");
						
						if($query_3->num_rows() == 0){
							$datos_insert=array(
										'titulo'				=> "-",
										'descripcion'			=> "-",
										'id_modulo'				=> $row->id_modulo,
										'id_tabla'				=> $row_2->id,
										'id_idioma'				=> $datos['idioma'],
										'id_estado_traduccion'	=> 1
							);
													
							$this->db->insert('modulos_idioma', $datos_insert);
						}
					}
					if($row->usa_hotel==1){
						if($row->usa_titulo==1 && $row->usa_descripcion==1){
							$query_final = $this->db->query("SELECT 
												$row->tabla.$row->id_tabla as id_tabla, 
												$row->tabla.$row->titulo as titulo_tabla, 
												$row->tabla.$row->descripcion as descripcion_tabla,
												modulos_idioma.titulo as titulo_idioma,
												modulos_idioma.descripcion as descripcion_idioma,
												estados_traduccion.estado_traduccion,
												estados_traduccion.label,
												hoteles.hotel,
												modulos_idioma.id_modulo_idioma
												FROM 
												modulos_idioma 
												INNER JOIN
												$row->tabla on(modulos_idioma.id_tabla=$row->tabla.$row->id_tabla)
												INNER JOIN
												hoteles on(hoteles.id_hotel=$row->tabla.id_hotel)
												INNER JOIN
												estados_traduccion on(estados_traduccion.id_estado_traduccion=modulos_idioma.id_estado_traduccion)
												WHERE modulos_idioma.id_modulo = '$row->id_modulo'
												AND modulos_idioma.id_idioma = '$datos[idioma]'
												ORDER BY hoteles.hotel, $row->tabla.$row->titulo");
						}else if($row->usa_titulo==1){
							$query_final = $this->db->query("SELECT 
												$row->tabla.$row->id_tabla as id_tabla, 
												$row->tabla.$row->titulo as titulo_tabla, 
												modulos_idioma.titulo as titulo_idioma,
												modulos_idioma.descripcion as descripcion_idioma,
												estados_traduccion.estado_traduccion,
												estados_traduccion.label,
												hoteles.hotel,
												modulos_idioma.id_modulo_idioma
												FROM 
												modulos_idioma 
												INNER JOIN
												$row->tabla on(modulos_idioma.id_tabla=$row->tabla.$row->id_tabla)
												INNER JOIN
												hoteles on(hoteles.id_hotel=$row->tabla.id_hotel)
												INNER JOIN
												estados_traduccion on(estados_traduccion.id_estado_traduccion=modulos_idioma.id_estado_traduccion)
												WHERE modulos_idioma.id_modulo = '$row->id_modulo'
												AND modulos_idioma.id_idioma = '$datos[idioma]'
												ORDER BY hoteles.hotel, $row->tabla.$row->titulo");
						}else if($row->descripcion==1){
							$query_final = $this->db->query("SELECT 
												$row->tabla.$row->id_tabla as id_tabla, 
												$row->tabla.$row->descripcion as descripcion_tabla,
												modulos_idioma.titulo as titulo_idioma,
												modulos_idioma.descripcion as descripcion_idioma,
												estados_traduccion.estado_traduccion,
												estados_traduccion.label,
												hoteles.hotel,
												modulos_idioma.id_modulo_idioma
												FROM 
												modulos_idioma 
												INNER JOIN
												$row->tabla on(modulos_idioma.id_tabla=$row->tabla.$row->id_tabla)
												INNER JOIN
												hoteles on(hoteles.id_hotel=$row->tabla.id_hotel)
												INNER JOIN
												estados_traduccion on(estados_traduccion.id_estado_traduccion=modulos_idioma.id_estado_traduccion)
												WHERE modulos_idioma.id_modulo = '$row->id_modulo'
												AND modulos_idioma.id_idioma = '$datos[idioma]'
												ORDER BY hoteles.hotel, $row->tabla.$row->titulo");
						}	
					}else{
						if($row->usa_titulo==1 && $row->usa_descripcion==1){
							$query_final = $this->db->query("SELECT 
												$row->tabla.$row->id_tabla as id_tabla, 
												$row->tabla.$row->titulo as titulo_tabla, 
												$row->tabla.$row->descripcion as descripcion_tabla,
												modulos_idioma.titulo as titulo_idioma,
												modulos_idioma.descripcion as descripcion_idioma,
												estados_traduccion.estado_traduccion,
												estados_traduccion.label,
												modulos_idioma.id_modulo_idioma
												FROM 
												modulos_idioma 
												INNER JOIN
												$row->tabla on(modulos_idioma.id_tabla=$row->tabla.$row->id_tabla)
												INNER JOIN
												estados_traduccion on(estados_traduccion.id_estado_traduccion=modulos_idioma.id_estado_traduccion)
												WHERE modulos_idioma.id_modulo = '$row->id_modulo'
												AND modulos_idioma.id_idioma = '$datos[idioma]'
												ORDER BY $row->tabla.$row->titulo");
						}else if($row->usa_titulo==1){
							$query_final = $this->db->query("SELECT 
												$row->tabla.$row->id_tabla as id_tabla, 
												$row->tabla.$row->titulo as titulo_tabla, 
												modulos_idioma.titulo as titulo_idioma,
												modulos_idioma.descripcion as descripcion_idioma,
												estados_traduccion.estado_traduccion,
												estados_traduccion.label,
												modulos_idioma.id_modulo_idioma
												FROM 
												modulos_idioma 
												INNER JOIN
												$row->tabla on(modulos_idioma.id_tabla=$row->tabla.$row->id_tabla)
												INNER JOIN
												estados_traduccion on(estados_traduccion.id_estado_traduccion=modulos_idioma.id_estado_traduccion)
												WHERE modulos_idioma.id_modulo = '$row->id_modulo'
												AND modulos_idioma.id_idioma = '$datos[idioma]'
												ORDER BY $row->tabla.$row->titulo");
						}else if($row->descripcion==1){
							$query_final = $this->db->query("SELECT 
												$row->tabla.$row->id_tabla as id_tabla, 
												$row->tabla.$row->descripcion as descripcion_tabla,
												modulos_idioma.titulo as titulo_idioma,
												modulos_idioma.descripcion as descripcion_idioma,
												estados_traduccion.estado_traduccion,
												estados_traduccion.label,
												modulos_idioma.id_modulo_idioma
												FROM 
												modulos_idioma 
												INNER JOIN
												$row->tabla on(modulos_idioma.id_tabla=$row->tabla.$row->id_tabla)
												INNER JOIN
												estados_traduccion on(estados_traduccion.id_estado_traduccion=modulos_idioma.id_estado_traduccion)
												WHERE modulos_idioma.id_modulo = '$row->id_modulo'
												AND modulos_idioma.id_idioma = '$datos[idioma]'
												ORDER BY $row->tabla.$row->descripcion");
						}	
					}
							
						
					if($query_final->num_rows() > 0){
						foreach ($query_final->result() as $fila){
							$data[] = $fila;
						}
						return $data;
					}else{
						return FALSE;
					}
				}else{
					//echo "no entro";
				}
			}
			
		}else{
			return FALSE;
		}
	}
	
	function updateModulo($datos){
		$this->db->update('modulos_idioma', $datos, array('id_modulo_idioma' => $datos['id_modulo_idioma']));
	}

		
	function getTraducciones($registros, $id_modulo){
		$query_modulo = $this->db->query("SELECT * FROM modulos WHERE id_modulo='$id_modulo'");
		
		if($query_modulo->num_rows() > 0){
			foreach ($query_modulo->result() as $row){
				$tabla			= $row->tabla;
				$id_tabla		= $row->id_tabla;
				$titulo			= $row->titulo;
				$descripcion	= $row->descripcion;
			}
				
			$url_idioma = $this->uri->segment(1);	
			
			$query_idioma = $this->db->query("SELECT * FROM idiomas WHERE url = '$url_idioma'");
				foreach ($query_idioma->result() as $row){
					$id_idioma = $row->id_idioma;
				}		
			
			foreach ($registros as $registro) {
				$id	= $registro->$id_tabla;
				$query_traduccion = $this->db->query("	SELECT * FROM 
														modulos_idioma 
														WHERE id_tabla = '$id' 
														AND id_modulo= '$id_modulo'
														AND id_estado_traduccion=3 
														AND id_idioma='$id_idioma'");
				
				
				if($query_traduccion->num_rows() > 0){
					foreach ($query_traduccion->result() as $row){
						$data['traduccion_titulo'.$id]		= $row->titulo;
						$data['traduccion_descripcion'.$id]	= $row->descripcion;
						}
				}
			
		}
		}	
		
		
		
		if(isset($data)){
			return $data;	
		}else{
			return false;
		}
			
		
	}
			

}
?>
