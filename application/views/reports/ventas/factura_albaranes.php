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

// Convierte las líneas en albaranes
#echo '<pre>'; print_r($lineas); echo '</pre>';
$temp = array();
$this->load->model('compras/m_albaransalida');
foreach($lineas as $k => $linea)
{
	$idal = $linea['nIdAlbaran'];
	$i = $idal . $linea['fIVA'];
	#. $linea['fPVP'] . $linea['fDescuento']. $linea['fPrecio'] . $linea[$ref_cliente] .(($linea['nCantidad'] > 0)?'+':'-');
	if (isset($temp[$i]))
	{
		$k2 = $temp[$i];
		$k2['fBase'] += $linea['fBase'];
		$k2['fIVAImporte'] += $linea['fIVAImporte'];
		$k2['fPVP'] += $linea['fPVP'];
		$k2['fPrecio'] += $linea['fPrecio'];
		$k2['fBase'] += $linea['fBase'];
		$k2['fTotal'] += $linea['fTotal'];
		$k2['fIVAImporte2'] += $linea['fIVAImporte2'];
		$k2['fBase2'] += $linea['fBase2'];
		#$k2['fPrecio2'] += $linea['fPrecio2'];
		$k2['fTotal2'] += $linea['fTotal2'];
		#$k2['nCantidad'] += $linea['nCantidad'];
		$temp[$i] = $k2;
	}
	else
	{
		$alb = $this->m_albaransalida->load($idal);
		$k2 = array(
			'nCantidad' 	=> 1, #$linea['nCantidad'],
			'fDescuento'	=> 0,
			'nIdLibro'		=> $idal,
			'fBase' 		=> $linea['fBase'],
			'fIVAImporte' 	=> $linea['fIVAImporte'],
			'fTotal' 		=> $linea['fTotal'],
			'fPrecio'		=> $linea['fPrecio'],
			'fBase2' 		=> $linea['fBase2'],
			'fIVAImporte2' 	=> $linea['fIVAImporte2'],
			'fTotal2' 		=> $linea['fTotal2'],
			#'fPrecio2'		=> $linea['fPrecio2'],
			'fPVP'			=> $linea['fPVP'],
			'fIVA'			=> $linea['fIVA'],
			'cTitulo'		=> sprintf($this->lang->line('report-facturaalbaran'), $linea['nIdAlbaran'], format_date($alb['dCreacion'])),		
		);
		$k2['cRefCliente'] = isset($alb['cRefCliente'])?$alb['cRefCliente']:'';
		$temp[$i] = $k2;
	}
}
$lineas = $temp;

require(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'base.php');
