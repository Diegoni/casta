<table
	class="sortable-onload-0 rowstyle-alt colstyle-alt"
	summary="<?php echo $this->lang->line('Facturas suscripciones');?>">
	<caption><?php echo $this->lang->line('Facturas suscripciones');?><br/>
		<?php echo format_date($fecha1); ?> -> <?php echo format_date($fecha2); ?> 
	</caption>
	<thead>
		<tr>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Factura');?></th>
			<th class="sortable-date-dmy" scope="col"><?php echo $this->lang->line('Fecha'); ?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Suscripción');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Cliente');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Cantidad');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Descuento');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('IVA'); ?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Precio'); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php $odd = FALSE;?>
	<?php $total = 0; ?>
	<?php foreach($valores as $k => $m):?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td scope="row"><?php echo format_enlace_cmd(format_numerofactura($m['nNumero'], $m['cSerie']), site_url('ventas/factura/index/' . $m['nIdFactura']));?></td>
			<td scope="row" align="right"><?php echo format_date($m['dFecha']);?></td>
			<td scope="row"><?php echo format_enlace_cmd($m['nIdSuscripcion'], site_url('suscripciones/suscripcion/index/' . $m['nIdSuscripcion']));?></td>
			<td scope="row"><?php echo format_enlace_cmd(format_name($m ['cNombre'], $m ['cApellido'], $m ['cEmpresa']), site_url('clientes/cliente/index/' . $m['nIdCliente']));?></td>
			<td scope="row" align="right"><?php echo ($m['nCantidad']);?></td>
			<td scope="row" align="center"><?php echo ($m['fDescuento']);?></td>
			<td scope="row" align="center"><?php echo ($m['fIVA']);?></td>
			<td scope="row" align="right"><?php echo format_price($m['fPrecio']);?></td>
		</tr>
		<?php $odd = !$odd;?>
		<?php $total += $m['nCantidad'] * format_decimals($m['fPrecio'] * (1 - $m['fDescuento'] / 100)); ?>
		<?php endforeach;?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="7"><?php echo count($valores);?> <?php echo $this->lang->line('registros');?></td>
			<td align="right"><?php echo format_price($total);?></td>
		</tr>
	</tfoot>
</table>
