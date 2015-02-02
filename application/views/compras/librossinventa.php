<?php $this->load->helper('extjs');?>
<table class="rowstyle-alt colstyle-alt no-arrow"
	summary="<?php echo $title . ' ' . $seccion['cNombre'];?>">
	<caption><strong><?php echo $title;?></strong> <br />
	<?php echo $seccion['cNombre'];?></caption>
	<thead>
		<tr>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Id');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Sección');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('cTitulo');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('cAutores');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Stock');?></th>
			<th class="sortable-currency" scope="col"><?php echo $this->lang->line('fPrecio');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Proveedor');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Editorial');?></th>
			<?php if (isset($salida) && $salida):?>
				<th class="sortable" scope="col"><?php echo $this->lang->line('Ult. Venta');?></th>
			<?php endif; ?>
			<?php if (isset($entrada) && $entrada):?>
				<th class="sortable" scope="col"><?php echo $this->lang->line('Ult. Compra');?></th>
			<?php endif; ?>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Ubicación');?></th>
		</tr>
	</thead>
	<tbody>
	<?php $odd = FALSE;?>
	<?php $stock = 0; ?>
	<?php foreach($titulos as $m):?>
		<?php
		$cmp = datediff('d', $m['dEntrada'], time(), TRUE);
		$vnt = datediff('d', $m['dUltimaVenta'], time(), TRUE);
		?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td><?php echo format_enlace_cmd($m['nIdLibro'], site_url('catalogo/articulo/index/' . $m['nIdLibro']));?></td>
			<td><?php echo ($m['cSeccion']);?></td>
			<td><?php echo $m['cTitulo'];?></td>
			<td><?php echo $m['cAutores'];?></td>
			<td align="right"><?php echo (isset($m['fFirme']))?format_number($m['fFirme']):'&nbsp;';?></td>
			<td align="right"><?php echo (isset($m['fImporte']))?format_price($m['fImporte']):'&nbsp;';?></td>
			<td><?php echo format_name($m['cNombre'], $m['cApellido'], $m['cEmpresa']);?> (<?php echo format_enlace_cmd($m['nIdProveedor'], site_url('proveedores/proveedor/index/' . $m['nIdProveedor']));?>)</td>
			<td><?php echo (isset($m['cEditorial']))?($m['cEditorial']):'&nbsp;';?></td>
			<?php if (isset($salida) && $salida):?>
				<td>
				<?php if (isset($m['dUltimaVenta'])):?>
					<?php echo format_date($m['dUltimaVenta']);?> [<span style="color: blue;"><?php echo $vnt;?></span>]
				<?php else:?>
					<strong><span style="color: red;"><?php echo $this->lang->line('NO HAY VENTAS');?></span></strong>
				<?php endif; ?>
				</td>
			<?php endif; ?>
			<?php if (isset($entrada) && $entrada):?>
				<td><?php echo (isset($m['dEntrada']))?(format_date($m['dEntrada'])):'&nbsp;';?> [<span style="color: blue;"><?php echo $cmp;?></span>]</td>
			<?php endif; ?>
			<td><?php if (isset($m['ubicaciones'])):?> <?php $ub = array();?> <?php foreach ($m['ubicaciones'] as $u):?>
			<?php $ub[] = $u['cDescripcion'];?> <?php endforeach;?> <?php if (count($ub) > 0) echo implode(', ', $ub);?>
			<?php endif;?> &nbsp;</td>
		</tr>
		<?php $odd = !$odd;?>
		<?php $stock += $m['fFirme'];?>
		<?php endforeach;?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="4" scope="row"><?php echo count($titulos);?> <?php echo $this->lang->line('registros');?></td>
			<td align="right"><?php echo format_number($stock);?></td>
			<?php $cols=4; ?>
			<?php if (isset($salida) && $salida) ++$cols; ?>
			<?php if (isset($entrada) && $entrada) ++$cols;?>
			<td colspan="<?php echo $cols;?>" scope="row" align="right">&nbsp;</td>
		</tr>
	</tfoot>
</table>
