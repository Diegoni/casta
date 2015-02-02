<?php $this->load->helper('extjs');?>
<table class="rowstyle-alt colstyle-alt no-arrow"
	summary="<?php echo $this->lang->line('Ajustes de stock contado');?>">
	<caption><?php echo $this->lang->line('Ajustes de stock contado');?></caption>
	<thead>
		<tr>
			<th scope="col"><?php echo $this->lang->line('Id');?></th>
			<th scope="col"><?php echo $this->lang->line('Sección');?></th>
			<th scope="col"><?php echo $this->lang->line('FM-');?></th>
			<th scope="col"><?php echo $this->lang->line('DP-');?></th>
			<th scope="col"><?php echo $this->lang->line('FM+');?></th>
			<th scope="col"><?php echo $this->lang->line('DP+');?></th>
		</tr>
	</thead>
	<tbody>
	<?php $fm1 = $fm2 = $dp1 = $dp2 = 0; ?>
	<?php $odd = FALSE;?>
	<?php foreach($stocks as $k => $m):?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td><?php echo $k;?></td>
			<td><?php echo $m['cNombre'];?></td>
			<td align="right"><?php echo format_number($m['fm-']);?></td>
			<td align="right"><?php echo format_number($m['dp-']);?></td>
			<td align="right"><?php echo format_number($m['fm+']);?></td>
			<td align="right"><?php echo format_number($m['dp+']);?></td>
		</tr>
		<?php $fm1 += $m['fm-'];?>
		<?php $dp1 += $m['dp-'];?>
		<?php $fm2 += $m['fm+'];?>
		<?php $dp2 += $m['dp+'];?>
		<?php $odd = !$odd;?>
		<?php endforeach;?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="2" scope="row" align="right">&nbsp;</td>
			<td scope="row" align="right"><?php echo format_number($fm1);?></td>
			<td scope="row" align="right"><?php echo format_number($dp1);?></td>
			<td scope="row" align="right"><?php echo format_number($fm2);?></td>
			<td scope="row" align="right"><?php echo format_number($dp2);?></td>
		</tr>
		<tr>
			<td colspan="11" scope="row" align="right"><?php echo count($stocks);?>
			<?php echo $this->lang->line('registros');?></td>
		</tr>
	</tfoot>
</table>
