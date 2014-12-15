<?php 
class Articulos_model extends CI_Model {
	
	function getArticulos($datos=NULL){
		$date=date("Y-m-d");
		$url_idioma = $this->uri->segment(1);
		$id_hotel	= $_COOKIE['id_hotel'];
		if(isset($datos)){
			$query = $this->db->query("SELECT * FROM articulos 
									INNER JOIN categorias ON(articulos.id_categoria=categorias.id_categoria)
									INNER JOIN idiomas ON(articulos.id_idioma=idiomas.id_idioma)
									WHERE 
									articulos.id_hotel='$id_hotel' AND
									articulos.delete = 0 AND
									articulos.id_estado_articulo != 2 AND
									DATE_FORMAT(articulos.fecha_publicacion, '%Y-%m-%d') <= '$date' AND
									(DATE_FORMAT(articulos.fecha_publicacion, '%Y-%m-%d') >= '$date' OR 
									articulos.fecha_despublicacion=0 ) AND
									articulos.$datos[columna] = '$datos[dato]'
									AND articulos.id_tipo = 1
									ORDER BY articulos.orden");
			
		}else{
			$query = $this->db->query("SELECT * FROM articulos 
									INNER JOIN categorias ON(articulos.id_categoria=categorias.id_categoria)
									INNER JOIN idiomas ON(articulos.id_idioma=idiomas.id_idioma)
									WHERE 
									articulos.id_hotel='$id_hotel' AND
									articulos.delete = 0 AND
									articulos.id_estado_articulo != 2 AND
									DATE_FORMAT(articulos.fecha_publicacion, '%Y-%m-%d') <= '$date' AND
									(DATE_FORMAT(articulos.fecha_publicacion, '%Y-%m-%d') >= '$date' OR 
									articulos.fecha_despublicacion=0 )
									AND articulos.id_tipo = 1
									ORDER BY articulos.id_articulo");	
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
	
	
	
	
	function getArticulos_paginaprincipal($id_hotel=NULL){
		$date		= date("Y-m-d");
		$url_idioma = $this->uri->segment(1);
		$query = $this->db->query("SELECT * FROM articulos 
									INNER JOIN categorias ON(articulos.id_categoria=categorias.id_categoria)
									INNER JOIN idiomas ON(articulos.id_idioma=idiomas.id_idioma)
									WHERE 
									articulos.id_hotel='$id_hotel' AND
									articulos.delete = 0 AND
									articulos.id_estado_articulo != 2 AND
									DATE_FORMAT(articulos.fecha_publicacion, '%Y-%m-%d') <= '$date' AND
									(DATE_FORMAT(articulos.fecha_publicacion, '%Y-%m-%d') >= '$date' OR 
									articulos.fecha_despublicacion=0 ) AND
									articulos.pagina_principal = 1 
									AND articulos.id_tipo = 1
									ORDER BY articulos.id_articulo");
		// AND (articulos.id_idioma = 0 OR idiomas.url = '$url_idioma' )
				
		if($query->num_rows() > 0){
			foreach ($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}else{
			return FALSE;
		}
	}
	
	
	function getBanner($datos=NULL){
		$date		= date("Y-m-d");
		$url_idioma = $this->uri->segment(1);
		$id_hotel	= $_COOKIE['id_hotel'];
		if(isset($datos)){
			$query = $this->db->query("SELECT * FROM articulos 
									INNER JOIN categorias ON(articulos.id_categoria=categorias.id_categoria)
									INNER JOIN idiomas ON(articulos.id_idioma=idiomas.id_idioma)
									WHERE 
									articulos.id_hotel='$id_hotel' AND
									articulos.delete = 0 AND
									articulos.id_estado_articulo != 2 AND
									DATE_FORMAT(articulos.fecha_publicacion, '%Y-%m-%d') <= '$date' AND
									(DATE_FORMAT(articulos.fecha_publicacion, '%Y-%m-%d') >= '$date' OR 
									articulos.fecha_despublicacion=0 ) AND
									(articulos.id_idioma = 0 OR idiomas.url = '$url_idioma' )
									AND articulos.id_tipo = '$datos[id_tipo]'
									ORDER BY articulos.fecha_publicacion");
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
