<?php
include_once('class_factura_electronica.php');

$factura_e = new factura_electronica();

$fecha = date("Ymd");

$factura = array(
	'tipo_cbte' 		=> 1, 
	'punto_vta' 		=> 1,
	
	'concepto'			=> 1,				# 1: productos, 2: servicios, 3: ambos
	'tipo_doc'			=> 80,				# 80: CUIT, 96: DNI, 99: Consumidor Final
	'nro_doc'			=> "23111111113",	# 0 para Consumidor Final (<$1000)
			
	'imp_total'			=> "179.25",		# total del comprobante
	'imp_tot_conc'		=> "2.00",			# subtotal de conceptos no gravados
	'imp_neto'			=> "150.00",		# subtotal neto sujeto a IVA
	'imp_iva'			=> "26.25",			# subtotal impuesto IVA liquidado
	'imp_trib'			=> "1.00",			# subtotal otros impuestos
	'imp_op_ex'			=> "0.00",			# subtotal de operaciones exentas
	'fecha_cbte'		=> $fecha,
	'fecha_venc_pago'	=> "",				# solo servicios
	# Fechas del per�odo del servicio facturado (solo si concepto = 1?)
	'fecha_serv_desde'	=> "",
	'fecha_serv_hasta'	=> "",
	'moneda_id'			=> "PES",			# no utilizar DOL u otra moneda 
	'moneda_ctz'		=> "1.000",			# (deshabilitado por AFIP)
);	    
		    

$cae = $factura_e->obtener_cae($factura);

foreach ($cae as $key => $value) 
{
	echo $key.' '.$value.'<br>';	
}

?>
