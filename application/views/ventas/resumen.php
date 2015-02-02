<table summary="<?php echo $this->lang->line('Resumen estado');?>">
	<caption><?php echo $pedido['nIdPedido'];?><br/>
	<?php echo format_name($pedido['cNombre'], $pedido['cApellido'], $pedido['cEmpresa']);?></caption>
	<thead>
		<tr>
			<th><?php echo $this->lang->line('Estado');?></th>
			<th><?php echo $this->lang->line('Líneas');?></th>
			<th><?php echo $this->lang->line('nCantidad');?></th>
			<th><?php echo $this->lang->line('fBase');?></th>
			<th><?php echo $this->lang->line('fTotal');?></th>
		</tr>
	</thead>
	<tbody>
	<?php $lineas = $unidades = $base = $total = 0; ?>
	<?php foreach($estados as $estado => $valores):?>
		<tr>
			<td scope="row" id="_co2" nowrap="nowrap"><?php echo $estado;?>
			</td>
			<td align="right"><?php echo $valores['lineas'];?></td>
			<td align="right"><?php echo $valores['unidades'];?></td>
			<td align="right"><?php echo format_price($valores['base']);?></td>
			<td align="right"><?php echo format_price($valores['total']);?></td>
		</tr>
		<?php $lineas += $valores['lineas'];?>
		<?php $unidades += $valores['unidades'];?>
		<?php $base += $valores['base'];?>
		<?php $total += $valores['total'];?>
		<?php endforeach; ?>
	</tbody>
	<tfoot>
		<tr>
			<td>&nbsp;</td>
			<td align="right"><?php echo $lineas;?></td>
			<td align="right"><?php echo $unidades;?></td>
			<td align="right"><?php echo format_price($base);?></td>
			<td align="right"><?php echo format_price($total);?></td>
		</tr>
	</tfoot>
</table>
