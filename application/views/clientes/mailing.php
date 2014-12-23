<table class="sortable-onload-shpw-2 rowstyle-alt colstyle-alt">
	<thead>
		<tr>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Id');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Fecha');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Email');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Boletín');?></th>
		</tr>
	</thead>
	<tbody>
	<?php $odd = FALSE;?>
	<?php foreach($mailing as $m):?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td><?php echo format_enlace_cmd($m['nIdMailing'], site_url('mailing/mailing/index/' . $m['nIdMailing']));?>
			</td>
			<td align="right"><?php echo isset($m['dEnvio'])?format_datetime($m['dEnvio']):'';?></td>
			<td><?php echo $m['cEmail'];?></td>
			<td><?php echo $m['cDescripcion'];?></td>
		</tr>
		<?php $odd = !$odd;?>
		<?php endforeach;?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="11" scope="row" align="right"><?php echo count($mailing);?>
			<?php echo $this->lang->line('registros');?></td>
		</tr>
	</tfoot>
</table>
