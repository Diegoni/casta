<?php $this->load->helper('extjs');?>
<table class="sortable-onload-shpw-0 rowstyle-alt colstyle-alt"
	summary="<?php echo $this->lang->line('Albaranes del concurso');?>">
	<caption><?php echo $this->lang->line('Albaranes del concurso');?><br />
	<strong><?php echo $concurso;?></strong>
	</caption>
	<thead>
		<tr>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Proveedor');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Número');?></th>
			<th class="sortable-date-dmy" scope="col"><?php echo $this->lang->line('Fecha Proveedor');?></th>
			<th class="sortable-date-dmy" scope="col"><?php echo $this->lang->line('Entrada');?></th>
			<th class="sortable-currency" scope="col"><?php echo $this->lang->line('Importe');?></th>
		</tr>
	</thead>
	<tbody>
	<?php $odd = FALSE;?>
	<?php foreach($valores as $m):?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td><?php echo $m['cProveedor'];?></td>
			<td><?php echo $m['cDescripcion'];?></td>
			<td><?php echo format_date($m['dFechaEntrada']);?></td>
			<td><?php echo format_date($m['dFechaProceso']);?></td>
			<td align="right"><?php echo (isset($m['fImporte']))?format_price($m['fImporte']):'&nbsp;';?></td>
		</tr>
		<?php $odd = !$odd;?>
		<?php endforeach;?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="5" scope="row" align="right"><?php echo count($valores);?>
			<?php echo $this->lang->line('registros');?></td>
		</tr>
	</tfoot>
</table>
