<?php $this->load->helper('extjs');?>
<table class="sortable-onload-shpw-0 rowstyle-alt colstyle-alt no-arrow"
	summary="<?php echo $this->lang->line('Ventas por Títulos');?>">
	<caption><strong><?php echo $this->lang->line('Ventas por Títulos');?></strong>
	<br />
	<?php echo $this->lang->line('Sección');?>: <?php echo $seccion;?><br />
	<?php if (isset($fecha1)):?> <?php echo $this->lang->line('Desde');?>:
	<?php echo format_date($fecha1);?><br />
	<?php endif;?> <?php if (isset($fecha2)):?> <?php echo $this->lang->line('Hasta');?>:
	<?php echo format_date($fecha2);?><br />
	<?php endif;?> <?php if (isset($min)) echo $this->lang->line('Mínimo') . ':' . $min .'<br/>';?>
	</caption>

	<thead>
		<tr>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Id');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Autores');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Título');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Editorial');?></th>
			<th class="sortable-currency" scope="col"><?php echo $this->lang->line('fPVP');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Ventas');?></th>
		</tr>
	</thead>

	<tbody>
	<?php $odd = FALSE;?>
	<?php $cantidad = 0;?>
	<?php foreach($valores as $v):?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td><?php echo format_enlace_cmd($v['nIdLibro'], site_url('catalogo/articulo/index/' . $v['nIdLibro']));;?></td>
			<td><?php echo format_text($v['cAutores']);?></td>
			<td><?php echo format_text($v['cTitulo']);?></td>
			<td><?php echo format_text($v['cNombre']);?></td>
			<td align="right"><?php echo format_price(format_add_iva($v['fPrecio'], $v['fIVA']));?></td>
			<td align="right"><?php echo format_number($v['nCantidad']);?></td>
		</tr>
		<?php $odd = !$odd;?>
		<?php $cantidad += $v['nCantidad'];?>
		<?php endforeach;?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="5" scope="row" align="right"><?php echo count($valores);?>
			<?php echo $this->lang->line('registros');?></td>
			<td align="right"><?php echo format_number($cantidad);?></td>
		</tr>
	</tfoot>
</table>
