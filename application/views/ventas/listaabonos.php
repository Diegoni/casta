<table summary="<?php echo $this->lang->line('Abonos');?>">
	<caption><?php echo $this->lang->line('Abonos');?><br/>
	<?php echo $cliente['nIdCliente'];?><br/>
	<?php echo format_name($cliente['cNombre'], $cliente['cApellido'], $cliente['cEmpresa']);?></caption>
	<thead>
		<tr>
			<th colspan="2">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach($abonos as $abono):?>
		<tr>
			<td scope="row" id="_co2" nowrap="nowrap"><?php echo format_enlace_cmd($abono, site_url('ventas/abono/index/' . $abono));?>
			</td>
			<td align="right"><?php echo format_price($importe);?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
