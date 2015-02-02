<?php
$titulo = $this->lang->line(($bDeposito)?'report-Pedido Proveedor Depósito':'report-Pedido Proveedor');
$borrador = ($nIdEstado == 1);
$nIdDocumento = $nIdPedido;
$cliente = $proveedor;
$nIdCliente = $nIdProveedor;
$cRefCliente = $cRefProveedor;
$ref_cliente = 'cRefProveedor';
$dCreacion = $dFechaEntrega;
$texto_condiciones = $this->lang->line('text-pedidoproveedor');
$texto_email = $this->lang->line('text-pedidoproveedor-email');
$clpv = $this->lang->line('report-Proveedor');

$ci = get_instance();
if (isset($nIdEntrega))
{
	$ci->load->model('proveedores/m_direccion');
	$dir = $ci->m_direccion->load($nIdEntrega);
	$direccionenvio = format_address_print($dir);
	$extra_head = '<h5> ' . $this->lang->line('report-DirEntrega') . '</h5> ' .
		'<div>' . $direccionenvio . '</div>';
	$num_lineas_1_minus = 3;
}

if (isset($pedidosuscripcion) && (count($pedidosuscripcion) > 0))
{
	$ci->load->model('suscripciones/m_suscripcion');
	$suscripcion = $ci->m_suscripcion->load($pedidosuscripcion[0]['nIdSuscripcion'], TRUE);
	if ($suscripcion)
	{
		$show_ejemplares = FALSE;
		$show_titulos = FALSE;
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
		$fiscal = $this->config->item('company.name') . '<br />' . (($this->config->item('company.address.1') != '') ? $this->config->item('company.address.1') . '<br/>' : '') . (($this->config->item('company.address.2') != '') ? $this->config->item('company.address.2') . '<br/>' : '') . (($this->config->item('company.address.3') != '') ? $this->config->item('company.address.3') . '<br/>' : '') . $this->lang->line('report-NIF') . ': ' . $this->config->item('company.vat');

		$extra_page = /*'<pre>' . print_r($suscripcion, TRUE) . '</pre>' . */'<table width="100%">
		<tr><th class="items-th" colspan="2">' . $this->lang->line('report-Datos Suscripción') . '</th></tr>
		<tr>
			<td nowrap="nowrap" width="10%"class="meta-head">' . $this->lang->line('report-Número Suscripción') . '</td>
			<td width="90%"class="text-bold">' . $suscripcion['nIdSuscripcion'] . '</td>
		</tr>
		<tr>
	
			<td nowrap="nowrap" class="meta-head">' . $this->lang->line('report-Dirección Envío') . '</td>
			<td class="text">' . $direccionenvio . '</td>
		</tr>
		<tr>
			<td nowrap="nowrap" class="meta-head">' . $this->lang->line('report-Periodo') . '</td>
			<td class="text">' . format_date($suscripcion['dDesde']) . ' - ' . format_date($suscripcion['dHasta']) . '</td>
		</tr>
		</table>';
		$texto_condiciones .= sprintf($this->lang->line('report-condicionespedidosuscripcion'), $suscripcion['nIdSuscripcion'], $fiscal);
	}
}
require (__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'base.php');
