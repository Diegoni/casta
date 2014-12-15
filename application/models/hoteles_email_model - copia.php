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
	
	function correoMensaje($consulta, $id_tipo_correo){
		
		$título = $consulta['titulo'];
		
		$query = $this->db->query("	SELECT *  FROM config_email_mensaje
									WHERE config_email_mensaje.id_hotel = '$consulta[id_hotel]'
									AND config_email_mensaje.id_tipo_correo = '$id_tipo_correo'
									AND config_email_mensaje.delete = 0");

		
		if($query->num_rows() > 0){
			 $row = $query->row(); 
		}
		
		
		$mensaje = $row->correo."<br>";
		$mensaje .= 
		"
		<html>
		<head>
		<style type='text/css'>
		table.gridtable {
			font-family: verdana,arial,sans-serif;
			font-size:11px;
			color:#333333;
			border-width: 1px;
			border-color: #666666;
			border-collapse: collapse;
		}
		table.gridtable th {
			border-width: 1px;
			padding: 8px;
			border-style: solid;
			border-color: #666666;
			background-color: #dedede;
		}
		table.gridtable td {
			border-width: 1px;
			padding: 8px;
			border-style: solid;
			border-color: #666666;
			background-color: #ffffff;
		}
		</style>".
			header('Content-type: text/html; charset=utf-8')." 
  			<title>".$título."</title>
		</head>
		<body>
  			<table class='gridtable'>";
  		
  		if($row->mensaje==1){
  			$mensaje .="
  			<tr>
	      		<td>Mensaje: </td>
	      		<th>".$consulta['mensaje']."</th>
	    	</tr>";
  		}
  		
		if($row->fecha==1){
  			$mensaje .="
  			<tr>
	      		<td>Fecha: </td>
	      		<th>".date("H:i:s d-m-Y", strtotime($consulta['fecha_envio']))."</th>
	    	</tr>";
  		}
  		
  		if($row->email==1){
  			$mensaje .="
  			<tr>
	      		<td>Email: </td>
	      		<th>".$consulta['emisor']."</th>
	    	</tr>";
  		}
  		
  		if($row->nombre==1){
  			$mensaje .="
  			<tr>
	      		<td>Nombre: </td>
	      		<th>".$consulta['nombre']."</th>
	    	</tr>";
  		}
  		
  		if($row->apellido==1){
  			$mensaje .="
  			<tr>
	      		<td>Apellido: </td>
	      		<th>".$consulta['apellido']."</th>
	    	</tr>";
  		}
  		
  		if($row->telefono==1){
  			$mensaje .="
  			<tr>
	      		<td>Telefono: </td>
	      		<th>".$consulta['telefono']."</th>
	    	</tr>";
  		}
  		
  		if($row->hotel==1){
  			$mensaje .="
  			<tr>
	      		<td>Hotel: </td>
	      		<th>".$consulta['id_hotel']."</th>
	    	</tr>";
  		}
  		
  		$mensaje .="
  			</table>
		</body>
		</html>
		";
		
	
		
		// Para enviar un correo HTML
		$cabeceras  = 'MIME-Version: 1.0' . "\r\n";
		$cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		
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
	
	
	function correoReserva($huesped, $tarjeta, $reservas, $vuelo, $id_tipo_correo){
		foreach ($reservas as $reserva) {
			$entrada=$reserva->entrada;
			$salida=$reserva->salida;
			$adultos=$reserva->adultos;
			$menores=$reserva->menores;
			$hotel=$reserva->hotel;
			$id_hotel=$reserva->id_hotel;
			$id_nota=$reserva->id_nota;
			$id_reserva=$reserva->id_reserva;
			$fecha_alta=$reserva->fecha_alta;
			$total=$reserva->total;
		}
		
		$query = $this->db->query("	SELECT *  FROM config_email_reserva
									WHERE config_email_reserva.id_hotel = '$id_hotel'
									AND config_email_reserva.id_tipo_correo = '$id_tipo_correo'
									AND config_email_reserva.delete = 0");

		
		if($query->num_rows() > 0){
			 $row = $query->row(); 
		}
			
											
		$título = 'Reserva online';
		$mensaje = $row->correo."<br>";
		$mensaje .= 
		"
		<html>
		<head>
		<style type='text/css'>
		table.gridtable {
			font-family: verdana,arial,sans-serif;
			font-size:11px;
			color:#333333;
			border-width: 1px;
			border-color: #666666;
			border-collapse: collapse;
		}
		table.gridtable th {
			border-width: 1px;
			padding: 8px;
			border-style: solid;
			border-color: #666666;
			background-color: #dedede;
		}
		table.gridtable td {
			border-width: 1px;
			padding: 8px;
			border-style: solid;
			border-color: #666666;
			background-color: #ffffff;
		}
		</style>".
			header('Content-type: text/html; charset=utf-8')." 
  			<title>".$título."</title>
		</head>
		<body>
  			<table class='gridtable'>";
  		if($row->hotel==1){
  			$mensaje .="
  			<tr>
	      		<td>Hotel: </td>
	      		<th>".$hotel."</th>
	    	</tr>";
  		}
		
		if($row->entrada==1){
  			$mensaje .="
  			<tr>
	      		<td>Entrada: </td>
	      		<th>".date("d-m-Y", strtotime($entrada))."</th>
	    	</tr>";
  		}
		
		if($row->salida==1){
  			$mensaje .="
  			<tr>
	      		<td>Salida: </td>
	      		<th>".date("d-m-Y", strtotime($salida))."</th>
	    	</tr>";
  		}
  		
  		if($row->adultos==1){
  			$mensaje .="
  			<tr>
	      		<td>Adultos: </td>
	      		<th>".$adultos."</th>
	    	</tr>";
  		}
  		
  		if($row->menores==1){
  			$mensaje .="
  			<tr>
	      		<td>Adultos: </td>
	      		<th>".$menores."</th>
	    	</tr>";
  		}
  		
		if($row->nombre==1){
  			$mensaje .="
  			<tr>
	      		<td>Nombre: </td>
	      		<th>".$huesped['nombre']."</th>
	    	</tr>";
  		}
		
		if($row->apellido==1){
  			$mensaje .="
  			<tr>
	      		<td>Apellido: </td>
	      		<th>".$huesped['apellido']."</th>
	    	</tr>";
  		}
		
		if($row->email==1){
  			$mensaje .="
  			<tr>
	      		<td>Email: </td>
	      		<th>".$huesped['email']."</th>
	    	</tr>";
  		}
		
		if($row->telefono==1){
  			$mensaje .="
  			<tr>
	      		<td>Teléfono: </td>
	      		<th>".$huesped['telefono']."</th>
	    	</tr>";
  		}
		
		if($row->tipo_tarjeta==1){
  			$mensaje .="
  			<tr>
	      		<td>Tipo tarjeta: </td>
	      		<th>".$tarjeta['id_tipo_tarjeta']."</th>
	    	</tr>";
  		}
		
		if($row->tarjeta==1){
  			$mensaje .="
  			<tr>
	      		<td>Tarjeta: </td>
	      		<th>".$tarjeta['tarjeta']."</th>
	    	</tr>";
  		}
		
		if($row->pin==1){
  			$mensaje .="
  			<tr>
	      		<td>Pin: </td>
	      		<th>".$tarjeta['pin']."</th>
	    	</tr>";
  		}
  		
  		if($row->vencimiento==1){
  			$mensaje .="
  			<tr>
	      		<td>Vencimiento: </td>
	      		<th>".$tarjeta['vencimiento']."</th>
	    	</tr>";
  		}
  		
  		if($row->nro_de_vuelo==1){
  			$mensaje .="
  			<tr>
	      		<td>Número de vuelo: </td>
	      		<th>".$vuelo['nro_vuelo']."</th>
	    	</tr>";
  		}
		
		if($row->horario_llegada==1){
  			$mensaje .="
  			<tr>
	      		<td>Horario de llegada: </td>
	      		<th>".date("H:i", strtotime($vuelo['horario_llegada']))."</th>
	    	</tr>";
  		}
  		
  		if($row->aerolinea==1){
  			$mensaje .="
  			<tr>
	      		<td>Aerolínea: </td>
	      		<th>".$vuelo['id_aerolinea']."</th>
	    	</tr>";
  		}
		
		if($row->id_nota==1){
			$query = $this->db->query("SELECT * FROM notas WHERE notas.id_nota='$id_nota'");
			if($query->num_rows() > 0){
			$notas = $query->row(); 
  			$mensaje .="
  			<tr>
	      		<td>Nota: </td>
	      		<th>".$notas->nota."</th>
	    	</tr>";
			}
  		}
  		
  		if($row->id_reserva==1){
  			$mensaje .="
  			<tr>
	      		<td>Reserva nro: </td>
	      		<th>".$id_reserva."</th>
	    	</tr>";
  		}
  		
	    	
	    foreach ($reservas as $reserva) {
		    if($row->cantidad==1){
	  			$mensaje .="
	  			<tr>
		      		<td>Cantidad: </td>
		      		<th>".$reserva->cantidad."</th>
		    	</tr>";
	  		}
	    	
	    	if($row->habitacion==1){
	  			$mensaje .="
	  			<tr>
		      		<td>Habitación: </td>
		      		<th>".$reserva->habitacion."</th>
		    	</tr>";
	  		}
		}
		
		if($row->fecha==1){
	  			$mensaje .="
	  			<tr>
		      		<td>Fecha: </td>
		      		<th>".date('H:i:s d-m-Y', strtotime($fecha_alta))."</th>
		    	</tr>";
	  		}
		
		if($row->precio==1){
	  			$mensaje .="
	  			<tr>
		      		<td>Precio total: </td>
		      		<th>".$total."</th>
		    	</tr>";
	  		}
		
		if($id_tipo_correo!=1){
			$query = $this->db->query("SELECT termino FROM terminos");
			
			if($query->num_rows() > 0){
				foreach ($query->result() as $fila){
					$terminos = $fila->termino;
				}
			}	
			
			$mensaje .="
	  			<tr>
		      		<td>Terminos: </td>
		      		<th>".$terminos."</th>
		    	</tr>";
	  		}
	    
	    $mensaje.= 
	  		"</table>
		</body>
		</html>
		";
		
		// Para enviar un correo HTML
		$cabeceras  = 'MIME-Version: 1.0' . "\r\n";
		$cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		
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
		
		
		$mensaje = $row->correo."<br>";
		$mensaje .= 
		"
		<html>
		<head>
		<style type='text/css'>
		table.gridtable {
			font-family: verdana,arial,sans-serif;
			font-size:11px;
			color:#333333;
			border-width: 1px;
			border-color: #666666;
			border-collapse: collapse;
		}
		table.gridtable th {
			border-width: 1px;
			padding: 8px;
			border-style: solid;
			border-color: #666666;
			background-color: #dedede;
		}
		table.gridtable td {
			border-width: 1px;
			padding: 8px;
			border-style: solid;
			border-color: #666666;
			background-color: #ffffff;
		}
		</style>".
			header('Content-type: text/html; charset=utf-8')." 
  			<title>".$título."</title>
		</head>
		<body>
  			<table class='gridtable'>";
  		
  		if($row->mensaje==1){
  			$mensaje .="
  			<tr>
	      		<td>Mensaje: </td>
	      		<th>".$consulta['mensaje']."</th>
	    	</tr>";
  		}
  		
		if($row->fecha==1){
  			$mensaje .="
  			<tr>
	      		<td>Fecha: </td>
	      		<th>".date("H:i:s d-m-Y", strtotime($consulta['fecha_envio']))."</th>
	    	</tr>";
  		}
  		
  		if($row->email==1){
  			$mensaje .="
  			<tr>
	      		<td>Email: </td>
	      		<th>".$consulta['emisor']."</th>
	    	</tr>";
  		}
  		
  		if($row->nombre==1){
  			$mensaje .="
  			<tr>
	      		<td>Nombre: </td>
	      		<th>".$consulta['nombre']."</th>
	    	</tr>";
  		}
  		
  		if($row->apellido==1){
  			$mensaje .="
  			<tr>
	      		<td>Apellido: </td>
	      		<th>".$consulta['apellido']."</th>
	    	</tr>";
  		}
  		
  		
  		if($row->hotel==1){
  			$mensaje .="
  			<tr>
	      		<td>Hotel: </td>
	      		<th>".$consulta['id_hotel']."</th>
	    	</tr>";
  		}
  		
  		if($row->habitacion==1){
  			$mensaje .="
  			<tr>
	      		<td>Habitacion: </td>
	      		<th><a href='".base_url()."index.php/habitacion/view/".$habitacion['id_habitacion']."'>".$habitacion['habitacion']."</a></th>
	    	</tr>";
  		}
  		
  		$mensaje .="
  			</table>
		</body>
		</html>
		";
		
	
		
		// Para enviar un correo HTML
		$cabeceras  = 'MIME-Version: 1.0' . "\r\n";
		$cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		
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
