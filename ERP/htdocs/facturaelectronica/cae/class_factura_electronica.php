<?php
# Ejemplo de Uso de Interface COM con Web Services AFIP (PyAfipWs) para PHP
# WSFEv1 2.5 (factura electrónica mercado interno sin detalle -régimen general-)
# RG2485 RG2485/08 RG2757/10 RG2904/10 RG3067/11 RG3571/13 RG3668/14 RG3749/15
# 2015 (C) Mariano Reingart <reingart@gmail.com> licencia AGPLv3+
#
# Documentación:
#  * http://www.sistemasagiles.com.ar/trac/wiki/ProyectoWSFEv1
#  * http://www.sistemasagiles.com.ar/trac/wiki/ManualPyAfipWs
#
# Instalación: agregar en el php.ini las siguientes lineas (sin #)
# [COM_DOT_NET] 
# extension=ext\php_com_dotnet.dll 

require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';


class factura_electronica extends CommonObject
{
	var $ambiente		= 'homologacion';	
	//var $ambiente		= 'produccion';		
	var $CACHE 			= '';				// directorio para archivos temporales (usar por defecto)
	
	var $cuil			= "27037544863";
	
	# Certificado: certificado es el firmado por la AFIP
	var $certificado	= "nosotros.crt"; 	// certificado de prueba
	# ClavePrivada: la clave privada usada para crear el certificado
	var $clave_privada	= "nosotros.key";	// clave privada de prueba
	var $path			= 'C:\\xampp2\\htdocs\\casta\\ERP\\htdocs\\facturaelectronica\\cae\\'; 			// Especificar la ubicacion de los archivos certificado y clave privada
	
	var $homo_web_ser	= "https://wsaahomo.afip.gov.ar/ws/services/LoginCms";
	var $homo_wsfev1	= "https://wswhomo.afip.gov.ar/wsfev1/service.asmx?WSDL";	
	var $prod_web_ser	= "https://wsaa.afip.gov.ar/ws/services/LoginCms"; # producción
	var $prod_wsfev1	= "https://servicios1.afip.gov.ar/wsfev1/service.asmx?WSDL";
	
	var $request		= "request.xml";
	var	$response		= "response.xml";
	
	var	$table			= "tms_cae";
	
	function __construct($db)
	{
		$this->db = $db;
	}
	
	
/*----------------------------------------------------------------------------
		Validación de datos
----------------------------------------------------------------------------*/
	
	
	function insert($datos)
	{	
		$campos		= "";
		$valores	= "";
		
		foreach ($datos as $key => $value) {
			$campos		.= "`".$key."` ,"; 
			$valores	.= "'".$value."' ,";		
		}
		
		$campos		= substr($campos, 0, -1);
		$valores	= substr($valores, 0, -1);
		
		$sql = 
			"INSERT INTO `$this->table`(
				$campos
			)VALUES	(
				$valores
			);";
							
		$this->db->query($sql);
	}
	
/*----------------------------------------------------------------------------
		Validación de datos
----------------------------------------------------------------------------*/
	
	function validation_form($dato, $validacion)
	{
		$dato = str_replace("'", '', $dato);
		
		foreach ($validacion as $value)
		{
			$value_case = condicion($value);
			
			switch ($value_case) {
				case 'required':
					if(!(isset($dato)) || $dato == '' || $dato == NULL)
					{
						$bandera[] = 'required';
					}
					break;
					
				case 'number':
					if(!(is_numeric($dato)))
					{
						var_dump(is_numeric($dato));
						$bandera[] = 'float';
					}
					break;
					
				case 'min_length':
					$cantidad = str_replace("min_length[", '', $value);
					$cantidad = str_replace("]", '', $cantidad);
					if(strlen($dato) < $cantidad)
					{
						$bandera[] = 'min_length';
					}
					break;
					
				case 'max_length':
					$cantidad = str_replace("max_length[", '', $value);
					$cantidad = str_replace("]", '', $cantidad);
					if(strlen($dato) > $cantidad)
					{
						$bandera[] = 'min_length';
					}
					break;
					
				case 'length':
					$cantidad = str_replace("length[", '', $value);
					$cantidad = str_replace("]", '', $cantidad);
					if(strlen($dato) != $cantidad)
					{
						$bandera[] = 'length';
					}
					break;
					
				case 'date':
					$fecha = str_replace("date[", '', $value);
					$fecha = str_replace("]", '', $fecha);
					if(!(validateDate($dato, $fecha)))
					{
						$bandera[] = 'date';	
					}
					break;								
			}
		}
	
		if(isset($bandera))
		{
			return $bandera;
		}	
		else
		{
			return TRUE;	
		}
	}
	
	
/*----------------------------------------------------------------------------
		Controla si los datos de la factura
----------------------------------------------------------------------------*/
	
	function control_factura($factura)
	{
		$validacion = array(
			'tipo_cbte' 		=> $this->validation_form($factura['tipo_cbte'],		array('int', 'length[11]', 'required')),
			'punto_vta' 		=> $this->validation_form($factura['punto_vta'],		array('int', 'length[11]', 'required')),	
			'concepto'			=> $this->validation_form($factura['concepto'],		array('int', 'length[11]', 'required')),
			'tipo_doc'			=> $this->validation_form($factura['tipo_doc'],		array('int', 'length[11]', 'required')),
			'nro_doc'			=> $this->validation_form($factura['nro_doc'],			array('int', 'length[11]', 'required')),
			'imp_total'			=> $this->validation_form($factura['imp_total'],		array('int', 'length[11]', 'required')),
			'imp_tot_conc'		=> $this->validation_form($factura['imp_tot_conc'],	array('int', 'length[11]', 'required')),
			'imp_neto'			=> $this->validation_form($factura['imp_neto'],		array('int', 'length[11]', 'required')),
			'imp_iva'			=> $this->validation_form($factura['imp_iva'],			array('int', 'length[11]', 'required')),
			'imp_trib'			=> $this->validation_form($factura['imp_trib'],		array('int', 'length[11]', 'required')),
			'imp_op_ex'			=> $this->validation_form($factura['imp_op_ex'],		array('int', 'length[11]', 'required')),
			'fecha_cbte'		=> $this->validation_form($factura['fecha_cbte'],		array('int', 'length[11]', 'required')),
			'fecha_venc_pago'	=> $this->validation_form($factura['fecha_venc_pago'],	array('int', 'length[11]', 'required')),
			'fecha_serv_desde'	=> $this->validation_form($factura['fecha_serv_desde'],array('int', 'length[11]', 'required')),
			'fecha_serv_hasta'	=> $this->validation_form($factura['fecha_serv_hasta'],array('int', 'length[11]', 'required')),
			'moneda_id'			=> $this->validation_form($factura['moneda_id'],		array('int', 'length[11]', 'required')),
			'moneda_ctz'		=> $this->validation_form($factura['moneda_ctz'],		array('int', 'length[11]', 'required')),
		);
		
		foreach ($validacion as $key => $value) 
		{
			if($value !== TRUE)
			{
				foreach ($value as $error) 
				{
					$log_error[$key]		= $error;
				}
					
			}
		}
		
		if(isset($log_error))
		{
			return $log_error;
		}
		else
		{
			return TRUE;	
		}

	}
	
/*----------------------------------------------------------------------------
		Web Service con la AFip, devuelve el CAE si todo ok
----------------------------------------------------------------------------*/
	
	function obtener_cae($factura, $agregariva)
	{
		//$control = $this->control_factura($factura);
		$control = 1;
		if(is_array($control))
		{
			return $control;
		}
		else
		{
			try 
			{
				# Crear objeto interface Web Service Autenticación y Autorización
				$WSAA			= new COM('WSAA'); 
				# Generar un Ticket de Requerimiento de Acceso (TRA)
				$tra			= $WSAA->CreateTRA() ;
				
				# Especificar la ubicacion de los archivos certificado y clave privada
				//$path			= getcwd()  . $this->path;
							
				# Generar el mensaje firmado (CMS) ;
				//echo $this->clave_privada."<br>";
				$cms			= $WSAA->SignTRA($tra, $this->path . $this->certificado, $this->path . $this->clave_privada);
			
			    # iniciar la conexión al webservice de autenticación
				if ($this->ambiente == 'homologacion')
			        $wsdl 		= $homo_web_ser;
			    else
			        $wsdl 		= $prod_web_ser;
			        
				$ok 			= $WSAA->Conectar($CACHE, $wsdl);
				
				# Llamar al web service para autenticar
				$ta 			= $WSAA->LoginCMS($cms);
				
				//echo "Token de Acceso: $WSAA->Token <br>";
				//echo "Sing de Acceso: $WSAA->Sign <br>";
				
				# Crear objeto interface Web Service de Factura Electrónica v1 (version 2.5)
				$WSFEv1 		= new COM('WSFEv1');
				
				# Setear tocken y sing de autorización (pasos previos) Y CUIT del emisor
				$WSFEv1->Token	= $WSAA->Token;
				$WSFEv1->Sign	= $WSAA->Sign; 
				$WSFEv1->Cuit	= $this->cuil;
				
				# Conectar al Servicio Web de Facturación: homologación testing o producción
				if ($this->ambiente == 'homologacion')
			    	$wsdl		= $homo_wsfev1;
				else
			    	$wsdl		= $prod_wsfev1;
				
				$ok				= $WSFEv1->Conectar($CACHE, $wsdl); // pruebas
				
				#$ok = WSFE.Conectar() ' producción # producción
				
				# Llamo a un servicio nulo, para obtener el estado del servidor (opcional)
				$WSFEv1->Dummy();
				//echo "appserver status $WSFEv1->AppServerStatus <br>";
				//echo "dbserver status $WSFEv1->DbServerStatus <br>";
				//echo "authserver status $WSFEv1->AuthServerStatus <br>";
					
				# Recupero último número de comprobante para un punto venta/tipo (opcional)
				
				$ult = $WSFEv1->CompUltimoAutorizado($factura['tipo_cbte'], $factura['punto_vta']);
				
				$factura['cbt_desde']			= $ult + 1; 
				$factura['cbt_hasta']			= $ult + 1;
				
				# Inicializo la factura interna con los datos de la cabecera
				$ok = $WSFEv1->CrearFactura(
					$factura['concepto'], 
					$factura['tipo_doc'], 
					$factura['nro_doc'], 
			    	$factura['tipo_cbte'], 
			    	$factura['punto_vta'], 
			    	$factura['cbt_desde'],
			    	$factura['cbt_hasta'], 
			    	$factura['imp_total'],
			    	$factura['imp_tot_conc'], 
			    	$factura['imp_neto'], 
			    	$factura['imp_iva'],
			    	$factura['imp_trib'],
			    	$factura['imp_op_ex'],
			    	$factura['fecha_cbte'],
			    	$factura['fecha_venc_pago'], 
			    	$factura['fecha_serv_desde'],
			    	$factura['fecha_serv_hasta'],
		        	$factura['moneda_id'],
		        	$factura['moneda_ctz']);					
			        
			    # Agrego los comprobantes asociados (solo para notas de crédito y débito):
			    if (false) 
			    {
			        $tipo		= 19;
			        $pto_vta	= 2;
			        $nro		= 1234;
			        $ok			= $WSFEv1->AgregarCmpAsoc($tipo, $pto_vta, $nro);
			    }
			        
			    # Agrego impuestos varios
			    $tributo_id		= 99;
			    $ds				= "Impuesto Municipal Matanza'";
			    $base_imp		= "100.00";
			    $alic			= "0.10";
			    $importe		= "0.10";
			    $ok				= $WSFEv1->AgregarTributo($tributo_id, $ds, $base_imp, $alic, $importe);
			
			    # Agrego impuestos varios
			    $tributo_id		= 4;
			    $ds				= "Impuestos internos";
			    $base_imp		= "100.00";
			    $alic			= "0.40";
			    $importe		= "0.40";
			    $ok				= $WSFEv1->AgregarTributo($tributo_id, $ds, $base_imp, $alic, $importe);
			
			    # Agrego impuestos varios
			    $tributo_id		= 1;
			    $ds				= "Impuesto nacional";
			    $base_imp		= "50.00";
			    $alic			= "1.00";
			    $importe		= "0.50";
			    $ok				= $WSFEv1->AgregarTributo($tributo_id, $ds, $base_imp, $alic, $importe);
				
				foreach ($agregariva as $iva_id => $values) 
				{
					# Agrego tasas de IVA
				    $ok	= $WSFEv1->AgregarIva($iva_id, $values['base_imp'], $values['importe']);
				}
				
			    # Agrego datos opcionales  RG 3668 Impuesto al Valor Agregado - Art.12 
			    # ("presunción no vinculación la actividad gravada", F.8001):
			    if ($tipo_cbte == 1) 
			    {  # solo para facturas A
			        # IVA Excepciones (01: Locador/Prestador, 02: Conferencias, 03: RG 74, 04: Bienes de cambio, 05: Ropa de trabajo, 06: Intermediario).
			        $ok = $WSFEv1->AgregarOpcional(5, "02");
			        # Firmante Doc Tipo (80: CUIT, 96: DNI, etc.)
			        $ok = $WSFEv1->AgregarOpcional(61, "80");
			        # Firmante Doc Nro:
			        $ok = $WSFEv1->AgregarOpcional(62, "20267565393");
			        # Carácter del Firmante (01: Titular, 02: Director/Presidente, 03: Apoderado, 04: Empleado)
			        $ok = $WSFEv1->AgregarOpcional(7, "01");
			    }
			    # proximamente más valores opcionales para RG 3749/2015
			    
			    # Habilito reprocesamiento automático (predeterminado):
			    $WSFEv1->Reprocesar = true;
			        
				# Llamo al WebService de Autorización para obtener el CAE
				$cae			= $WSFEv1->CAESolicitar();
						
				$respuesta = array(
					'resultado'		=> $WSFEv1->Resultado,
					'cbtenro'		=> $WSFEv1->CbteNro,
					'cae'			=> $cae,
					'vencimiento'	=> $WSFEv1->Vencimiento,
					'emisiontipo'	=> $WSFEv1->EmisionTipo,
					'reproceso'		=> $WSFEv1->Reproceso,
					'errmsg'		=> $WSFEv1->ErrMsg	  	
				);
				
				# Verifico que no haya rechazo o advertencia al generar el CAE
				if ($cae == "") 
				{
					$respuesta['error'] = "La página esta caida o la respuesta es inválida";
				}
				else
				if ($cae == "NULL" || $WSFEv1->Resultado != "A") 
				{
					$respuesta['error'] = "No se asignó CAE (Rechazado). Motivos: $WSFEv1->Motivo";
				}
				else
				if ($WSFEv1->Obs != "") 
				{
					$respuesta['obs'] = "Se asignó CAE pero con advertencias. Motivos: $WSFEv1->Obs";
				} 
			
			} 
			catch (Exception $e) 
			{
				$respuesta['error'] = 'Excepción: '.$e->getMessage();
				
				if (isset($WSAA)) 
				{
				    $respuesta['error'] .= "WSAA.Excepcion: $WSAA->Excepcion ";
				    $respuesta['error'] .= "WSAA.Traceback: $WSAA->Traceback ";
				}
				
				if (isset($WSFEv1)) 
				{
				    $respuesta['error'] .= "WSFEv1.Excepcion: $WSFEv1->Excepcion ";
				    $respuesta['error'] .= "WSFEv1.Traceback: $WSFEv1->Traceback ";
				}
			}
			if (isset($WSFEv1)) 
			{
			    # almacenar la respuesta para depuración / testing
			    # (guardar en un directorio no descargable al subir a un servidor web)
			    $mascara = $ult+1;
				$mascara .= date('-Y_m_d');
			    file_put_contents($this->path.$mascara.$this->request,  $WSFEv1->XmlRequest);
			    file_put_contents($this->path.$mascara.$this->response, $WSFEv1->XmlResponse);
			}
			
			return $respuesta;	
		}
	}
} 

?>