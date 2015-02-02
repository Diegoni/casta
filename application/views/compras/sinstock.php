<?php $this->load->helper('extjs');?>
<div class="details-panel">
<table>
	<tr class="label">
		<th>&nbsp;</th>
		<th><?php echo $this->lang->line('Id');?></th>
		<th><?php echo $this->lang->line('Título');?></th>
		<th><?php echo $this->lang->line('Sección');?></th>
		<th><?php echo $this->lang->line('Cantidad');?></th>
		<th><?php echo $this->lang->line('Disponible');?></th>
		<th><?php echo $this->lang->line('Exceso');?></th>
	</tr>

	<?php foreach ($errores as $error):?>
	<tr>
		<td
			width="<?php echo $this->config->item('bp.catalogo.cover.datosventa');?>"><?php echo format_cover($error['nIdLibro'], $this->config->item('bp.catalogo.cover.datosventa'), 'portada');?></td>
		<td class="label"><?php echo $error['nIdLibro'];?></td>
		<td class="info"><?php echo format_enlace_cmd($error['cTitulo'], site_url('catalogo/articulo/index/' . $error['nIdLibro']));?></td>
		<td class="info"><?php echo $error['cSeccion'];?></td>
		<td class="info"><?php echo $error['nCantidad'];?></td>
		<td class="info"><?php echo $error['nCantidad'] - $error['nExceso'];?></td>
		<td class="info"><?php echo $error['nExceso'];?></td>
	</tr>

	<?php endforeach;?>
</table>
</div>
