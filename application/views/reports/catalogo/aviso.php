<?php
$titulo = $this->lang->line('report-Factura');
$borrador = !isset($nNumero);
$nIdDocumento = isset($nNumero)?format_numerofactura($nNumero, $serie['nNumero']):$nIdFactura;;
$texto_condiciones = $this->lang->line('text-factura');
$extra_impuestos = '';
$dCreacion = $dFecha;
foreach($modospago as $mp) {
	$extra_impuestos .= $this->lang->line('report-' .$mp['cModoPago']) .': ' . format_price($mp['fImporte']) .'<br />';
}
$texto_email = $this->lang->line('text-factura-email');

require('email.php');
