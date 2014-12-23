<table summary="<?php echo $this->lang->line('Mensajes');?>  <?php echo $username;?>"
	id="tab_resumen">
	<caption><?php echo $this->lang->line('Mensajes');?>  <?php echo $username;?></caption>
	<thead>
		<tr>
			<th class="sortable" scope="col"><?php echo $this->lang->line('dCreacion');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('cOrigen');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('tMensaje');?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="3"><?php echo count($mensajes);?> <?php echo $this->lang->line('registros');?></td>
		</tr>
	</tfoot>
	<tbody>
	<?php $odd = FALSE;?>
	<?php foreach($mensajes as $h):?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td scope="row"><?php echo format_datetime($h['dCreacion']);?></td>
			<td><?php echo $h['cOrigen'];?></td>
			<td><?php echo ($h['tMensaje']);?></td>
		</tr>
		<?php $odd = !$odd;?>
		<?php endforeach;?>
	</tbody>
</table>
<script>
//console.dir(parent);
</script>