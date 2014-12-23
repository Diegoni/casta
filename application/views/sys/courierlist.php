<table summary="<?php echo $this->lang->line('Envios Courier');?> <br/>
	<?php echo format_date($desde);?> -> <?php echo format_date($hasta);?>"
	id="tab_resumen">
	<caption><?php echo $this->lang->line('Envios Courier');?> <br/>
	<?php echo format_date($desde);?> -> <?php echo format_date($hasta);?></caption>
	<thead>
		<tr>
			<th class="sortable" scope="col"><?php echo $this->lang->line('dCreacion');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Envio');?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="3"><?php echo count($envios);?> <?php echo $this->lang->line('registros');?></td>
		</tr>
	</tfoot>
	<tbody>
	<?php $odd = FALSE;?>
	<?php foreach($envios as $h):?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td scope="row"><?php echo format_datetime($h['dCreacion']);?></td>
			<td><?php echo $h['tObservacion'];?></td>
		</tr>
		<?php $odd = !$odd;?>
		<?php endforeach;?>
	</tbody>
</table>
