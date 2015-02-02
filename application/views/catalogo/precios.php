<?php $this->load->library('HtmlFile');?>
<table class="sortable-onload-shpw-0 rowstyle-alt colstyle-alt no-arrow"
summary="<?php echo $cTitulo;?>">
	<caption>
		<strong><?php echo $cTitulo;?></strong>
		<br />
		<?php echo $this->lang->line('Id');?>
		:<?php echo $nIdLibro;?><br />
	</caption>
	<thead>
		<tr>
			<th class="sortable-date-dmy"><?php echo $this->lang->line('Fecha');?></th>
			<th class="sortable-numeric"><?php echo $this->lang->line('Antiguo');?></th>
			<th class="sortable-numeric"><?php echo $this->lang->line('Nuevo');?></th>
			<th class="sortable"><?php echo $this->lang->line('Usuario');?></th>
		</tr>
	</thead>
	<tbody>
		<?php $odd = FALSE;?>
		<?php foreach($precios as $m):
		?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td><?php echo format_datetime($m['dCambio']);?></td>
			<td align="right">(<?php echo format_price($m['fPrecioAntiguo']);?>) <span style="color: blue;"><?php echo format_price(format_add_iva($m['fPrecioAntiguo'], $fIVA));?></span></td>
			<td align="right">(<?php echo format_price($m['fPrecioNuevo']);?>) <span style="color: green;"><?php echo format_price(format_add_iva($m['fPrecioNuevo'], $fIVA));?></span></td>
			<td><?php echo $m['cCUser'];?></td>
		</tr>
		<?php $odd = !$odd;?>
		<?php endforeach;?>
	</tbody>
</table>
