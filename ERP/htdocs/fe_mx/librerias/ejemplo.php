<?php
include_once("facturaElectronica.php");
// Creación del objeto
$factura_electronica = new facturaElectronica();


/*---------------------------------------------------------------------------------
		TIMBRADO 
---------------------------------------------------------------------------------*/

// Array con la factura
$factura = array(
	'Encabezado' => array(
		'serie'				=> '',
		'fecha'				=> '2015-10-12T18:39:35',
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
	)
);

// Obtengo resultados, ver como mejorar
$mensaje = $factura_electronica->timbrado($factura);

if($mensaje['resultado']){
	foreach ($mensaje['archivo'] as $archivo) {
		echo $archivo;
	}
}else{
	echo $mensaje['error'];
};

/*---------------------------------------------------------------------------------
		CANCELACIÓN 
---------------------------------------------------------------------------------*/

echo "<br><hr>";

$uuid	= "3F938316-7E5E-4EE4-9D38-A8C3023120C9";
$mensaje = $factura_electronica->cancelacion($uuid);

if($mensaje['resultado']){
	echo $mensaje['archivo'];
}else{
	echo $mensaje['error'];
}


/*---------------------------------------------------------------------------------
		RETENCIONES 
---------------------------------------------------------------------------------*/

echo "<br><hr>";

$mensaje = $factura_electronica->retenciones();

if($mensaje['resultado']){
	foreach ($mensaje['archivo'] as $archivo) {
		echo $archivo;
	}
}else{
	echo $mensaje['error'];
};


/*---------------------------------------------------------------------------------
		TIMBRADO XML 
---------------------------------------------------------------------------------*/

echo "<br><hr>";

$mensaje = $factura_electronica->timbradoXML();

if($mensaje['resultado']){
	foreach ($mensaje['archivo'] as $archivo) {
		echo $archivo;
	}
}else{
	echo $mensaje['error'];
};

?>
