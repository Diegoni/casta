<?php $this->load->library('HtmlFile'); ?>
<?php echo get_instance()->htmlfile->orientation(ORIENTATION_LANDSCAPE); ?>
<?php $this->load->helper('extjs');?>
<table class="sortable-onload-shpw-0 rowstyle-alt colstyle-alt no-arrow"
summary="<?php echo $title;?>">
	<caption>
		<strong><?php echo $title;?>
		<?php if (isset($stock)):?>	(<?php echo $this->lang->line('mayor o igual');?> <?php echo $stock;?>) <?php endif;?></strong>
	</caption>
	<thead>
		<tr>
			<th scope="col">#</th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Id');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Sección');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('cISBN');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('cTitulo');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('cAutores');?></th>
			<th class="sortable-currency" scope="col"><?php echo $this->lang->line('fPrecio');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Proveedor');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Editorial');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Ubicación');?></th>
		</tr>
	</thead>
	<tbody>
		<?php $odd = FALSE;?>
		<?php foreach($titulos as $m):
		?>
		<?php
		$ub = array();
		foreach ($m['secciones'] as $u)
		{
			if (!isset($stock) || ($u['nStockFirme'] + $u['nStockDeposito']) >= $stock)
			{
				$ub[] = $u['cNombre'] . '(' . ($u['nStockFirme'] + $u['nStockDeposito']) . ')';
			}
		}
		?>
		<?php if ((count($ub) > 0) || ($stock == 0)):
		?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
		<td
			width="<?php echo $this->config->item('bp.catalogo.cover.small');?>">
			<?php echo format_cover($m['nIdLibro'], $this->config->item('bp.catalogo.cover.small'));?>
		</td>
			<td><?php echo format_enlace_cmd($m['nIdLibro'], site_url('catalogo/articulo/index/' . $m['nIdLibro']));?></td>
			<td><?php if (isset($m['secciones'])):
			?> <?php
				if (count($ub) > 0)
					echo implode(', ', $ub);
			?> <?php endif;?>&nbsp;</td>
			<td nowrap="nowrap"><?php echo $m['cISBN'];?></td>
			<td><?php echo $m['cTitulo'];?></td>
			<td><?php echo $m['cAutores'];?></td>
			<td align="right"><?php echo (isset($m['fPVP']))?format_price($m['fPVP']):'&nbsp;';
			?></td>
			<td><?php echo($m['cProveedor']);?>(<?php echo format_enlace_cmd($m['nIdProveedor'], site_url('proveedores/proveedor/index/' . $m['nIdProveedor']));?>)</td>
			<td><?php echo (isset($m['cEditorial']))?($m['cEditorial']):'&nbsp;';
			?></td>
			<td><?php if (isset($m['ubicaciones'])):
			?> <?php $ub = array();?> <?php foreach ($m['ubicaciones'] as $u):
			?>
			<?php $ub[] = $u['cDescripcion'];?> <?php endforeach;?> <?php
				if (count($ub) > 0)
					echo implode(', ', $ub);
			?>
			<?php endif;?>&nbsp;</td>
		</tr>
		<?php $odd = !$odd;?>
		<?php endif;?>
		<?php endforeach;?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td colspan="10"><?php echo implode(', ', $no);?></td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="10" scope="row"><?php echo count($titulos);?> <?php echo $this->lang->line('registros encontrados');?></td>
		</tr>
		<tr>
			<td colspan="10" scope="row"><?php echo count($no);?> <?php echo $this->lang->line('registros no encontrados');?></td>
		</tr>
	</tfoot>
</table>
