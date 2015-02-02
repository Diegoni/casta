<?php $this->load->helper('asset');?>
<?php
$titulo = $this->lang->line('report-reclamacion-' . $cTipoReclamacion);
$borrador = FALSE;
$nIdDocumento = $nIdReclamacion;
$cRefCliente = $this->lang->line('report-Suscripcion-breve'). ' ' . $nIdSuscripcion;
$texto_condiciones = null;
if ($nIdDestino == DESTINO_RECLAMACION_PROVEEDOR) 
{
	$cliente = $proveedor;
	$nIdCliente = $nIdProveedor;
	$direccion = $direccionproveedor;
}
else
{
	$direccion = $direccioncliente;
}
?>
<?php require(APPPATH . 'views' . DIRECTORY_SEPARATOR . 'reports' . DIRECTORY_SEPARATOR . 'params.php'); ?>
<div id="page-wrap"><?php require(APPPATH . 'views' . DIRECTORY_SEPARATOR . 'reports' . DIRECTORY_SEPARATOR . 'header.php'); ?>

<table class="items">
	<tr>
		<td><?php echo $tDescripcion;?></td>
	</tr>
</table>
<div style="clear: both"></div>
<div id="footer">
<?php require(APPPATH . 'views' . DIRECTORY_SEPARATOR . 'reports' . DIRECTORY_SEPARATOR . 'terms.php'); ?>
</div>
</div>
