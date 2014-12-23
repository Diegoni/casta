<table>
	<thead>
		<tr>
			<th><?php echo $this->lang->line('Id');?></th>
			<th><?php echo $this->lang->line('cCUser');?></th>
			<th><?php echo $this->lang->line('Tarea');?></th>
			<th><?php echo $this->lang->line('Comando');?></th>
			<th><?php echo $this->lang->line('Estado');?></th>
			<th><?php echo $this->lang->line('Resultado');?></th>
			<th><?php echo $this->lang->line('dCreacion');?></th>
			<th><?php echo $this->lang->line('dAct');?></th>
		</tr>
	</thead>
	<tbody>
	<?php $odd = FALSE;?>
	<?php foreach($tareas as $tarea):?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td scope="row"><?php echo $tarea['nIdTarea'];?></td>
			<td><?php echo $tarea['cCUser'];?></td>
			<td><?php echo $tarea['cDescripcion'];?></td>
			<td><?php echo $tarea['cComando'];?></td>
			<td><?php echo $this->lang->line('estado-tarea-' . $tarea['nIdEstado']);?></td>
			<td><?php echo htmlentities($tarea['cResultado']);?></td>
			<td><?php echo format_datetime($tarea['dCreacion']);?></td>
			<td><?php echo format_datetime($tarea['dAct']);?></td>
		</tr>
		<?php $odd = !$odd;?>
		<?php endforeach;?>
</table>
