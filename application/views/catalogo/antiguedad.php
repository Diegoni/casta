<?php $this->load->helper('extjs');?>
<table class="rowstyle-alt colstyle-alt no-arrow"
	summary="<?php echo sprintf($this->lang->line('antiguedad_articulo'), $articulo['cTitulo']);?>">
	<caption><strong><?php echo $articulo['cTitulo'];?></strong> <br />
	<?php echo $this->lang->line('Id');?> :<?php echo $articulo['id'];?>
	</caption>
	<thead>
		<tr>
			<th scope="col"><?php echo $this->lang->line('Fecha');?></th>
			<th scope="col"><?php echo $this->lang->line('DP');?></th>
			<th scope="col"><?php echo $this->lang->line('F1');?></th>
			<th scope="col"><?php echo $this->lang->line('F2');?></th>
			<th scope="col"><?php echo $this->lang->line('F3');?></th>
			<th scope="col"><?php echo $this->lang->line('F4');?></th>
			<th scope="col"><?php echo $this->lang->line('Coste');?></th>
			<th scope="col"><?php echo $this->lang->line('E1');?></th>
			<th scope="col"><?php echo $this->lang->line('E2');?></th>
			<th scope="col"><?php echo $this->lang->line('E3');?></th>
			<th scope="col"><?php echo $this->lang->line('E4');?></th>
		</tr>
	</thead>
	<tbody>
	<?php $odd = FALSE;?>
	<?php foreach($docs as $m):?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td><?php echo format_date($m['dCreacion']);?></td>
			<td><?php echo format_number($m['nDeposito']);?></td>
			<td><?php echo format_number($m['nFirme1']);?></td>
			<td><?php echo format_number($m['nFirme2']);?></td>
			<td><?php echo format_number($m['nFirme3']);?></td>
			<td><?php echo format_number($m['nFirme4']);?></td>
			<td align="right"><?php echo (isset($m['fCoste']))?format_price($m['fCoste']):'&nbsp;';?></td>
			<td><?php echo format_number($m['nEntradas1']);?></td>
			<td><?php echo format_number($m['nEntradas2']);?></td>
			<td><?php echo format_number($m['nEntradas3']);?></td>
			<td><?php echo format_number($m['nEntradas4']);?></td>
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
