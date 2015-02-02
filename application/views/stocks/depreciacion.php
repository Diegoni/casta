<table width="100%">
	<caption><strong><?php echo $this->lang->line('Análisis stock retrocedido'); ?>
	<?php echo $fecharetroceso; ?></strong></caption>
	<thead>
		<tr>
			<th>&nbsp;</th>
			<th><?php echo $this->lang->line('nFirme1');?></th>
			<th><?php echo $this->lang->line('nFirme2');?></th>
			<th><?php echo $this->lang->line('nFirme3');?></th>
			<th><?php echo $this->lang->line('nFirme4');?></th>
			<th><?php echo $this->lang->line('Total');?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><?php echo $this->lang->line('Unidades');?></td>
			<td align="right"><?php echo format_number($nFirme1); ?></td>
			<td align="right"><?php echo format_number($nFirme2); ?></td>
			<td align="right"><?php echo format_number($nFirme3); ?></td>
			<td align="right"><?php echo format_number($nFirme4); ?></td>
			<td align="right"><?php echo format_number($nFirme1 + $nFirme2 + $nFirme3 + $nFirme4); ?></td>
		</tr>
		<tr class="alt">
			<td><?php echo $this->lang->line('Importe');?></td>
			<td align="right"><?php echo format_price($nCosteFirme1); ?></td>
			<td align="right"><?php echo format_price($nCosteFirme2); ?></td>
			<td align="right"><?php echo format_price($nCosteFirme3); ?></td>
			<td align="right"><?php echo format_price($nCosteFirme4); ?></td>
			<td align="right"><?php echo format_price($nCosteFirme1 + $nCosteFirme2 + $nCosteFirme3 + $nCosteFirme4); ?></td>
		</tr>
		<tr>
			<td><?php echo $this->lang->line('Depreciación');?></td>
			<td align="right"><?php echo format_price($nCosteDepreciado1); ?></td>
			<td align="right"><?php echo format_price($nCosteDepreciado2); ?></td>
			<td align="right"><?php echo format_price($nCosteDepreciado3); ?></td>
			<td align="right"><?php echo format_price($nCosteDepreciado4); ?></td>
			<td align="right"><?php echo format_price($nCosteDepreciado1 + $nCosteDepreciado2 + $nCosteDepreciado3 + $nCosteDepreciado4); ?></td>
		</tr>
	</tbody>
</table>
