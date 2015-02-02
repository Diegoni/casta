<?php $this->load->helper('extjs');?>
<table class="rowstyle-alt colstyle-alt"
	summary="<?php echo $this->lang->line('Avisos por crear');?>">
	<caption><?php echo $this->lang->line('Avisos por crear');?></caption>
	<thead>
		<tr>
			<th scope="col" class="sortable-numeric"><?php echo $this->lang->line('Suscripción');?></th>
			<th scope="col" class="sortable"><?php echo $this->lang->line('Cliente');?></th>
			<th scope="col" class="sortable"><?php echo $this->lang->line('Revista');?></th>
			<th scope="col" class="sortable-date-dmy"><?php echo $this->lang->line('Renovación');?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="6" scope="row" align="right"><?php echo count($suscripciones);?>
			<?php echo $this->lang->line('registros');?></td>
		</tr>
	</tfoot>
	<tbody>
	<?php $odd = FALSE;?>
	<?php foreach($suscripciones as $m):?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td><?php echo format_enlace_cmd($m['nIdSuscripcion'], site_url('suscripciones/suscripcion/index/' . $m['nIdSuscripcion']));?></td>
			<td><?php echo format_enlace_cmd(format_name($m['cNombre'], $m['cApellido'], $m['cEmpresa']), site_url('clientes/cliente/index/' . $m['nIdCliente']));?></td>
			<td><?php echo format_enlace_cmd($m['cTitulo'], site_url('catalogo/articulo/index/' . $m['nIdLibro']));?></td>
			<td><?php echo format_datetime($m['dRenovacion']);?></td>
		</tr>
		<?php $odd = !$odd;?>
		<?php endforeach;?>
	</tbody>
</table>
