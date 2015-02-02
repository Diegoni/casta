	<h2><?php echo $fecha1; ?> &lt;-&gt; <?php echo $fecha2; ?>
<?php if (isset($seccion)):?>
<br/><?php echo $this->lang->line('Sección'); ?>: <?php echo $seccion['cNombre']; ?>
<?php endif;?>
<?php if (isset($idproveedor)):?>
<br /><?php echo $this->lang->line('Proveedor'); ?>: <?php echo format_name($proveedor['cNombre'], $proveedor['cApellido'], $proveedor['cEmpresa']); ?>
<?php endif;?>
</h2>
<table cellspacing="0" cellpadding="3" class="SummaryDataGrid"
	style="BORDER-TOP-WIDTH: 0px; BORDER-LEFT-WIDTH: 0px; BORDER-BOTTOM-WIDTH: 0px; BORDER-COLLAPSE: collapse; BORDER-RIGHT-WIDTH: 0px">
	<tr class="HeaderStyle">
		<?php if (!$sinalbaran):?>
		<th class="HeaderStyle"><?php echo $this->lang->line('Sección');?></th>
		<?php endif; ?>
		<th class="HeaderStyle"><?php echo $this->lang->line('Unidades');?></th>
		<?php if (!$sinalbaran):?>
		<th class="HeaderStyle"><?php echo $this->lang->line('Importe');?></th>
		<th class="HeaderStyle"><?php echo $this->lang->line('Dto');?></th>
		<?php endif;?>
		<?php if ($desglosado):?>
		<?php if (!isset($idproveedor)):?>
		<th class="HeaderStyle"><?php echo $this->lang->line('Proveedor');?></th>
		<?php endif; ?>
		<th class="HeaderStyle"><?php echo $this->lang->line('ISBN');?></th>
		<th class="HeaderStyle"><?php echo $this->lang->line('Título');?></th>
		<th class="HeaderStyle"><?php echo $this->lang->line('Editorial');?></th>
		<?php if (!$sinalbaran):?>
		<th class="HeaderStyle"><?php echo $this->lang->line('Albarán');?></th>
		<th class="HeaderStyle"><?php echo $this->lang->line('Fecha');?></th>
		<?php endif; ?>
		<?php endif; ?>
	</tr>

	<?php $libros = $importe = $dto = 0.0;?>
	<?php foreach($valores as $venta):?>
	<?php
	$libros += $venta['nLibros'];
	if (!$sinalbaran)
	{
		$importe += $venta['fCoste'];
		$dto += $venta['nLibros'] * $venta['fDescuento'];
	}
	?>
	<tr class="Line2">
		<?php if (!$sinalbaran):?>
		<td align="left" class="Line1"><strong>
<?php if ($desglosado):?> <?php echo $venta['cSeccion'];?> <?php else:?>
		<?php $url = site_url('oltp/oltp/compras_periodo/1/'.urlencode(str_replace('/', '-', $fecha1)).'/'.urlencode(str_replace('/', '-', $fecha2)).'/' . $venta['nIdSeccion']. '/' .(isset($idproveedor)?$idproveedor:'null'));?>
		<a class="cmd-link"	href="javascript:parent.Ext.app.execCmd({timeout: false, url: '<?php echo $url; ?>'});">
			<?php echo $venta['cSeccion'];?></a></strong> <?php endif;?>			
		</td>
		<?php endif; ?>
		<td align="right" class="Line1"><?php echo format_number($venta['nLibros']);?></td>
		<?php if (!$sinalbaran):?>
		<td align="right" class="Line1"><?php echo format_price($venta['fCoste']);?></td>
		<td align="right" class="Line1"><?php echo format_percent($venta['fDescuento']);?></td>
		<?php endif; ?>
		<?php if ($desglosado):?>
		<?php if (!isset($idproveedor)):?>
		<td class="Line1"><span style="color: blue;"><?php echo format_enlace_cmd(format_name($venta['cEmpresa'], $venta['cNombre'], $venta['cApellido']), site_url('proveedores/proveedor/index/' . $venta['nIdProveedor']));?></span>			
		</td>
		<?php endif; ?>
		<td class="Line1"><?php echo $venta['cISBN'];?></td>
		<td class="Line1"><span style="color: green;"><?php echo format_enlace_cmd($venta['cTitulo'], site_url('catalogo/articulo/index/' . $venta['nIdLibro']));?></span></td>
		<td class="Line1"><?php echo $venta['cEditorial'];?></td>
				<?php if (!$sinalbaran):?>
		<td class="Line1">
			<?php echo format_enlace_cmd($venta['nIdAlbaran'], site_url('compras/albaranentrada/index/' . $venta['nIdAlbaran']));?>			
		</td>
		<td align="right" class="Line1"><?php echo format_date($venta['dCierre']);?></td>
		<?php endif; ?>
	</tr>
	<?php endif;?>
	<?php endforeach;?>
	<tr class="CategoryHeader">
		<?php if (!$sinalbaran):?>
		<td class="CategoryHeader2"></td>
		<?php endif; ?>
		<td align="right" class="CategoryHeader2"><?php echo format_number($libros);?></td>
		<?php if (!$sinalbaran):?>
		<td align="right" class="CategoryHeader2"><?php echo format_price($importe);?></td>
		<td align="right" class="CategoryHeader2"><?php echo format_percent($dto / $libros);?></td>
		<?php endif; ?>
		<?php if ($desglosado):?>
		<td class="CategoryHeader2" colspan="5"></td>
	<?php endif;?>
	</tr>
</table>
