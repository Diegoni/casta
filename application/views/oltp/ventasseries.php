<table
	summary="<?php echo $this->lang->line('Ventas por series y meses');?> <?php echo $year; ?>">
	<caption><?php echo $this->lang->line('Ventas por series y meses');?> <?php echo $year; ?>
	</caption>
	<thead>
		<tr>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Serie');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Ene.');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Feb.');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Mar.');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Abr.');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('May.');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Jun.');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Jul.');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Ago.');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Sep.');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Oct.');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Nov.');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Dic.');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Total');?></th>
		</tr>
	</thead>
	<tbody>
	<?php $odd = FALSE;?>
	<?php $totales[] = array();?>
	<?php foreach($valores as $k => $v):?>
	<?php $total = 0;?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td scope="row"><?php echo $v['serie']['cDescripcion']?> (<?php echo $v['serie']['nNumero']?>)</td>
			<?php for($i = 1; $i <= 12; $i++):?>
			<td align="right"><?php echo format_price((isset($v['data'][$i])?$v['data'][$i]:0));?></td>
			<?php $total += (isset($v['data'][$i])?$v['data'][$i]:0);?>
			<?php $totales[$i] = (isset($totales[$i])?$totales[$i]:0) + (isset($v['data'][$i])?$v['data'][$i]:0); ?>
			<?php endfor; ?>
			<td align="right"><strong><?php echo format_price($total);?></strong></td>
		</tr>
		<?php $odd = !$odd;?>
		<?php endforeach;?>
	</tbody>
	<tfoot>
		<tr>
			<td>&nbsp;</td>
			<?php $total = 0;?>
			<?php for($i = 1; $i <= 12; $i++):?>
			<td align="right"><?php echo format_price((isset($totales[$i])?$totales[$i]:0));?></td>
			<?php $total += (isset($totales[$i])?$totales[$i]:0);?>
			<?php endfor; ?>
			<td align="right"><strong><?php echo format_price($total);?></strong></td>
		</tr>
	</tfoot>
</table>
