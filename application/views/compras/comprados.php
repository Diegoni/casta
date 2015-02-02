<?php $this->load->helper('extjs');?>
<?php $this->load->library('HtmlFile'); ?>
<?php echo get_instance()->htmlfile->orientation(ORIENTATION_LANDSCAPE); ?>
<table class="sortable-onload-shpw-0 rowstyle-alt colstyle-alt no-arrow"
	summary="<?php echo $this->lang->line('Comprados');?>">
	<caption>
		<?php echo ($stock)?$this->lang->line('Comprados con stock'):$this->lang->line('Comprados');?>
			(<?php echo $proveedor['nIdProveedor']; ?>) <?php echo format_name($proveedor['cNombre'], $proveedor['cApellido'], $proveedor['cEmpresa']);?>
	<?php if (!empty($desde)):?> 
		<br /><?php echo $this->lang->line('Desde');?> :<?php echo format_date($desde);?><br />
	<?php endif;?> 
	<?php if (!empty($hasta)):?> 
		<?php echo $this->lang->line('Hasta');?> :<?php echo format_date($hasta);?>
	<?php endif;?>
	</caption>
	<thead>
		<tr>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Id');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('cTitulo');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('cAutores');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Editorial');?></th>
			<?php if ($stock): ?>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Seccion');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('FM');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('DP');?></th>
			<?php endif; ?>
		</tr>
	</thead>
	<tbody>
		<?php $odd = FALSE;?>
		<?php foreach($titulos as $m): ?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td><?php echo format_enlace_cmd($m['nIdLibro'], site_url('catalogo/articulo/index/' . $m['nIdLibro']));?></td>
			<td><?php echo $m['cTitulo'];?></td>
			<td><?php echo $m['cAutores'];?></td>
			<td><?php echo $m['cNombre'];?></td>
			<?php if ($stock): ?>
			<td><?php echo $m['cSeccion'];?></td>
			<td align="right"><?php echo format_number($m['nStockFirme']);?></td>
			<td align="right"><?php echo format_number($m['nStockDeposito']);?></td>
			<?php endif; ?>
		</tr>
		<?php $odd = !$odd;?>
		<?php endforeach;?>
	</tbody>
</table>
