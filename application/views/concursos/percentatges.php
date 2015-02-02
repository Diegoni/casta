<table class="table table-striped table-bordered table-hover">
	<thead>
		<tr>
			<th><?php echo $this->lang->line('Biblioteca'); ?></th>
			<th><?php echo $this->lang->line('Sala'); ?></th>
			<th><?php echo $this->lang->line('Servits'); ?></th>
			<th><?php echo $this->lang->line('Descatalogats'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php $total = $serv = $desc = 0; ?>
		<?php foreach($datos as $b => $v):?>
			<?php foreach($v as $s => $t):?>
				<tr>
					<th align="left"><?php echo $b;?></th>
					<td align="left"><?php echo $s;?></td>
					<td align="left"><?php echo format_percent($t['servidos']*100/$t['total']);?></td>
					<td align="left"><?php echo format_percent($t['descatalogados']*100/$t['total']);?></td>
					<?php 
						$total += $t['total'];
						$serv += $t['servidos'];
						$desc += $t['descatalogados'];
					?>
				</tr>
			<?php endforeach;?>
			<tr class="info">
				<th align="left"><?php $this->lang->line('Total'); ?></th>
				<td align="left">&nbsp;</td>
				<td align="left"><strong><?php echo format_percent($serv*100/$total);?></strong></td>
				<td align="left"><strong><?php echo format_percent($desc*100/$total);?></strong></td>
			</tr>
		<?php endforeach;?>
	</tbody>
</table>
