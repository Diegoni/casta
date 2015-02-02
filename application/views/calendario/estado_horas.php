<table summary="<?php echo $this->lang->line('estado-horas');?>  <?php echo $year;?>"
class="sortable-onload-0 rowstyle-alt colstyle-alt no-arrow"
id="tab_resumen">
	<caption>
		<?php echo $this->lang->line('estado-horas');?>  <?php echo $year;?>
	</caption>
	<thead>
		<tr>
			<th class="sortable" scope="col"><?php echo $this->lang->line('cNombre');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('nAnno');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Total');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('fHoras');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Extras');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Difencia');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('AFavor');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('EnContra');?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="8"><?php echo count($horas);?> <?php echo $this->lang->line('registros');?></td>
		</tr>
	</tfoot>
	<tbody>
		<?php $odd = FALSE;?>
		<?php foreach($horas as $h):
		?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td scope="row"><?php echo format_enlace_cmd($h['cNombre'], site_url('calendario/trabajador/index/' . $h['nIdTrabajador']));?></td>
			<td><?php echo $h['nAnno'];?></td>
			<td align="right"><?php echo format_number($h['Total']);?></td>
			<td align="right"><?php echo format_number($h['fHoras']);?></td>
			<td align="right"><?php echo format_number($h['Extras']);?></td>
			<td align="right"><?php echo format_number($h['Diferencia']);?></td>
			<td align="right"><?php echo format_number($h['AFavor']);?></td>
			<td align="right"><?php echo format_number($h['EnContra']);?></td>
		</tr>
		<?php $odd = !$odd;?>
		<?php endforeach;?>
	</tbody>
</table>
