<table>
	<thead>
		<tr>
			<th colspan="2"><?php echo $this->lang->line('Permisos');?></th>
		</tr>
	</thead>
	<?php foreach ($auth as $k => $v):?>
	<tr>
		<td><?php echo $k;?></td>
		<td><?php echo $v;?></td>
	</tr>
	<?php endforeach;?>
</table>
