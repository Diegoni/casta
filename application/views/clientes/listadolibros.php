<table class="sortable-onload-shpw-2 rowstyle-alt colstyle-alt">
	<thead>
		<tr>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Id');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Cantidad');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Título');?></th>
			<th class="sortable-currency" scope="col"><?php echo $this->lang->line('Precio');?></th>
		</tr>
	</thead>
	<tbody>
	<?php $odd = FALSE;?>
	<?php foreach($libros as $m):?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td><?php echo format_enlace_cmd($m['nIdLibro'], site_url('catalogo/articulo/index/' . $m['nIdLibro']));?>
			</td>
			<td align="right"><?php echo (isset($m['nCantidad']))?format_number($m['nCantidad']):'1';?></td>
			<td><?php echo $m['cTitulo'];?></td>
			<td align="right"><?php echo (isset($m['fPVP']))?format_price($m['fPVP']):'&nbsp;';?></td>
		</tr>
		<?php $odd = !$odd;?>
		<?php endforeach;?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="11" scope="row" align="right"><?php echo count($libros);?>
			<?php echo $this->lang->line('registros');?></td>
		</tr>
	</tfoot>
</table>
