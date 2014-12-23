<table
	summary="<?php echo $this->lang->line('Tareas Programadas');?>"
	id="tab_resumen">
	<caption><?php echo $this->lang->line('Tareas Programadas');?></caption>
	<thead>
		<tr>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Descripción');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Acción');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Minuto');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Hora');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Día');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Mes');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Di/Se');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Ult.Ejec.');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Sig.Ejec.');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Resultado');?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="10"><?php echo count($tareas);?> <?php echo $this->lang->line('registros');?></td>
		</tr>
	</tfoot>
	<tbody>
	<?php $odd = FALSE;?>
	<?php foreach($tareas as $h):?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td><?php echo $h[8];?></td>
			<td><?php echo format_enlace_cmd($h[7], site_url($h[7]));?></td>
			<td><?php echo $h[1];?></td>
			<td><?php echo $h[2];?></td>
			<td><?php echo $h[3];?></td>
			<td><?php echo $h[4];?></td>
			<td><?php echo $h[5];?></td>
			<td><?php echo format_datetime($h['lastActual']);?></td>
			<td><?php echo format_datetime($h['lastScheduled']);?></td>
			<td align="left" valign="top"><?php $res = json_decode($h['result']);
			if (empty($res))
				echo $h['result'];
			else
			{
				foreach ($res as $k => $v)
				{
					echo "<strong>{$k}</strong>:{$v}<br/>";
				}				
			}
			?></td>
		</tr>
		<?php $odd = !$odd;?>
		<?php endforeach;?>
	</tbody>
</table>
