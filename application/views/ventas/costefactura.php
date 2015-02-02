<table summary="<?php echo $this->lang->line('Coste');?>">
	<caption>
		<?php echo $this->lang->line('Coste');?> <?php echo $id;?>
	</caption>
	<thead>
		<tr>
			<th><?php echo $this->lang->line('Coste');?></th>
			<th><?php echo $this->lang->line('Venta');?></th>
			<th><?php echo $this->lang->line('Margen');?></th>
			<th><?php echo $this->lang->line('%');?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td align="right"><?php echo format_price($coste);?></td>
			<td align="right"><?php echo format_price($base);?></td>
			<td align="right"><?php echo format_price($base - $coste);?></td>
			<td align="right"><?php echo format_percent(100-(($coste*100)/$base));?></td>
		</tr>
	</tbody>
</table>
