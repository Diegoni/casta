<?php $this->load->library('HtmlFile'); ?>
<?php echo get_instance()->htmlfile->orientation(ORIENTATION_LANDSCAPE); ?>
<?php $this->load->helper('extjs');?>
<table class="sortable-onload-shpw-0 rowstyle-alt colstyle-alt no-arrow"
summary="<?php echo $this->lang->line('Liquidación depósitos') . ' ' . $id;?>">
	<caption>
		<strong><?php echo $this->lang->line('Liquidación depósitos') .' ' . $id;?></strong>
	</caption>
	<thead>
		<tr>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Id');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Doc.');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Ct');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('cISBN');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('cTitulo');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('cAutores');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Editorial');?></th>
		</tr>
	</thead>
	<tbody>
		<?php $odd = FALSE;?>
		<?php $total = 0; ?>
		<?php foreach($lineas as $m):?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td><?php echo format_enlace_cmd($m['nIdLibro'], site_url('catalogo/articulo/index/' . $m['nIdLibro']));?></td>
			<td><?php echo format_enlace_cmd($m['nIdDocumento'], site_url('compras/liquidaciondepositos/index/' . $m['nIdDocumento']));?></td>
			<td ><?php echo $m['nCantidad'];?></td>
			<td nowrap="nowrap"><?php echo $m['cISBN'];?></td>
			<td><?php echo $m['cTitulo'];?></td>
			<td><?php echo $m['cAutores'];?></td>
			<td><?php echo (isset($m['cEditorial']))?($m['cEditorial']):'&nbsp;';?></td>
		</tr>
		<?php $total += $m['nCantidad']; ?>
		<?php $odd = !$odd;?>
		<?php endforeach;?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="9" scope="row">
				<?php echo count($lineas);?> <?php echo $this->lang->line('registros encontrados');?>, 
				<?php echo $total;?> <?php echo $this->lang->line('unidades');?>
			</td>
		</tr>
	</tfoot>
</table>
