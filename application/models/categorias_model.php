<?php 
class Categorias_model extends CI_Model {
	
	function getCategoria($id){
		$query = $this->db->query("SELECT * FROM categorias 
									WHERE categorias.delete = 0 AND
									categorias.id_categoria = '$id'");
		
		if($query->num_rows() > 0){
			foreach ($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}else{
			return FALSE;
		}
	}
	
	
	function getCategorias($id_hotel=NULL){
		$date=date("Y-m-d");
		$url_idioma = $this->uri->segment(1);
		$id			= $id_hotel;
		$query = $this->db->query("SELECT 
									categorias.categoria,
									categorias.id_categoria 
									FROM articulos 
									INNER JOIN categorias ON(articulos.id_categoria=categorias.id_categoria)
									INNER JOIN idiomas ON(articulos.id_idioma=idiomas.id_idioma)
									WHERE articulos.delete = 0 AND
									articulos.id_estado_articulo != 2 AND
									DATE_FORMAT(articulos.fecha_publicacion, '%Y-%m-%d') <= '$date' AND
									(DATE_FORMAT(articulos.fecha_publicacion, '%Y-%m-%d') >= '$date' OR 
									articulos.fecha_despublicacion=0 )
									AND articulos.id_hotel='$id'
									GROUP BY categorias.categoria
									ORDER BY articulos.id_articulo
									");	

		
		
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
