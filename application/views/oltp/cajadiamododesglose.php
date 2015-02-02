<h1><?php echo $this->lang->line('Desde');?>: <?php echo $fecha1; ?><br />
<?php echo $this->lang->line('Hasta');?>: <?php echo $fecha2; ?></h1>
<table border="0" bordercolor="#000000" cellspacing="0" cellpadding="3"
	class="SummaryDataGrid"
	style="BORDER-TOP-WIDTH: 0px; BORDER-LEFT-WIDTH: 0px; BORDER-BOTTOM-WIDTH: 0px; BORDER-COLLAPSE: collapse; BORDER-RIGHT-WIDTH: 0px">
	<tr class="HeaderStyle">
		<th class="HeaderStyle"><?php echo $this->lang->line('Fecha');?></th>
		<th class="HeaderStyle"><?php echo $this->lang->line('Caja');?></th>
		<th class="HeaderStyle"><?php echo $this->lang->line('Modo');?></th>
		<th class="HeaderStyle"><?php echo $this->lang->line('Importe');?></th>
		<th class="HeaderStyle"><?php echo $this->lang->line('Factura');?></th>
		<th class="HeaderStyle"><?php echo $this->lang->line('Cliente');?></th>
	</tr>

	<?php $total = 0; ?>
	<?php foreach($valores as $venta):?>
	<?php if (isset($venta['nIdFactura'])): ?>
	<tr class="Line2">
		<td class="Line1"><?php echo format_datetime($venta['dDia']);?></td>
		<td class="Line1"><?php echo $venta['cCaja'];?></td>
		<td class="Line1"><?php echo $venta['cDescripcion'];?></td>
		<td align="right" class="Line1"><?php echo format_price($venta['fImporte']);?></td>
		<td class="Line1"><?php echo format_enlace_cmd($venta['nNumero']. '-' . $venta['nNumeroSerie'], site_url('ventas/factura/index/' . $venta['nIdFactura']));?>
		</td>
		<td align="left" class="Line1"><?php echo format_enlace_cmd($venta['nIdCliente'], site_url('clientes/cliente/index/' . $venta['nIdCliente']));?>
		- <?php echo format_name($venta['cNombre'], $venta['cApellido'], $venta['cEmpresa']);?>
		</td>
	</tr>
	<?php $total += $venta['fImporte']?>
	<?php endif; ?>
	<?php endforeach; ?>
	<tr>
		<td colspan="3" class="tablapie">&nbsp;</td>
		<td align="right" class="tablapie"><?php echo format_price($total); ?></td>
		<td colspan="2" class="tablapie">&nbsp;</td>
	</tr>
</table>
