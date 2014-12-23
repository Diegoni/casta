<table>
	<thead>
		<tr>
			<th colspan="2"><?php echo $this->config->item('bp.application.name');?>
			</th>
		</tr>
	</thead>
	<tr class="alt">
		<td>PHP</td>
		<td><?php echo phpversion();?></td>
	</tr>
	<tr class="alt">
		<td>Servidor</td>
		<td><?php echo $_SERVER['SERVER_NAME'];?></td>
	</tr>
	<tr class="alt">
		<td>Servidor Web</td>
		<td><?php echo $_SERVER['SERVER_SOFTWARE'];?></td>
	</tr>
	<?php foreach ($versions as $k => $v):?>
	<tr>
		<td><?php echo $k;?></td>
		<td><?php echo $v[0];?></td>
	</tr>
	<?php endforeach;?>
</table>
