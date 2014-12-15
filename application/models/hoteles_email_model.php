<?php 
class Hoteles_email_model extends CI_Model {
	
	function getEmails($id_hotel){
		$query_reserva = $this->db->query("	SELECT emails_hotel.email  
									FROM hotel_email_reserva
									INNER JOIN emails_hotel 		ON(hotel_email_reserva.id_email=emails_hotel.id_email) 
									INNER JOIN config_email_reserva ON(hotel_email_reserva.id_config=config_email_reserva.id_config_email_reserva	)
									WHERE config_email_reserva.id_hotel = '$id_hotel'
									AND emails_hotel.mostrar = 1
									");
		
		$query_mensaje = $this->db->query("	SELECT emails_hotel.email  
									FROM hotel_email_mensaje
									INNER JOIN emails_hotel 		ON(hotel_email_mensaje.id_email=emails_hotel.id_email) 
									INNER JOIN config_email_mensaje ON(hotel_email_mensaje.id_config=config_email_mensaje.id_config_email_mensaje	)
									WHERE config_email_mensaje.id_hotel = '$id_hotel'
									AND	emails_hotel.mostrar = 1
									");
			
		if($query_reserva->num_rows() > 0 || $query_reserva->num_rows() > 0){
			$data=array();
			if($query_reserva->num_rows() > 0){				
				foreach ($query_reserva->result() as $fila){
					if (!(in_array($fila, $data))) {						
						$data[] = $fila;
					}
				}
			}
			if($query_mensaje->num_rows() > 0){
				foreach ($query_mensaje->result() as $fila){
					if (!(in_array($fila, $data))) {
						$data[] = $fila;	
					}
					
				}
			}
			return $data;
		}else{
			return FALSE;
		}
	}
	
	
	function getCorreo($huesped, $tarjeta, $reservas, $vuelo, $id_tipo_correo){
		foreach ($reservas as $reserva) {
			$entrada	= $reserva->entrada;
			$salida		= $reserva->salida;
			$adultos	= $reserva->adultos;
			$menores	= $reserva->menores;
			$hotel		= $reserva->hotel;
			$id_hotel	= $reserva->id_hotel;
			$id_nota	= $reserva->id_nota;
			$id_reserva	= $reserva->id_reserva;
			$fecha_alta	= $reserva->fecha_alta;
			$total		= $reserva->total;
		}
		
		$query = $this->db->query("	SELECT *  FROM config_email_reserva
									WHERE config_email_reserva.id_hotel = '$id_hotel'
									AND config_email_reserva.id_tipo_correo = '$id_tipo_correo'
									AND config_email_reserva.delete = 0");

		
		if($query->num_rows() > 0){
			 $row = $query->row(); 
		}
			
		$mensaje = $row->correo;
		//Datos reserva
		$mensaje = str_replace("#hotel#", $hotel, $mensaje);
		$mensaje = str_replace("#entrada#", $entrada, $mensaje);
		$mensaje = str_replace("#salida#", $salida, $mensaje);
		$mensaje = str_replace("#adultos#", $adultos, $mensaje);
		$mensaje = str_replace("#menores#", $menores, $mensaje);
		$mensaje = str_replace("#reserva_numero#", $id_reserva, $mensaje);
		$mensaje = str_replace("#reserva_alta#", date('H:i:s d-m-Y', strtotime($fecha_alta)), $mensaje);
		$mensaje = str_replace("#reserva_precio#", $total, $mensaje);
		//Datos huesped		
		$mensaje = str_replace("#huesped_nombre#", $huesped['nombre'], $mensaje);
		$mensaje = str_replace("#huesped_apellido#", $huesped['apellido'], $mensaje);
		$mensaje = str_replace("#huesped_email#", $huesped['email'], $mensaje);
		$mensaje = str_replace("#huesped_telefono#", $huesped['telefono'], $mensaje);
		$mensaje = str_replace("#huesped_id_tipo_tarjeta#", $tarjeta['id_tipo_tarjeta'], $mensaje);
		//Datos tarjeta
		$mensaje = str_replace("#tarjeta_numero#", $tarjeta['tarjeta'], $mensaje);
		$mensaje = str_replace("#tarjeta_pin#", $tarjeta['pin'], $mensaje);
		$mensaje = str_replace("#tarjeta_vencimiento#", $tarjeta['vencimiento'], $mensaje);
		//Datos vuelo
		if($vuelo){
			$mensaje = str_replace("#vuelo_numero#", $vuelo['nro_vuelo'], $mensaje);
			$mensaje = str_replace("#vuelo_horario_llegada#", $vuelo['horario_llegada'], $mensaje);
			$mensaje = str_replace("#vuelo_aerolinea#", $vuelo['aerolinea'], $mensaje);
		}else{
			$mensaje = str_replace("#vuelo_numero#", "", $mensaje);
			$mensaje = str_replace("#vuelo_horario_llegada#", "", $mensaje);
			$mensaje = str_replace("#vuelo_aerolinea#", "", $mensaje);
		}
				

		$query = $this->db->query("SELECT termino FROM terminos");
		if($query->num_rows() > 0){
			foreach ($query->result() as $fila){
				$terminos = $fila->termino;
				}
			}	
			
		$mensaje = str_replace("#terminos#", $terminos, $mensaje);
	    
	  	$query = $this->db->query("SELECT * FROM notas WHERE notas.id_nota='$id_nota'");
		if($query->num_rows() > 0){
			$notas = $query->row(); 
  			$mensaje = str_replace("#nota#", $notas->nota, $mensaje);
		}else{
			$mensaje = str_replace("#nota#", "", $mensaje);			
		}

  		
	    	
	    foreach ($reservas as $reserva) {
		    $mensaje .="Cantidad: <b>".$reserva->cantidad."</b><br>";
	  		
	    	$mensaje .="Habitación: <b>".$reserva->habitacion."</b><br>";
		}
		
		return $mensaje;
	}
	
	function correoMensaje($consulta, $id_tipo_correo){
		
		$título = $consulta['titulo'];
		
		$query = $this->db->query("	SELECT *  FROM config_email_mensaje
									WHERE config_email_mensaje.id_hotel = '$consulta[id_hotel]'
									AND config_email_mensaje.id_tipo_correo = '$id_tipo_correo'
									AND config_email_mensaje.delete = 0");

		
		if($query->num_rows() > 0){
			 $row = $query->row(); 
		}
		
		
		$mensaje = $row->correo;
		$mensaje = str_replace("#mensaje#", $consulta['mensaje'], $mensaje);
		$mensaje = str_replace("#fecha_envio#", $consulta['fecha_envio'], $mensaje);
		$mensaje = str_replace("#emisor_email#", $consulta['emisor'], $mensaje);
		$mensaje = str_replace("#emisor_nombre#", $consulta['nombre'], $mensaje);
		$mensaje = str_replace("#emisor_apellido#", $consulta['apellido'], $mensaje);
		$mensaje = str_replace("#emisor_telefono#", $consulta['telefono'], $mensaje);
		$mensaje = str_replace("#hotel#", $consulta['hotel'], $mensaje);
  			
		
		// Para enviar un correo HTML
		$cabeceras  = 'MIME-Version: 1.0' . "\r\n";
		$cabeceras .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
		
		$query = $this->db->query("	SELECT * FROM empresas");
		$row = $query->row(); 
		// Cabeceras adicionales
		$cabeceras .= 'From: '.$row->empresa.' <'.$row->email.'>' . "\r\n";
		
		
		if($id_tipo_correo==1){
			$query = $this->db->query("	SELECT emails_hotel.email  
									FROM hotel_email_mensaje
									INNER JOIN emails_hotel 		ON(hotel_email_mensaje.id_email=emails_hotel.id_email) 
									INNER JOIN config_email_mensaje ON(hotel_email_mensaje.id_config=config_email_mensaje.id_config_email_mensaje	)
									WHERE config_email_mensaje.id_hotel = '$consulta[id_hotel]'");
		
			if($query->num_rows() > 0){
				foreach ($query->result() as $fila){
					$para = $fila->email;
					mail($para, $título, $mensaje, $cabeceras);
					
					$data[] = $fila;	
				}
			}
		}else{
			$para = $consulta['emisor'];
			mail($para, $título, $mensaje, $cabeceras);
		}
		
	}
	
	
	function correoReserva($huesped, $tarjeta, $reservas, $precios_array, $vuelo, $id_tipo_correo){
		foreach ($reservas as $reserva) {
			$entrada	= $reserva->entrada;
			$salida		= $reserva->salida;
			$adultos	= $reserva->adultos;
			$menores	= $reserva->menores;
			$hotel		= $reserva->hotel;
			$id_hotel	= $reserva->id_hotel;
			$id_nota	= $reserva->id_nota;
			$id_reserva	= $reserva->id_reserva;
			$fecha_alta	= $reserva->fecha_alta;
			$total		= $reserva->total;
		}
		
		$query = $this->db->query("	SELECT *  FROM config_email_reserva
									WHERE config_email_reserva.id_hotel = '$id_hotel'
									AND config_email_reserva.id_tipo_correo = '$id_tipo_correo'
									AND config_email_reserva.delete = 0");

		
		if($query->num_rows() > 0){
			 $row = $query->row(); 
		}
			
		if(isset($huesped['titulo'])){
			$título = $huesped['titulo'];	
		}else{
			$título = 'Reserva online';	
		}									
		
		$mensaje = $row->correo;
		//Datos reserva
		$mensaje = str_replace("#hotel#", $hotel, $mensaje);
		$mensaje = str_replace("#entrada#", $entrada, $mensaje);
		$mensaje = str_replace("#salida#", $salida, $mensaje);
		$mensaje = str_replace("#adultos#", $adultos, $mensaje);
		$mensaje = str_replace("#menores#", $menores, $mensaje);
		$mensaje = str_replace("#reserva_numero#", $id_reserva, $mensaje);
		$mensaje = str_replace("#reserva_alta#", date('H:i:s d-m-Y', strtotime($fecha_alta)), $mensaje);
		$mensaje = str_replace("#reserva_precio#", $total, $mensaje);
		//Datos huesped		
		$mensaje = str_replace("#huesped_nombre#", $huesped['nombre'], $mensaje);
		$mensaje = str_replace("#huesped_apellido#", $huesped['apellido'], $mensaje);
		$mensaje = str_replace("#huesped_email#", $huesped['email'], $mensaje);
		$mensaje = str_replace("#huesped_telefono#", $huesped['telefono'], $mensaje);
		$mensaje = str_replace("#huesped_id_tipo_tarjeta#", $tarjeta['id_tipo_tarjeta'], $mensaje);
		//Datos tarjeta
		$mensaje = str_replace("#tarjeta_numero#", $tarjeta['tarjeta'], $mensaje);
		$mensaje = str_replace("#tarjeta_pin#", $tarjeta['pin'], $mensaje);
		$mensaje = str_replace("#tarjeta_vencimiento#", $tarjeta['vencimiento'], $mensaje);
		$mensaje = str_replace("#tarjeta_tipo#", $tarjeta['tipo_tarjeta'], $mensaje);
		//Datos vuelo
		if($vuelo){
			$mensaje = str_replace("#vuelo_numero#", $vuelo['nro_vuelo'], $mensaje);
			$mensaje = str_replace("#vuelo_horario_llegada#", $vuelo['horario_llegada'], $mensaje);
			$mensaje = str_replace("#vuelo_aerolinea#", $vuelo['aerolinea'], $mensaje);
		}else{
			$mensaje = str_replace("#vuelo_numero#", "", $mensaje);
			$mensaje = str_replace("#vuelo_horario_llegada#", "", $mensaje);
			$mensaje = str_replace("#vuelo_aerolinea#", "", $mensaje);
		}
				

		$query = $this->db->query("SELECT termino FROM terminos");
		if($query->num_rows() > 0){
			foreach ($query->result() as $fila){
				$terminos = $fila->termino;
				}
			}	
			
		$mensaje = str_replace("#terminos#", $terminos, $mensaje);
	    
	  	$query = $this->db->query("SELECT * FROM notas WHERE notas.id_nota='$id_nota'");
		if($query->num_rows() > 0){
			$notas = $query->row(); 
  			$mensaje = str_replace("#nota#", $notas->nota, $mensaje);
		}else{
			$mensaje = str_replace("#nota#", "", $mensaje);			
		}

  		
		$cant_habitacion	= "";
			    	
	    foreach ($reservas as $reserva) {
		    $cant_habitacion .= $reserva->cantidad." - ".$reserva->habitacion." ";
			$cant_habitacion .= "$ ".$precios_array[$reserva->id_habitacion]."<br>";
		}
		
		$mensaje = str_replace("#cant_habitacion#", $cant_habitacion, $mensaje);
		
				
		
		// Para enviar un correo HTML
		$cabeceras  = 'MIME-Version: 1.0' . "\r\n";
		$cabeceras .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
		
		$query = $this->db->query("	SELECT * FROM empresas");
		$row = $query->row(); 
		// Cabeceras adicionales
		$cabeceras .= 'From: '.$row->empresa.' <'.$row->email.'>' . "\r\n";
		
		if($id_tipo_correo==1){
			$query = $this->db->query("SELECT emails_hotel.email  
									FROM hotel_email_reserva
									INNER JOIN emails_hotel 		ON(hotel_email_reserva.id_email=emails_hotel.id_email) 
									INNER JOIN config_email_reserva ON(hotel_email_reserva.id_config=config_email_reserva.id_config_email_reserva	)
									WHERE config_email_reserva.id_hotel = '$id_hotel'");
			
			if($query->num_rows() > 0){
				foreach ($query->result() as $fila){
					$para = $fila->email;
					mail($para, $título, $mensaje, $cabeceras);				
					$data[] = $fila;	
				}
			}	
		}else{
			$para = $huesped['email'];
			mail($para, $título, $mensaje, $cabeceras);			
		}
		
		return $mensaje;
	}
	
	
	function correoHabitacion($consulta, $habitacion, $id_tipo_correo){
		
		$título = $consulta['titulo'];
		
		$query = $this->db->query("	SELECT *  FROM config_email_habitacion
									WHERE config_email_habitacion.id_hotel = '$consulta[id_hotel]'
									AND config_email_habitacion.id_tipo_correo = '$id_tipo_correo'
									AND config_email_habitacion.delete = 0");

		if($query->num_rows() > 0){
			 $row = $query->row(); 
		}
		
		
		$mensaje = $row->correo;
		$mensaje = str_replace("#mensaje#", $consulta['mensaje'], $mensaje);
		$mensaje = str_replace("#fecha_envio#", $consulta['fecha_envio'], $mensaje);
		$mensaje = str_replace("#emisor_email#", $consulta['emisor'], $mensaje);
		$mensaje = str_replace("#emisor_nombre#", $consulta['nombre'], $mensaje);
		$mensaje = str_replace("#emisor_apellido#", $consulta['apellido'], $mensaje);
		$mensaje = str_replace("#hotel#", $consulta['hotel'], $mensaje);
		$mensaje = str_replace("#habitacion#", "<a href='".base_url()."index.php/habitacion/view/".$habitacion['id_habitacion']."/".$consulta['id_hotel']."'>".$habitacion['habitacion']."</a>", $mensaje);
			
		// Para enviar un correo HTML
		$cabeceras  = 'MIME-Version: 1.0' . "\r\n";
		$cabeceras .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
		
		$query = $this->db->query("	SELECT * FROM empresas");
		$row = $query->row(); 
		// Cabeceras adicionales
		$cabeceras .= 'From: '.$row->empresa.' <'.$row->email.'>' . "\r\n";
		
		
		if($id_tipo_correo==1){
			$query = $this->db->query("	SELECT emails_hotel.email  
									FROM hotel_email_habitacion
									INNER JOIN emails_hotel 		ON(hotel_email_habitacion.id_email=emails_hotel.id_email) 
									INNER JOIN config_email_habitacion ON(hotel_email_habitacion.id_config=config_email_habitacion.id_config_email_habitacion	)
									WHERE config_email_habitacion.id_hotel = '$consulta[id_hotel]'");
		
			if($query->num_rows() > 0){
				foreach ($query->result() as $fila){
					$para = $fila->email;
					mail($para, $título, $mensaje, $cabeceras);
					
					$data[] = $fila;	
				}
			}
		}else{
			$para = $consulta['emisor'];
			mail($para, $título, $mensaje, $cabeceras);
		}
		
	}

} 
?>