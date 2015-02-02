<table
	summary="<?php echo $this->lang->line('Estado de la campaña');?> <?php echo $campana['cDescripcion'];?>">
	<caption><?php echo $this->lang->line('Estado de la campaña');?> <?php echo $campana['cDescripcion'];?></caption>
	<thead>
		<tr>
			<th scope="col"><?php echo $this->lang->line('Concepto');?></th>
			<th scope="col"><?php echo $this->lang->line('Avisos');?></th>
			<th scope="col"><?php echo $this->lang->line('%');?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td scope="row"><?php echo $this->lang->line('Avisos')?></td>
			<td align="right"><?php echo format_number($total);?></td>
			<td align="right"><?php format_percent(100);?></td>
		</tr>
		<tr class="alt">
			<td scope="row"><?php echo $this->lang->line('Gestionadas')?></td>
			<td align="right"><?php echo format_number($aceptadas['cantidad'] + $rechazadas['cantidad']);?></td>
			<td align="right"><?php echo format_percent(($total != 0)?(($aceptadas['cantidad'] + $rechazadas['cantidad'])*100 / $total):0);?></td>
		</tr>
		<tr>
			<td scope="row"><?php echo $this->lang->line('Faltan')?></td>
			<td align="right"><?php echo format_number($total - $aceptadas['cantidad'] - $rechazadas['cantidad']);?></td>
			<td align="right"><?php echo format_percent(($total != 0)?(($total - $aceptadas['cantidad'] - $rechazadas['cantidad'])*100 / $total):0);?></td>
		</tr>
		<tr class="alt">
			<td scope="row"><?php echo $this->lang->line('Aceptadas')?></td>
			<td align="right"><?php echo format_number($aceptadas['cantidad']);?></td>
			<td align="right"><?php echo format_percent((($aceptadas['cantidad'] + $rechazadas['cantidad']) != 0)?($aceptadas['cantidad'])*100 / ($aceptadas['cantidad'] + $rechazadas['cantidad']):0);?></td>
		</tr>
		<tr>
			<td scope="row"><?php echo $this->lang->line('Rechazadas')?></td>
			<td align="right"><?php echo format_number($rechazadas['cantidad']);?></td>
			<td align="right"><?php echo format_percent((($aceptadas['cantidad'] + $rechazadas['cantidad']) != 0)?($rechazadas['cantidad'])*100 / ($aceptadas['cantidad'] + $rechazadas['cantidad']):0);?></td>
		</tr>

	</tbody>
</table>

<table summary="<?php echo $this->lang->line('Medios de renovación');?>"
	class="sortable-onload-0 rowstyle-alt colstyle-alt">
	<caption><?php echo $this->lang->line('Medios de renovación');?></caption>
	<thead>
		<tr>
			<th class="sortable" scope="col"><?php echo $this->lang->line('cDescripcion');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Cantidad');?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="8"><?php echo count($medios);?> <?php echo $this->lang->line('registros');?></td>
		</tr>
	</tfoot>
	<tbody>
	<?php $odd = FALSE;?>
	<?php foreach($medios as $k => $v):?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td scope="row"><?php echo (!isset($k) || $k =='')?$this->lang->line('Sin gestionar'):$k;?></td>
			<td><?php echo $v;?></td>
		</tr>
		<?php $odd = !$odd;?>
		<?php endforeach;?>
	</tbody>
</table>
