<?php $this->load->library('HtmlFile'); ?>
<?php echo get_instance()->htmlfile->orientation(ORIENTATION_LANDSCAPE); ?>
<?php $this->load->helper('extjs');?>
<table class="sortable-onload-shpw-0 rowstyle-alt colstyle-alt no-arrow"
summary="">
	<thead>
		<tr>
			<th scope="col">#</th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Pedido');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Cliente');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Id');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('cTitulo');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('cAutores');?></th>
			<?php if ($seccion): ?>
				<th class="sortable" scope="col"><?php echo $this->lang->line('cSeccion');?></th>
			<?php endif; ?>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Qt');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Stk');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Vnt');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Cmp');?></th>
		</tr>
	</thead>
	<tbody>
		<?php $odd = FALSE;?>
		<?php foreach($titulos as $m):?>
		<?php
		$cmp = datediff('d', $m['dUltimaCompra'], time(), TRUE);
		$vnt = datediff('d', $m['dUltimaVenta'], time(), TRUE);
		?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
		<td
			width="<?php echo $this->config->item('bp.catalogo.cover.small');?>">
			<?php echo format_cover($m['nIdLibro'], $this->config->item('bp.catalogo.cover.small'));?>
		</td>
			<td><?php echo $m['nIdPedido'];?></td>
			<td><?php echo $m['cEmpresa'];?></td>
			<td><?php echo format_enlace_cmd($m['nIdLibro'], site_url('catalogo/articulo/index/' . $m['nIdLibro']));?></td>
			<td><?php if (($m['nStock'] > $m['nCantidad']) || ($vnt > 30) || ($cmp > 60)): ?>
				<strong><?php echo $m['cTitulo'];?></strong>
				<?php else:?>
				<?php echo $m['cTitulo'];?>
				<?php endif; ?>
			</td>
			<td><?php echo $m['cAutores'];?></td>
			<?php if ($seccion): ?>		
				<td><?php echo $m['cNombre'];?></td>
			<?php endif; ?>
			<td><?php echo $m['nCantidad'];?></td>
			<td><?php echo $m['nStock'];?></td>
			<td><?php if (isset($m['dUltimaVenta'])):?>
				<?php echo format_date($m['dUltimaVenta']);?> [<span style="color: blue;"><?php echo $vnt;?></span>]
			<?php else:?>
				<strong><span style="color: red;"><?php echo $this->lang->line('NO HAY VENTAS');?></span></strong>
			<?php endif; ?>
			</td>
			<td><?php echo format_date($m['dUltimaCompra']);?> [<span style="color: blue;"><?php echo $cmp;?></span>]</td>
		</tr>
		<?php $odd = !$odd;?>
		<?php endforeach;?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="10" scope="row"><?php echo count($titulos);?> <?php echo $this->lang->line('registros encontrados');?></td>
		</tr>
	</tfoot>
</table>
