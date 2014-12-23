<code>
	<?php foreach($tareas as $tarea):?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td scope="row"><?php echo $tarea['nIdTarea'];?></td>
			<td><?php echo $tarea['cCUser'];?></td>
			<td><?php echo $tarea['cDescripcion'];?></td>
			<td><?php echo $tarea['cComando'];?></td>
			<td><?php echo $tarea['nIdEstado'];?></td>
			<td><?php echo htmlentities($tarea['cResultado']);?></td>
			<td><?php echo format_datetime($tarea['dCreacion']);?></td>
			<td><?php echo format_datetime($tarea['dAct']);?></td>
		</tr>
		<?php $odd = !$odd;?>
		<?php endforeach;?>
</code>
