<table>
	<thead>
		<tr>
			<th><?php echo $this->lang->line('Id');?></th>
			<th><?php echo $this->lang->line('Destino');?></th>
			<th><?php echo $this->lang->line('Comando');?></th>
			<th><?php echo $this->lang->line('Tarea');?></th>
			<th><?php echo $this->lang->line('Estado');?></th>
			<th><?php echo $this->lang->line('dCreacion');?></th>
			<th><?php echo $this->lang->line('cCUser');?></th>
		</tr>
	</thead>
	<tbody>
	<?php $odd = FALSE;?>
	<?php foreach($comandos as $comando):?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td scope="row"><?php echo format_enlace_cmd($comando['nIdComando'], site_url('sys/comando/runcmd/' . $comando['nIdComando']));?></td>
			<td><?php echo $comando['cDestino'];?></td>
			<td><?php echo $comando['tComando'];?></td>
			<td><?php echo $comando['nIdTarea'];?></td>
			<td><?php echo $comando['bEjecutado'];?></td>
			<td><?php echo format_datetime($comando['dCreacion']);?></td>
			<td><?php echo $comando['cCUser'];?></td>
		</tr>
		<?php $odd = !$odd;?>
		<?php endforeach;?>
</table>
