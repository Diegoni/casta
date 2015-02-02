<?php $this->load->helper('extjs');?>
<table class="sortable-onload-shpw-0 rowstyle-alt colstyle-alt no-arrow"
summary="<?php echo $this->lang->line('Cálculo de comisiones');?> <?php echo $mes;?>-<?php echo $year;?>">
	<caption>
	<?php echo $this->lang->line('Cálculo de comisiones');?> <?php echo $mes;?>-<?php echo $year;?>
	</caption>
	<thead>
		<tr class="HeaderStyle">
			<th class="sortable" scope="col"><?php echo $this->lang->line('Escuela');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Venta');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('fDescuento');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('fComision');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('fImporte');?></th>
		</tr>
	</thead>
	<tbody>
		<?php $odd = FALSE;?>
		<?php foreach($valores as $k => $valor): ?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td align="left"><?php echo $valor['cDescripcion'];?></td>
			<td align="right"><?php echo format_price($valor['fVenta']);?></td>
			<td align="right" style="color:green;"><?php echo format_percent($valor['fDescuento']);?></td>
			<td align="right"><?php echo format_percent($valor['fComision']);?></td>
			<td align="right" style="color:<?php echo ($valor['fImporte'] < 0)?'red':'blue';?>;"><?php echo format_price($valor['fImporte']);?></td>
		</tr>
		<?php $odd = !$odd;?>
		<?php endforeach;?>
	</tbody>
</table>
