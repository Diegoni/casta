<h2><?php echo $fecha1; ?> &lt;-&gt; <?php echo $fecha2; ?>
<?php if (isset($idseccion) && $idseccion!=''):?>
<br/><?php echo $this->lang->line('Sección'); ?>: <?php echo $idseccion; ?>
<?php endif;?>
<?php if (isset($idcliente) && $idcliente!=''):?>
<br /><?php echo $this->lang->line('Cliente'); ?>: <?php echo $idcliente; ?>
<?php endif;?>
</h2>
<table cellspacing="0" cellpadding="3" class="SummaryDataGrid"
	style="BORDER-TOP-WIDTH: 0px; BORDER-LEFT-WIDTH: 0px; BORDER-BOTTOM-WIDTH: 0px; BORDER-COLLAPSE: collapse; BORDER-RIGHT-WIDTH: 0px">
	<tr class="HeaderStyle">
		<th class="HeaderStyle"><?php echo $this->lang->line('Area');?></th>
		<th class="HeaderStyle"><?php echo $this->lang->line('Serie');?></th>
		<th class="HeaderStyle"><?php echo $this->lang->line('Libros');?></th>
		<th class="HeaderStyle"><?php echo $this->lang->line('Importe');?></th>
		<th class="HeaderStyle"><?php echo $this->lang->line('Coste');?></th>
		<th class="HeaderStyle"><?php echo $this->lang->line('Margen');?></th>
		<?php if ($desglosado):?>
		<th class="HeaderStyle"><?php echo $this->lang->line('Cliente');?></th>
		<th class="HeaderStyle"><?php echo $this->lang->line('Título');?></th>
		<th class="HeaderStyle"><?php echo $this->lang->line('Factura');?></th>
		<th class="HeaderStyle"><?php echo $this->lang->line('Fecha');?></th>
		<th class="HeaderStyle"><?php echo $this->lang->line('Descuento');?></th>
		<?php endif; ?>
	</tr>

	<?php $libros4 = 0;$importe4 = 0;$coste4 = 0; ?>
	<?php foreach($valores as $area => $series):?>
	<tr class="CategoryHeaderHier">
		<td class="CategoryHeaderHier" colspan="12"><?php echo $area;?></td>
	</tr>
	<?php $libros3 = 0;$importe3 = 0;$coste3 = 0; ?>
	<?php foreach($series as $serie => $secciones):?>
	<tr class="CategoryHeader">
		<td class="CategoryHeader"></td>
		<td class="CategoryHeader" colspan="<?php echo ($desglosado)?12:6;?>"><?php echo $serie;?></td>
	</tr>

	<?php $libros2 = 0; $importe2 = 0; $coste2 = 0;?>
	<?php foreach($secciones as $seccion => $ventas):?>
	<?php $libros = 0; $importe = 0; $coste = 0; ?>
	<?php if ($desglosado):?>
	<tr class="Line2">
		<td class="Line2"></td>
		<td class="Line2" colspan="12"><?php echo $seccion;?></td>
	</tr>
	<?php endif;?>
	<?php foreach($ventas as $venta):?>
	<?php
	$libros += $venta['nLibros'];
	$importe += $venta['fImporte'];
	$coste += $venta['fCoste'];
	?>
	<?php if ($desglosado):?>
	<tr class="Line2">
		<td colspan="2" class="Line1"></td>
		<td align="right" class="Line1"><?php echo format_number($venta['nLibros']);?></td>
		<td align="right" class="Line1"><?php echo format_price($venta['fImporte']);?></td>
		<td align="right" class="Line1"><?php echo format_price($venta['fCoste']);?></td>
		<td align="right" class="Line1"><?php echo format_percent((1 - ($venta['fCoste'] / $venta['fImporte'])) * 100);?></td>
		<td class="Line1">
<?php echo format_enlace_cmd(format_name($venta['cEmpresa'], $venta['cNombre'], $venta['cApellido']), site_url('clientes/cliente/index/' . $venta['nIdCliente']));?>			
		</td>
		<td class="Line1"><?php echo format_enlace_cmd($venta['cTitulo'], site_url('catalogo/articulo/index/' . $venta['nIdLibro']));?></td>
		<td class="Line1">
<?php echo format_enlace_cmd($venta['nNumero']. '-' . $venta['nNumeroSerie'], site_url('ventas/factura/index/' . $venta['nIdFactura']));?>			
			</td>
		<td align="right" class="Line1"><?php echo format_date($venta['dFecha']);?></td>
		<td align="right" class="Line1"><?php echo format_percent($venta['fDescuento']);?></td>
	</tr>
	<?php endif;?>
	<?php endforeach;?>
	<tr class="Line2">
		<td class="Line2"></td>
		<td class="Line2"><?php if ($desglosado):?> <?php echo $seccion;?> <?php else:?>
		<?php $url = site_url('oltp/oltp/ventas_periodo/1/'.urlencode(str_replace('/', '-', $fecha1)).'/'.urlencode(str_replace('/', '-', $fecha2)).'/' . $ventas[0]['nIdSerie']. '/' .$ventas[0]['nIdSeccion'].'/' . $ventas[0]['nIdArea'] . '/' .(isset($idcliente)?$idcliente:'null'));?>
		<a
			href="javascript:parent.Ext.app.execCmd({timeout: false, url: '<?php echo $url; ?>'});">
			<?php echo $seccion;?></a> <?php endif;?></td>
		<td align="right" class="Line2"><?php echo format_number($libros);?></td>
		<td align="right" class="Line2"><?php echo format_price($importe);?></td>
		<td align="right" class="Line2"><?php echo format_price($coste);?></td>
		<td align="right" class="Line2"><?php echo format_percent(($importe != 0)?(1 - ($coste / $importe)) * 100:0);?></td>
		<?php if ($desglosado):?>
		<td align="right" class="Line2" colspan="6"></td>
		<?php endif;?>
	</tr>
	<?php
	$libros2 += $libros;
	$importe2 += $importe;
	$coste2 += $coste;
	?>
	<?php endforeach;?>
	<?php
	$libros3 += $libros2;
	$importe3 += $importe2;
	$coste3 += $coste2;
	?>
	<?php endforeach;?>
	<?php
	$libros4 += $libros3;
	$importe4 += $importe3;
	$coste4 += $coste3;
	?>
	<tr class="CategoryHeader">
		<td class="CategoryHeader2"></td>
		<td class="CategoryHeader2"><?php echo $serie;?></td>
		<td align="right" class="CategoryHeader2"><?php echo format_number($libros3);?></td>
		<td align="right" class="CategoryHeader2"><?php echo format_price($importe3);?></td>
		<td align="right" class="CategoryHeader2"><?php echo format_price($coste3);?></td>
		<td align="right" class="CategoryHeader2"><?php echo format_percent(($importe3 != 0)?(1 - ($coste3 / $importe3)) * 100:0);?></td>
		<td class="CategoryHeader2" colspan="<?php echo ($desglosado)?12:6;?>"></td>
	</tr>
	<?php endforeach;?>
	<tr class="CategoryHeader">
		<td class="FooterStyle" colspan="2"><?php echo $area;?></td>
		<td align="right" class="FooterStyle"><?php echo format_number($libros4);?></td>
		<td align="right" class="FooterStyle"><?php echo format_price($importe4);?></td>
		<td align="right" class="FooterStyle"><?php echo format_price($coste4);?></td>
		<td align="right" class="FooterStyle"><?php echo format_percent(($importe4)?(1 - ($coste4 / $importe4)) * 100:0);?></td>
		<td align="right" colspan="<?php echo ($desglosado)?11:5;?>" class="FooterStyle"></td>
	</tr>
</table>
