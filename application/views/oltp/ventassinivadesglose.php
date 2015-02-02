<table
	class="rowstyle-alt colstyle-alt no-arrow"
	summary="<?php echo $this->lang->line('report-ventas-exentas-iva');?> <?php echo $fecha1; ?> &lt;-&gt; <?php echo $fecha2; ?>">
	<caption><?php echo $this->lang->line('report-ventas-exentas-iva');?> <?php echo $fecha1; ?>
	&lt;-&gt; <?php echo $fecha2; ?></caption>
	<thead>
		<tr>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Cliente');?></th>
			<th class="sortable-date" scope="col"><?php echo $this->lang->line('Fecha');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Factura');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Importe');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Pais');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Región');?></th>
		</tr>
	</thead>
	<tbody>
	<?php $odd = FALSE;?>
	<?php foreach($valores as $k => $v):?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td scope="row"><?php echo $v['cCliente'];?></td>
			<td><?php echo $v['dFecha'];?></td>
			<td><?php echo $v['nNumero'];?>-<?php echo $v['nSerie'];?></td>
			<td align="right"><?php echo format_price($v['fBaseImponible']);?></td>
			<td><?php echo $v['cPais'];?></td>
			<td><?php echo $v['cRegion'];?></td>
		</tr>
		<?php $odd = !$odd;?>
		<?php endforeach;?>
	</tbody>
</table>
