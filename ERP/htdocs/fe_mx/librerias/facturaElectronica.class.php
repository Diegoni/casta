<?php
require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';
define('FE_MX_URL', '/fe_mx/librerias/');	
include("FacturacionModerna/FacturacionModerna.php");

/***************************************************************************
* Descripción: Ejemplo del uso de la clase FacturacionModerna, generando un
* archivo de texto simple con los layouts soportados para ser timbrados.
* http://developers.facturacionmoderna.com/#layout 
*
* 
* Facturación Moderna :  (http://www.facturacionmoderna.com)
* @author Edgar Durán <edgar.duran@facturacionmoderna.com>
* @package FacturacionModerna
* @version 1.0
*
*****************************************************************************/

class facturaElectronica extends CommonObject{
	
	/**
	* Niveles de debug:
	* 0 - No almacenar
	* 1 - Almacenar mensajes SOAP en archivo log.
	*/
	private	$debug = 1;
	  
	// RFC utilizado para el ambiente de pruebas
	private $rfc_emisor = "ESI920427886";
	  
	// Datos de acceso al ambiente de pruebas
	private	$url_timbrado	= "https://t1demo.facturacionmoderna.com/timbrado/wsdl";
	private	$user_id		= "UsuarioPruebasWS";
	private	$user_password	= "b9ec2afa3361a59af4b4d102d3f704eabdf097d4";
	private $cliente;
	private $mensaje 		= array();
	private $archCer		= "utilerias/certificados/20001000000200000192.cer";
  	private $archKey		= "utilerias/certificados/20001000000200000192.key";
	private $archKeypem 	= "utilerias/certificados/20001000000200000192.key.pem";
  	private $passKey		= "12345678a";
	private $numero_certificado = "20001000000200000192";
	private $opciones 		= array();
	private $routes;
	var	$table			= "tms_sap";
	
	
	
	function __construct($db){
		$parametros = array(
	  		'emisorRFC'	=> $this->rfc_emisor, 
	  		'UserID'	=> $this->user_id,
	  		'UserPass'	=> $this->user_password
		);
		
		$this->cliente = new FacturacionModerna($this->url_timbrado, $parametros, $this->debug);
		
		$this->opciones = array(
			/**
			* Establecer el valor a true, si desea que el Web services genere el CBB en
			* formato PNG correspondiente.
			* Nota: Utilizar está opción deshabilita 'generarPDF'
			*/   
			'generarCBB'	=> FALSE,
			/**
			* Establecer el valor a true, si desea que el Web services genere la
			* representación impresa del XML en formato PDF.
			* Nota: Utilizar está opción deshabilita 'generarCBB'
			*/
			'generarPDF'	=> FALSE,
			/**
			* Establecer el valor a true, si desea que el servicio genere un archivo de
			* texto simple con los datos del Nodo: TimbreFiscalDigital
			*/
			'generarTXT'	=> FALSE
		);
		
		
		$this->routes= array(
			'comprobantes'		=> 'comprobantes/',
			'retencion_xslt'	=> 'utilerias/xsltretenciones/retenciones.xslt',
			'retencion_pago'	=> 'http://www.sat.gob.mx/esquemas/retencionpago/1',
			'xslt32'			=> 'utilerias/xslt32/cadenaoriginal_3_2.xslt',
			'cfd'				=> 'http://www.sat.gob.mx/cfd/3'
		);
		
		$this->db = $db;
	}
	
		
	function timbrado($factura){
		// generar y sellar un XML con los CSD de pruebas
		// $cfdi = 'layout_ini.txt';
		$cfdi = $this->generarLayout($this->rfc_emisor, $factura);
		$this->mensaje['archivo'] = '';		
		if($this->cliente->timbrar($cfdi, $this->opciones)){
			//Almacenanos en la raíz del proyecto los archivos generados.
			$comprobante = $this->routes['comprobantes'].$this->cliente->UUID;
			$this->mensaje['UUID'] = $this->cliente->UUID;
			
			if($this->cliente->xml){
				$this->mensaje['archivo']['xml'] = "$comprobante.xml";        
				file_put_contents($comprobante.".xml", $this->cliente->xml);
			}
			if(isset($this->cliente->pdf)){
				$this->mensaje['archivo']['pdf'] = "$comprobante.pdf";
				file_put_contents($comprobante.".pdf", $this->cliente->pdf);
			}
			if(isset($this->cliente->png)){
				$this->mensaje['archivo']['png'] = "$comprobante.png";
				file_put_contents($comprobante.".png", $this->cliente->png);
			}
	    
			$this->mensaje['resultado'] = TRUE;
	    
		}else{
			$this->mensaje['resultado'] = FALSE;
			$this->mensaje['error'] = "[".$this->cliente->ultimoCodigoError."] - ".$this->cliente->ultimoError."\n";
		}   
		
		return $this->mensaje; 
	}
	
	
	
	
	function generarLayout($rfc_emisor, $factura){
		/*
    	Puedes encontrar más ejemplos y documentación sobre estos archivos aquí. (Factura, Nota de Crédito, Recibo de Nómina y más...)
    	Link: https://github.com/facturacionmoderna/Comprobantes
    	Nota: Si deseas información adicional contactanos en www.facturacionmoderna.com
 		*/
 
	 	$estructuraFactura = $this->estructuraFactura();
	 
		$cfdi2 = '';
	 	if($estructuraFactura){
			foreach ($estructuraFactura as $seccion => $datos) {
				$cfdi2 .= '['.$seccion.'] 
				
				';
				foreach ($datos as $key => $value) {
					$cfdi2 .= $key.'|';
					
					if(isset($factura[$seccion][$key])){
						$cfdi2 .= $factura[$seccion][$key].' 
						';
					}else{
						$cfdi2 .= $value.' 
						';
					}
					
				}
			}
		}
		
		$final = '<<<LAYOUT 
		'.$cfdi2.' 
		LAYOUT';
		
		return $final;
	}



	function estructuraFactura(){
		$fecha = date('Y-m-j H:s:i');
		$fecha_actual  = $this->formato_fecha($fecha);
		
		$factura = array(
			'Encabezado' => array(
				'serie'				=> '',
				'fecha'				=> $fecha_actual,
				'folio'				=> '',
				'tipoDeComprobante'	=> 'ingreso',
				'formaDePago'		=> 'PAGO EN UNA SOLA EXHIBICIÓN',
				'metodoDePago'		=> 'Transferencía Electrónica',
				'condicionesDePago'	=> 'Contado',
				'NumCtaPago'		=> 'No identificado',
				'subTotal'			=> '10.00',
				'descuento'			=> '0.00',
				'total'				=> '11.60',
				'Moneda'			=> 'MXN',
				'noCertificado'		=> '',
				'LugarExpedicion'	=> 'Nuevo León, México.'
			),
			'Datos Adicionales' => array(
				'tipoDocumento'		=> 'Factura',
				'observaciones'		=> ''
			),
			'Emisor' => array(
				'rfc'				=> 'ESI920427886',
				'nombre'			=> 'EMPRESA DE MUESTRA S.A de C.V.',
				'RegimenFiscal'		=> 'REGIMEN GENERAL DE LEY'
			),
			'DomicilioFiscal' => array(
				'calle'				=> 'Calle', 
				'noExterior'		=> 'Número Ext.',
				'noInterior'		=> 'Número Int.',
				'colonia'			=> 'Colonia',
				'localidad'			=> 'Localidad',
				'municipio'			=> 'Municipio',
				'estado'			=> 'Nuevo León',
				'pais'				=> 'México',
				'codigoPostal'		=> '66260'
			),
			'ExpedidoEn' => array(
				'calle'				=> 'Calle sucursal',
				'noExterior'		=> '',
				'noInterior'		=> '',
				'colonia'			=> '',
				'localidad'			=> '',
				'municipio'			=> 'Nuevo León',
				'estado'			=> 'Nuevo León',
				'pais'				=> 'México',
				'codigoPostal'		=> '77000'
			),
			'Receptor' => array(
				'rfc'				=> 'XAXX010101000',
				'nombre'			=> 'PÚBLICO EN GENERAL'
			),
			'Domicilio' => array(
				'calle'				=> 'Calle',
				'noExterior'		=> 'Num. Ext',
				'noInterior'		=> '',
				'colonia'			=> 'Colonia',
				'localidad'			=> 'San Pedro Garza García',
				'municipio'			=> '',
				'estado'			=> 'Nuevo León',
				'pais'				=> 'México',
				'codigoPostal'		=> '66260'
			),
			'DatosAdicionales' => array(
				'noCliente' 		=> '09871',
				'email'				=> 'edgar.duran@facturacionmoderna.com'
			),
			'Concepto' => array(
				'cantidad'			=> '1',
				'unidad'			=> 'No aplica',
				'noIdentificacion'	=> '',
				'descripcion'		=> 'Servicio Profesional',
				'valorUnitario'		=> '10.00',
				'importe'			=> '10.00'
			),
			'ImpuestoTrasladado' => array(
				'impuesto'			=> 'IVA',
				'importe'			=> '1.60',
				'tasa'				=> '16.00'
			)
			
		);
		
		return $factura;
	}



	function formato_fecha($fecha){
		$nuevafecha = strtotime ( '-1 day' , strtotime ( $fecha ) ) ;
		$fecha = date ( 'Y-m-j' , $nuevafecha );
		$hora = date ( 'H:s:i' , $nuevafecha );
		$fecha_actual = $fecha.'T'.$hora;
		
		return $fecha_actual;
	}
	
	
	
	function cancelacion($uuid){
		/*Cambiar este valor por el UUID que se desea cancelar*/
		$opciones	= null;
	  	
		if($this->cliente->cancelar($uuid, $opciones)){
			$this->mensaje['resultado'] = TRUE;
		}else{
	    	$this->mensaje['resultado'] = FALSE;
			$this->mensaje['error'] = "[".$this->cliente->ultimoCodigoError."] - ".$this->cliente->ultimoError;
		}  
		
		return $this->mensaje;
	}
	
	
	
	function activarCancelacion(){
		if($this->cliente->activarCancelacion($this->archCer,$this->archKey,$this->passKey)){
			$this->mensaje['resultado'] = TRUE;
		}else{
			$this->mensaje['resultado'] = FALSE;
			$this->mensaje['error'] = "[".$this->cliente->ultimoCodigoError."] - ".$this->cliente->ultimoError;
		}    
		
		return $this->mensaje;
	}
	
	
	
	
	
	function retenciones(){
		// generar y sellar un XML con los CSD de pruebas
		$cfdi = $this->generarXMLRetenciones($this->rfc_emisor, $this->numero_certificado, $this->archCer);  
		$cfdi = $this->sellarXMLRetenciones($cfdi, $this->archKeypem);
	 	$this->mensaje['archivo'] = '';
		if($this->cliente->timbrar($cfdi, $this->opciones)){

			//Almacenanos en la raíz del proyecto los archivos generados.
			$comprobante = $this->routes['comprobantes'].$this->cliente->UUID;
    
			if($this->cliente->xml){
				$this->mensaje['archivo']['xml'] = "$comprobante.xml";        
				file_put_contents($comprobante.".xml", $this->cliente->xml);
			}
			if(isset($this->cliente->pdf)){
				$this->mensaje['archivo']['pdf'] = "$comprobante.pdf";        
				file_put_contents($comprobante.".pdf", $this->cliente->pdf);
			}
			if(isset($this->cliente->png)){
				$this->mensaje['archivo']['png'] = "$comprobante.png";        
				file_put_contents($comprobante.".png", $this->cliente->png);
			}
    
			$this->mensaje['resultado'] = TRUE;
	    
		}else{
			$this->mensaje['resultado'] = FALSE;
			$this->mensaje['error'] = "[".$this->cliente->ultimoCodigoError."] - ".$this->cliente->ultimoError."\n";
		}   
		
		return $this->mensaje; 
	}




	function sellarXMLRetenciones($cfdi,$archKeypem){

		$private = openssl_pkey_get_private(file_get_contents($archKeypem));   
  
		$xdoc = new DomDocument();
		$xdoc->loadXML($cfdi) or die("XML invalido"); 
  
		$XSL = new DOMDocument();
		$XSL->load($this->routes['retencion_xslt']);
  
		$proc = new XSLTProcessor;
		$proc->importStyleSheet($XSL);

		$cadena_original = $proc->transformToXML($xdoc);
		openssl_sign($cadena_original, $sig, $private);
		$sello = base64_encode($sig);
  
		$c = $xdoc->getElementsByTagNameNS($this->routes['retencion_pago'], 'Retenciones')->item(0); 
		$c->setAttribute('Sello', $sello);  
		return $xdoc->saveXML();
	}
	
	
	
	
	function generarXMLRetenciones($rfc_emisor,$numero_certificado, $archCer){

		$fecha = date('Y-m-j H:s:i');
		$fecha_actual  = $this->formato_fecha($fecha);
		$fecha_actual = $fecha_actual.'-06:00';
		
		$certificado = str_replace(array('\n', '\r'), '', base64_encode(file_get_contents($archCer)));
		/*
		Puedes encontrar más ejemplos y documentación sobre estos archivos aquí. (Factura, Nota de Crédito, Recibo de Nómina y más...)
		Link: https://github.com/facturacionmoderna/Comprobantes
		Nota: Si deseas información adicional contactanos en www.facturacionmoderna.com
		*/

  $cfdi = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<retenciones:Retenciones xmlns:retenciones="http://www.sat.gob.mx/esquemas/retencionpago/1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation=" http://www.sat.gob.mx/esquemas/retencionpago/1 http://www.sat.gob.mx/esquemas/retencionpago/1/retencionpagov1.xsd" Version="1.0" FolioInt="RetA" Sello="" NumCert="$numero_certificado" Cert="$certificado" FechaExp="$fecha_actual" CveRetenc="05">
  <retenciones:Emisor RFCEmisor="$rfc_emisor" NomDenRazSocE="Empresa retenedora ejemplo"/>
  <retenciones:Receptor Nacionalidad="Nacional">
  <retenciones:Nacional RFCRecep="XAXX010101000" NomDenRazSocR="Publico en GENERAL"/>
  </retenciones:Receptor>
  <retenciones:Periodo MesIni="1" MesFin="1" Ejerc="2014" />
  <retenciones:Totales montoTotOperacion="33783.75" montoTotGrav="35437.50" montoTotExent="0.00" montoTotRet="7323.75">
  <retenciones:ImpRetenidos BaseRet="35437.50" Impuesto="02" montoRet="3780.00" TipoPagoRet="Pago definitivo"/>
  <retenciones:ImpRetenidos BaseRet="35437.50" Impuesto="01" montoRet="3543.75" TipoPagoRet="Pago provisional"/>
  </retenciones:Totales>
  <retenciones:Complemento>
  </retenciones:Complemento>
 </retenciones:Retenciones>
  
XML;
  return $cfdi;
}




	function timbradoXML(){
		//generar y sellar un XML con los CSD de pruebas
		$cfdi = $this->generarXML($this->rfc_emisor);
		$cfdi = $this->sellarXML($cfdi, $this->numero_certificado, $this->archCer, $this->archKeypem);
  		$this->mensaje['archivo'] = '';
		if($this->cliente->timbrar($cfdi, $this->opciones)){

    		//Almacenanos en la raíz del proyecto los archivos generados.
			$comprobante = $this->routes['comprobantes'].$this->cliente->UUID;
    
			if($this->cliente->xml){
				$this->mensaje['archivo'][] = "$comprobante.xml";        
				file_put_contents($comprobante.".xml", $this->cliente->xml);
			}
			if(isset($this->cliente->pdf)){
				$this->mensaje['archivo'][] = "$comprobante.pdf";        
				file_put_contents($comprobante.".pdf", $this->cliente->pdf);
			}
			if(isset($this->cliente->png)){
				$this->mensaje['archivo'][] = "$comprobante.png";        
				file_put_contents($comprobante.".png", $this->cliente->png);
			}
    
			$this->mensaje['resultado'] = TRUE;
	    
		}else{
			$this->mensaje['resultado'] = FALSE;
			$this->mensaje['error'] = "[".$this->cliente->ultimoCodigoError."] - ".$this->cliente->ultimoError."\n";
		}   
		
		return $this->mensaje; 
	}



	function sellarXML($cfdi, $numero_certificado, $archivo_cer, $archivo_pem){
  
		$private = openssl_pkey_get_private(file_get_contents($archivo_pem));
		$certificado = str_replace(array('\n', '\r'), '', base64_encode(file_get_contents($archivo_cer)));
  
		$xdoc = new DomDocument();
		$xdoc->loadXML($cfdi) or die("XML invalido");

		$XSL = new DOMDocument();
		$XSL->load($this->routes['xslt32']);
  
		$proc = new XSLTProcessor;
		$proc->importStyleSheet($XSL);

		$cadena_original = $proc->transformToXML($xdoc);    
		openssl_sign($cadena_original, $sig, $private);
		$sello = base64_encode($sig);

		$c = $xdoc->getElementsByTagNameNS($this->routes['cfd'], 'Comprobante')->item(0); 
		$c->setAttribute('sello', $sello);
		$c->setAttribute('certificado', $certificado);
		$c->setAttribute('noCertificado', $numero_certificado);
		
		return $xdoc->saveXML();
	}
	
	
	
	
	function generarXML($rfc_emisor){

		$fecha = date('Y-m-j H:s:i');
		$fecha_actual  = $this->formato_fecha($fecha);
		
		$factura = $this->estructuraXML();
		
		foreach ($factura as $atributo => $datos) {
			echo $atributo."<br>";
			foreach ($datos as $key => $value) {
				if(is_array($value)){
					foreach ($value as $key2 => $value2) {
						echo '----'.$key2.' '.$value2.'<br>';
					}	
				}else{
					echo '--'.$key.' '.$value.'<br>';	
				}
			}
		}
		/*
		Puedes encontrar más ejemplos y documentación sobre estos archivos aquí. (Factura, Nota de Crédito, Recibo de Nómina y más...)
		Link: https://github.com/facturacionmoderna/Comprobantes
		Nota: Si deseas información adicional contactanos en www.facturacionmoderna.com
		*/
		
  		 $cfdi = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<cfdi:Comprobante xsi:schemaLocation="http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv32.xsd" xmlns:cfdi="http://www.sat.gob.mx/cfd/3" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xs="http://www.w3.org/2001/XMLSchema" version="3.2" fecha="$fecha_actual" tipoDeComprobante="ingreso" noCertificado="" certificado="" sello="" formaDePago="Pago en una sola exhibición" metodoDePago="Transferencia Electrónica" NumCtaPago="No identificado" LugarExpedicion="San Pedro Garza García, Mty." subTotal="10.00" total="11.60">
<cfdi:Emisor nombre="EMPRESA DEMO" rfc="$rfc_emisor">
  <cfdi:RegimenFiscal Regimen="No aplica"/>
</cfdi:Emisor>
<cfdi:Receptor nombre="PUBLICO EN GENERAL" rfc="XAXX010101000"></cfdi:Receptor>
<cfdi:Conceptos>
  <cfdi:Concepto cantidad="10" unidad="No aplica" noIdentificacion="00001" descripcion="Servicio de Timbrado" valorUnitario="1.00" importe="10.00">
  </cfdi:Concepto>  
</cfdi:Conceptos>
<cfdi:Impuestos totalImpuestosTrasladados="1.60">
  <cfdi:Traslados>
    <cfdi:Traslado impuesto="IVA" tasa="16.00" importe="1.6"></cfdi:Traslado>
  </cfdi:Traslados>
</cfdi:Impuestos>
</cfdi:Comprobante>
XML;
 return $cfdi;
	}


	function estructuraXML(){
		
		$fecha = date('Y-m-j H:s:i');
		$fecha_actual  = $this->formato_fecha($fecha);
		
		$comprobante = array(
			'xsi:schemaLocation'=> "http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv32.xsd", 
			'xmlns:cfdi'		=> "http://www.sat.gob.mx/cfd/3",
			'xmlns:xsi'			=> "http://www.w3.org/2001/XMLSchema-instance", 
			'xmlns:xs'			=> "http://www.w3.org/2001/XMLSchema",
			'version'			=> "3.2",
			'fecha'				=> "$fecha_actual", 
			'tipoDeComprobante'	=> "ingreso", 
			'noCertificado'		=> "",
			'certificado'		=> "",
			'sello'				=> "",
			'formaDePago'		=> "Pago en una sola exhibición", 
			'metodoDePago'		=> "Transferencia Electrónica",
			'NumCtaPago'		=> "No identificado",
			'LugarExpedicion'	=> "San Pedro Garza García, Mty.", 
			'subTotal'			=> "10.00",
			'total'				=> "11.60"
		);

		$emisor = array(
			'nombre'			=> "EMPRESA DEMO", 
			'rfc'				=> "$this->rfc_emisor",
			'RegimenFiscal'		=> array(
				'Regimen'			=> "No aplica",
			)   							
		); 
					
		$receptor = array(
			'nombre'			=> "PUBLICO EN GENERAL", 
			'rfc'				=> "XAXX010101000"
		);			
		
		$conceptos = array(
			array(
				'cantidad'			=> "10", 
  				'unidad'			=> "No aplica", 
  				'noIdentificacion'	=> "00001", 
  				'descripcion'		=> "Servicio de Timbrado", 
  				'valorUnitario'		=> "1.00", 
  				'importe'			=> "10.00"
			)
		);
		
		$impuestos = array(
			array(
				'nombre' 			=> "Traslados",
				'subnombre'			=> "Traslado",
				'total'				=> "1.60",
				'impuesto'			=> "IVA", 
    			'tasa'				=> "16.00", 
    			'importe'			=> "1.6"
			)
		);

		$factura = array(
			'comprobante'		=> $comprobante,
			'emisor'			=> $emisor,
			'receptor'			=> $receptor,
			'conceptos'			=> $conceptos,
			'impuestos'			=> $impuestos
		);
		
		return $factura;
	}
	
	
	function insert($datos){	
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
}
?>