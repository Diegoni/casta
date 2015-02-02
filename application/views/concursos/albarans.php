<table class="table table-striped table-bordered table-hover">
	<thead>
		<tr>
			<th><?php echo $this->lang->line('#'); ?></th>
			<th><?php echo $this->lang->line('Data'); ?></th>
			<th><?php echo $this->lang->line('Biblioteca'); ?></th>
			<th><?php echo $this->lang->line('Sala'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php $total = $unidades = 0; ?>
		<?php foreach($datos as $v):?>
			<tr>
				<th align="left"><?php echo $v['nIdAlbaran'];?></th>
				<td align="right"><?php echo format_date($v['dCreacion']);?></td>
				<td align="left"><?php echo $v['cBiblioteca'];?></td>
				<td align="left"><?php echo $v['cSala'];?></td>
			</tr>
		<?php endforeach;?>
	</tbody>
</table>
