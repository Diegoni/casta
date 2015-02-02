<?php
$titulo = $this->lang->line('report-Factura');
$borrador = !isset($nNumero);
$nIdDocumento = isset($nNumero)?format_numerofactura($nNumero, $serie['nNumero']):$nIdFactura;
$nIdCode = $nIdFactura;
$texto_condiciones = $this->lang->line('text-factura');
$extra_impuestos = '';
$dCreacion = $dFecha;
foreach($modospago as $mp) {
	$extra_impuestos .= $this->lang->line('report-' .$mp['cModoPago']) .': ' . format_price($mp['fImporte']) .'<br />';
}
$texto_email = $this->lang->line('text-factura-email');

$ci = get_instance();
# Dirigido a 
if (isset($nIdDireccionEnvio) && ($nIdDireccionEnvio != $nIdDireccion))
{
	$ci->load->model('clientes/m_direccioncliente');
	$dir = $ci->m_direccioncliente->load($nIdDireccionEnvio);
	$direccionenvio = format_address_print($dir);
	$extra_head = '<h5> ' . $this->lang->line('report-Dirigido A') . '</h5> ' .
		'<div>' . $direccionenvio . '</div>';
	$num_lineas_1_minus = 3;
}

# Suscripciones
$ci->load->model('suscripciones/m_suscripcion');
$ci->load->model('ventas/m_albaransalida');
$ci->load->model('ventas/m_factura');
$sus[] = array();
$hay = FALSE;
$sus2 = $ci->m_factura->get_suscripciones($nIdFactura);
if (count($sus) > 0)
{
	foreach ($lineas as $k => $linea)
	{	
		#Comprobación de suscripciones
		foreach ($sus2 as $k2 => $reg)
		{
			if (!isset($sus[$reg['nIdSuscripcion']]))
			{
				$sus[$reg['nIdSuscripcion']] = $ci->m_suscripcion->load($reg['nIdSuscripcion'], TRUE);
			}
			#var_dump($sus[$reg['nIdSuscripcion']]['nIdRevista'], $linea['nIdLibro']);

			if ($sus[$reg['nIdSuscripcion']]['nIdRevista'] == $linea['nIdLibro'])
			{
				$suscripcion = $sus[$reg['nIdSuscripcion']];
				if ($suscripcion['nIdTipoEnvio'] == 1)
				{
					$direccionenvio = format_address_print($suscripcion['direccionenvio']);
					if (empty($suscripcion['direccionenvio']['cTitular']))
					$direccionenvio = format_name($suscripcion['cliente']['cNombre'], $suscripcion['cliente']['cApellido'], $suscripcion['cEmpresa']) . 
					'<br/>' . $direccionenvio;
				}
				else
				{
					$direccionenvio = $this->config->item('company.name') . '<br />' . (($this->config->item('company.address.1') != '') ? $this->config->item('company.address.1') . '<br/>' : '') . (($this->config->item('company.address.2') != '') ? $this->config->item('company.address.2') . '<br/>' : '') . (($this->config->item('company.address.3') != '') ? $this->config->item('company.address.3') : '');
				}
		
				$extra = '<strong>' . $this->lang->line('report-Número Suscripción') . '</strong>: ' . $suscripcion['nIdSuscripcion'] . '<br/>';
				if (isset($linea['cRefInterna']) && trim($linea['cRefInterna']) != '')
					$extra .='<strong>' . $this->lang->line('report-Año/Vol') . '</strong>: ' . $linea['cRefInterna'] . '<br/>';
				$extra .= '<strong>' . $this->lang->line('report-Dirección Envío') . '</strong>: ' . $direccionenvio;
				$hay = TRUE;
				$lineas[$k]['cExtra2']  = $extra;
				#La quita por si hay más suscripciones del mismo artículo
				unset($sus2[$k2]);
				break;
			}
		}
	}	
}
#die();
if ($hay)
{
	require(APPPATH . 'views' . DIRECTORY_SEPARATOR . 'reports' . DIRECTORY_SEPARATOR . 'params.php');
	$num_lineas_1 = (int) ($num_lineas_1  / 3);
	$num_lineas_2 = (int) ($num_lineas_2 / 3);
}

require(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'base.php'); 
