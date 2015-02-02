<?php $this->load->helper('asset');?>
<div class="details-panel">
<table class="sortable-onload-shpw-0 rowstyle-alt colstyle-alt no-arrow"
	summary="<?php echo $this->lang->line('Stocks en las secciones');?>">
	<caption><strong><?php echo $this->lang->line('Stocks en las secciones');?></strong></caption>
	<thead>
		<tr>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Id');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Sección');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('FM');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('DP');?></th>
		</tr>
	</thead>
	<tbody>
	<?php $odd = FALSE;?>
	<?php $dp = $fm = 0; ?>
	<?php foreach ($stocks as $seccion):?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
		<td><?php echo $seccion['nIdSeccion'];?></td>
		<td><b><?php echo $seccion['cNombre'];?> (<?php echo $seccion['nIdSeccion'];?>)</b></td>
		<td align="right"><?php echo format_number($seccion['nStockFirme']);?></td>
		<td align="right"><?php echo format_number($seccion['nStockDeposito']);?></td>
	</tr>
		<?php $odd = !$odd;?>
		<?php $fm += $seccion['nStockFirme'];?>
		<?php $dp += $seccion['nStockDeposito'];?>
	<?php endforeach;?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="2" scope="row" align="right">&nbsp;</td>
			<td scope="row" align="right"><?php echo format_number($fm);?></td>
			<td scope="row" align="right"><?php echo format_number($dp);?></td>
		</tr>
		<tr>
			<td colspan="4" scope="row" align="right"><?php echo count($stocks);?>
			<?php echo $this->lang->line('registros');?></td>
		</tr>
	</tfoot>
</table>
</div>
