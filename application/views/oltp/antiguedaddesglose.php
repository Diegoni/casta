<?php $this->load->helper('extjs');?>
<table class="rowstyle-alt colstyle-alt no-arrow"
	summary="<?php echo $this->lang->line('Desglose antiguedad');?>">
	<caption><strong><?php echo $this->lang->line('Desglose antiguedad') . ' ' . $fecha;?></strong> 
	</caption>
	<thead>
		<tr>
			<th scope="Col"><?php echo $this->lang->line('Sección'); ?></th>
			<th scope="Col"><?php echo $this->lang->line('Id'); ?></th>
			<th scope="Col"><?php echo $this->lang->line('cISBN'); ?></th>
			<th scope="Col"><?php echo $this->lang->line('cTitulo'); ?></th>
			<th scope="Col"><?php echo $this->lang->line('cAutores'); ?></th>
			<th scope="Col"><?php echo $this->lang->line('cProveedor'); ?></th>
			<th scope="col"><?php echo $this->lang->line('FM');?></th>
			<th scope="col"><?php echo $this->lang->line('IMP');?></th>
			<th scope="col"><?php echo $this->lang->line('F1');?></th>
			<th scope="col"><?php echo $this->lang->line('V1');?></th>
			<th scope="col"><?php echo $this->lang->line('F2');?></th>
			<th scope="col"><?php echo $this->lang->line('V2');?></th>
			<th scope="col"><?php echo $this->lang->line('F3');?></th>
			<th scope="col"><?php echo $this->lang->line('V3');?></th>
			<th scope="col"><?php echo $this->lang->line('F4');?></th>
			<th scope="col"><?php echo $this->lang->line('V4');?></th>
		</tr>
	</thead>
	<tbody>
	<?php $odd = FALSE;?>
	<?php $v1 = $v2 = $v3 = $v4 = $f1 = $f2 = $f3 = $f4 = $v = $f = 0; ?>
	<?php foreach($valores as $seccion):?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td><?php echo $seccion['cSeccion']; ?></td>
			<td><?php echo format_enlace_cmd($seccion['nIdLibro'], site_url('catalogo/articulo/index/' . $seccion['nIdLibro']));?></td>
			<td><?php echo $seccion['cISBN']; ?></td>
			<td><?php echo $seccion['cTitulo']; ?></td>
			<td><?php echo $seccion['cAutores']; ?></td>
			<td><?php echo format_name($seccion['cNombre'], $seccion['cApellido'], $seccion['cEmpresa']);?></td>
			<td><?php echo format_number($seccion['StockTotal']);?></td>
			<td><?php echo format_price($seccion['ImporteTotal']); ?></td>
			<td><?php echo format_number($seccion['Firme1']); ?></td>
			<td><?php echo format_price($seccion['Importe1']); ?></td>
			<td><?php echo format_number($seccion['Firme2']); ?></td>
			<td><?php echo format_price($seccion['Importe2']); ?></td>
			<td><?php echo format_number($seccion['Firme3']); ?></td>
			<td><?php echo format_price($seccion['Importe3']); ?></td>
			<td><?php echo format_number($seccion['Firme4']); ?></td>
			<td><?php echo format_price($seccion['Importe4']); ?></td>
		</tr>
		<?php $odd = !$odd;?>
	<?php
		$v1 += $seccion['Firme1']; $v2 += $seccion['Firme2']; $v3 += $seccion['Firme3']; $v4 += $seccion['Firme4'];
		$f1 += $seccion['Importe1']; $f2 += $seccion['Importe2']; $f3 += $seccion['Importe3']; $f4 += $seccion['Importe4']; 
		$v += $seccion['ImporteTotal'];
		$f += $seccion['StockTotal'];
	?>
		<?php endforeach;?>
	</tbody>
	<tfoot>
		<tr>
			<td scope="row" colspan="6">&nbsp;</td>
			<td scope="Row"><?php echo format_price($v); ?></td>
			<td scope="Row"><?php echo format_number($f);?></td>
			<td scope="Row"><?php echo format_price($v1); ?></td>
			<td scope="Row"><?php echo format_number($f1); ?></td>
			<td scope="Row"><?php echo format_price($v2); ?></td>
			<td scope="Row"><?php echo format_number($f2); ?></td>
			<td scope="Row"><?php echo format_price($v3); ?></td>
			<td scope="Row"><?php echo format_number($f3); ?></td>
			<td scope="Row"><?php echo format_price($v4); ?></td>
			<td scope="Row"><?php echo format_number($f4); ?></td>
		</tr>
		<tr>
			<td colspan="16" scope="row" align="right"><?php echo count($valores);?>
			<?php echo $this->lang->line('registros');?></td>
		</tr>
	</tfoot>
</table>
