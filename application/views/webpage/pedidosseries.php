<table summary="<?php echo $this->lang->line('Pedidos Series');?>">
	<caption><?php echo $this->lang->line('Pedidos Series');?></caption>
	<thead>
		<tr>
			<th scope="col">&nbsp;</th>
			<?php foreach($pedidos['years'] as $y => $series): ?>
			<th align="center" scope="col" colspan="<?php echo count($series) + 1;?>"><?php echo $y;?></th>
			<?php endforeach; ?>
		</tr>
		<tr>
			<th scope="col">&nbsp;</th>
			<?php foreach($pedidos['years'] as $y => $series): ?>
			<?php foreach($series as $s => $shit): ?>
			<th align="center" scope="col"><?php echo $s;?></th>
			<?php endforeach; ?>
			<th align="center" scope="col"><?php echo $this->lang->line('Total');?></th>
			<?php endforeach; ?>
		</tr>
	</thead>
	<tbody>
	<?php $alt = FALSE;?>
	<?php for($m = 1;$m <= 12; $m++): ?>
		<tr <?php if ($alt):?>class="alt"<?php endif;?>>
			<td scope="row" id="_co" nowrap="nowrap"><?php echo $m;?></td>
			<?php foreach($pedidos['years'] as $y => $series): ?>
			<?php $total = 0;?>
			<?php foreach($series as $s => $shit): ?>
			<td align="right"><?php echo (isset($pedidos['datos'][$y][$m][$s]))?format_price($pedidos['datos'][$y][$m][$s]):'&nbsp;';?></td>
			<?php $total += (isset($pedidos['datos'][$y][$m][$s]))?$pedidos['datos'][$y][$m][$s]:0;?>
			<?php $totales[$y][$s] = (isset($totales[$y][$s])?$totales[$y][$s]:0) + ((isset($pedidos['datos'][$y][$m][$s]))?$pedidos['datos'][$y][$m][$s]:0);?>			
			<?php endforeach; ?>
			<td align="center" scope="col"><strong><?php echo format_price($total);?></strong></td>
			<?php endforeach; ?>
		</tr>
		<?php $alt = !$alt?>		
		<?php endfor;?>		
	</tbody>
	<tfoot>
			<tr>
			<td scope="row">&nbsp;</td>
			<?php foreach($pedidos['years'] as $y => $series): ?>
			<?php $total = 0;?>
			<?php foreach($series as $s => $shit): ?>
			<td align="right"><?php echo (isset($totales[$y][$s]))?format_price($totales[$y][$s]):'&nbsp;';?></td>
			<?php $total += (isset($totales[$y][$s]))?$totales[$y][$s]:0;?>
			<?php endforeach; ?>
			<td align="center" scope="col"><strong><?php echo format_price($total);?></strong></td>
			<?php endforeach; ?>
		</tr>	
	</tfoot>
</table>
