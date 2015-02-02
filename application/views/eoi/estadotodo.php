<?php $this->load->helper('extjs');?>
<table class="sortable-onload-shpw-0 rowstyle-alt colstyle-alt no-arrow"
summary="<?php echo $this->lang->line('estado-cuenta');?> <?php echo $fecha;?>">
	<caption>
		<?php echo $this->lang->line('estado-cuenta');?>
		<?php echo $fecha;?>
	</caption>
	<thead>
		<tr class="HeaderStyle">
			<th class="sortable" scope="col"><?php echo $this->lang->line('Escuela');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('fImporte');?></th>
		</tr>
	</thead>
	<tbody>
		<?php $odd = FALSE;?>
		<?php foreach($valores as $k => $valor):
		?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td align="left"><?php echo $k;?></td>
			<td align="right" style="color:<?php echo ($valor < 0)?'red':'blue';?>;"><?php echo format_price($valor);?></td>
		</tr>
		<?php $odd = !$odd;?>
		<?php endforeach;?>
</table>
