<table summary="<?php echo $this->lang->line('Pedidos en Internet');?>">
	<caption><?php echo $this->lang->line('Pedidos en Internet');?></caption>
	<thead>
		<tr>
			<th scope="col">&nbsp;</th>
			<?php foreach($pedidos['years'] as $y => $shit): ?>
			<th scope="col"><?php echo $y;?></th>
			<?php endforeach; ?>
		</tr>
	</thead>
	<tbody>
	<?php $alt = FALSE;?>
	<?php for($m = 1;$m <= 12; $m++): ?>
		<tr <?php if ($alt):?>class="alt"<?php endif;?>>
			<td scope="row" id="_co" nowrap="nowrap"><?php echo $m;?></td>
			<?php foreach($pedidos['years'] as $y => $shit): ?>
			<td align="right"><?php echo (isset($pedidos['datos'][$y][$m]))?format_number($pedidos['datos'][$y][$m]):'&nbsp;';?></td>
			<?php endforeach;?>
		</tr>
		<?php $alt = !$alt?>		
		<?php endfor;?>		
	</tbody>
</table>

