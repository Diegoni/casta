<table class="table table-striped table-bordered table-hover">
	<thead>
		<tr>
			<th><?php echo $this->lang->line('Biblioteca'); ?></th>
			<th><?php echo $this->lang->line('Sala'); ?></th>
			<th><?php echo $this->lang->line('Preu Mig'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php $total = $unidades = 0; ?>
		<?php foreach($datos as $b => $v):?>
			<?php foreach($v as $s => $t):?>
				<tr>
					<th align="left"><?php echo $b;?></th>
					<td align="left"><?php echo $s;?></td>
					<td align="right"><?php echo format_price($t['total']/$t['unidades']);?></td>
					<?php 
						$total += $t['total'];
						$unidades += $t['unidades'];
					?>
				</tr>
			<?php endforeach;?>
			<tr class="info">
				<th align="left"><?php $this->lang->line('Total'); ?></th>
				<td align="left">&nbsp;</td>
				<td align="right"><strong><?php echo format_price($total/$unidades);?></strong></td>
			</tr>
		<?php endforeach;?>
	</tbody>
</table>
