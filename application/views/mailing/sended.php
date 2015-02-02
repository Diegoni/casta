<?php $this->load->helper('extjs');?>
<table
	summary="<?php echo $this->lang->line('mensajes_enviados');?> <?php echo $email;?>">
	<caption><?php echo $this->lang->line('mensajes_enviados');?> 
	<br />
	<?php echo $email;?></caption>
	<thead>
		<tr>
			<th scope="col"><?php echo $this->lang->line('Id');?></th>
			<th scope="col"><?php echo $this->lang->line('Mailing');?></th>
			<th scope="col"><?php echo $this->lang->line('nIdEstado');?></th>
			<th scope="col"><?php echo $this->lang->line('cEstado');?></th>
			<th scope="col"><?php echo $this->lang->line('dEnvio');?></th>
			<th scope="col"><?php echo $this->lang->line('cOrigen');?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="6" scope="row" align="right"><?php echo count($mailings);?> <?php echo $this->lang->line('registros');?></td>
		</tr>
	</tfoot>
	<tbody>
	<?php $odd = FALSE;?>
	<?php foreach($mailings as $m):?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td><?php echo extjs_command('mailing/mailing/index/' . $m['nIdMailing'], $m['nIdMailing']);?></td>
			<td><?php echo $m['cDescripcion'];?></td>
			<td><?php echo $m['nIdEstado'];?></td>
			<td><?php echo $m['cEstado'];?></td>
			<td><?php echo format_datetime($m['dEnvio']);?></td>
			<td><?php echo $m['cOrigen'];?></td>
		</tr>
		<?php $odd = !$odd;?>
		<?php endforeach;?>
	</tbody>
</table>
