<?php $this->load->helper('extjs');?>
<table class="rowstyle-alt colstyle-alt no-arrow"
	summary="<?php echo sprintf($this->lang->line('stock_contado_articulo'), $articulo['cTitulo']);?>">
	<caption><strong><?php echo $articulo['cTitulo'];?></strong> <br />
	<?php echo $this->lang->line('Id');?> :<?php echo $articulo['id'];?>
	</caption>
	<thead>
		<tr>
			<th scope="col"><?php echo $this->lang->line('Sección');?></th>
			<th scope="col"><?php echo $this->lang->line('Tipo');?></th>
			<th scope="col"><?php echo $this->lang->line('Cantidad');?></th>
			<!-- <th scope="col"><?php echo $this->lang->line('Procesado');?></th>
			<th scope="col"><?php echo $this->lang->line('Creado');?></th> -->
		</tr>
	</thead>
	<tbody>
	<?php $odd = FALSE;?>
	<?php foreach($docs as $m):?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td><?php echo $m['cNombre'];?></td>
			<td><?php echo $m['cDescripcion'];?></td>
			<td align="right"><?php echo format_number($m['nCantidad']);?></td>
			<!-- <td><?php echo format_date($m['dCreacion']);?></td> -->
		</tr>
		<?php $odd = !$odd;?>
		<?php endforeach;?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="11" scope="row" align="right"><?php echo count($docs);?>
			<?php echo $this->lang->line('registros');?></td>
		</tr>
	</tfoot>
</table>
