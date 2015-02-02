<?php $fecha1 = str_replace('/', '-', $fecha1);?>
<?php $fecha2 = str_replace('/', '-', $fecha2);?>
<table
	summary="<?php echo $this->lang->line('Ventas por series en un periodo');?> <?php echo $fecha1; ?> &lt;-&gt; <?php echo $fecha2; ?>">
	<caption><?php echo $this->lang->line('Ventas por series en un periodo');?>
	<br/><?php echo $fecha1; ?> &lt;-&gt; <?php echo $fecha2; ?></caption>
	<thead>
		<tr>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Serie');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Coste');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Base');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('IVA');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Total');?></th>
		</tr>
	</thead>
	<tbody>
	<?php $odd = FALSE;?>
	<?php $total = 0;?>
	<?php $iva = 0;?>
	<?php $base = 0;?>
	<?php $coste = 0;?>
	<?php foreach($valores as $v):?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td><?php echo $v['NumeroSerie'];?>-<?php echo $v['Serie'];?></td>
			<td align="right"><?php echo format_price($v['fCoste']);?></td>
			<td align="right"><?php echo format_price($v['fBase']);?></td>
			<td align="right"><?php echo format_price($v['fIVA']);?></td>
			<td align="right"><?php echo format_price($v['fBase'] + $v['fIVA']);?></td>
		</tr>
		<?php $total += $v['fBase'] + $v['fIVA'];?>
		<?php $iva += $v['fIVA'];?>
		<?php $base += $v['fBase'];?>
		<?php $coste += $v['fCoste'];?>
		<?php $odd = !$odd;?>
		<?php endforeach;?>
	</tbody>
	<tfoot>
		<tr>
			<td>&nbsp;</td>
			<td align="right"><?php echo format_price($coste);?></td>
			<td align="right"><?php echo format_price($base);?></td>
			<td align="right"><?php echo format_price($iva);?></td>
			<td align="right"><?php echo format_price($total);?></td>
		</tr>
	</tfoot>
</table>
