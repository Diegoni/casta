<?php $this->load->helper('asset');?>
<?php
$titulo = $this->lang->line('report-liquidacion-deposito');
$borrador = (!isset($dFecha));
#$nIdDocumento = $nIdAlbaran;
$cliente = $proveedor;
$nIdCliente = $nIdProveedor;
$cRefCliente = $cRefProveedor;
$ref_cliente = 'cRefProveedor';
$dCreacion = $dFecha;
$texto_condiciones = $this->lang->line('text-liquidaciondeposito');
$texto_email = $this->lang->line('text-liquidaciondeposito-email');
$clpv = $this->lang->line('report-Proveedor');
//Une las líneas iguales
$esta = array();
?>
<?php require(APPPATH . 'views' . DIRECTORY_SEPARATOR . 'reports' . DIRECTORY_SEPARATOR . 'params.php'); ?>
<?php

$ci = get_instance();
$ci->load->model('generico/m_divisa');
$divisa_default = $this->config->item('bp.divisa.default');
if (!isset($nIdDivisa)) $nIdDivisa = $divisa_default;
$div = $ci->m_divisa->load($nIdDivisa);
$divisa = $div['cSimbolo'] . ' - ' . $div['cDescripcion'];

$count = 0;
$actual = 0;
$paginas = array();
$pagina = 0;
$titulos = count($lineas);
$ejemplares = 0;
foreach($lineas as $linea)
{
	$linea['fPrecioLocal'] = $linea['fPrecio'];
	$linea['fImporteLocal'] = $linea['fImporte2'];
	$linea['fIVAImporteLocal'] = $linea['fIVAImporte2'];
	$linea['fBaseLocal'] = $linea['fBase2'];
	$linea['fTotalLocal'] = $linea['fTotal2'];
	$totales = format_calculate_importes($linea);
	$linea = array_merge($linea, $totales);	 
	$paginas[$pagina][] = $linea;
	$ejemplares += $linea['nCantidad'];
	$actual++;
	if ((($pagina == 0) && ($actual == $num_lineas_1))||(($pagina > 0) && ($actual == $num_lineas_2)))
	{
		$pagina++;
		$actual = 0;
	}
}

?>


<div id="page-wrap"><?php require(APPPATH . 'views' . DIRECTORY_SEPARATOR . 'reports' . DIRECTORY_SEPARATOR . 'header.php'); ?>
<?php $total = 0; $ivas = array(); $bases = array(); $totales = array(); $actual = 0;?> 
<?php $total2 = 0; $ivas2 = array(); $bases2 = array(); $totales2 = array();?> 
<?php foreach($paginas as $pagina):?>
<table class="items">
	<tr>
		<th class="items-th"><?php echo $this->lang->line('report-Cant');?></th>
		<!--  <th class="items-th"><?php echo $this->lang->line('report-Referencia');?></th>-->
		<th class="items-th"><?php echo $this->lang->line('report-Título');?></th>
		<?php if ($precio):?>
		<th class="items-th"><?php echo $this->lang->line('report-Precio');?></th>
		<th class="items-th"><?php echo $this->lang->line('report-Desc.');?></th>
		<th class="items-th"><?php echo $this->lang->line('report-Base');?></th>
		<?php if ($show_unitario):?>
		<th class="items-th"><?php echo $this->lang->line('report-P/U');?></th>
		<?php endif;?>
		<th class="items-th"><?php echo $this->lang->line('report-Tipo IVA');?></th>
		<?php if ($show_iva):?>
		<th class="items-th"><?php echo $this->lang->line('report-IVA');?></th>
		<?php endif;?>
		<?php if ($show_total):?>
		<th class="items-th"><?php echo $this->lang->line('report-Total');?></th>
		<?php endif;?>
		<?php endif;?>
	</tr>
	<?php foreach($pagina as $linea):?>
	<tr class="item-row">
		<td class="item-ct"><?php echo $linea['nCantidad'];?></td>
		<td class="item-name"><?php echo format_title($linea['cTitulo'], $titlelen);?><br />
		<?php echo (isset($linea[$ref_cliente])&& (trim($linea[$ref_cliente])!=''))?$this->lang->line('report-Ref.'). ': ' . format_title($linea[$ref_cliente], $reflen):'';?>
		<?php echo (isset($linea['cISBN'])&&trim($linea['cISBN'])!='')?'[' . $linea['cISBN'] . ']':'';?>
		<?php echo (isset($linea['cEditorial'])&&trim($linea['cEditorial'])!='')?$linea['cEditorial']:'';?><br />
		<?php if (isset($linea['cNumeroAlbaran'])&&$linea['cNumeroAlbaran']!=''):?>
		<?php echo $this->lang->line('report-Alb./Fac. Original')?>: <?php echo $linea['cNumeroAlbaran'];?>
		<?php echo (isset($linea['dFechaAlbaran']))?'[' . format_date($linea['dFechaAlbaran']) .']':'';?>
		<?php endif;?>		
		<?php if ($precio):?>
		<td class="item-pvp"><?php echo format_price($linea['fPrecio'], FALSE);?>
			<?php if ($nIdDivisa != $divisa_default):?>
				<br/><span class="small"><?php echo format_price($linea['fPrecioLocal']);?></span>
			<?php endif;?>
		</td>
		<td class="item-dto"><?php echo ($linea['fDescuento']>0)?format_number($linea['fDescuento']):'&nbsp;';?></td>
		<?php if ($show_unitario):?>
		<td class="item-base"><?php echo format_price($linea['fImporte2'], FALSE);?>
			<?php if ($nIdDivisa != $divisa_default):?>
				<br/><span class="small"><?php echo format_price($linea['fImporteLocal']);?></span>
			<?php endif;?>			
		</td>
		<?php endif;?>
		<td class="item-base"><?php echo format_price($linea['fBase2'], FALSE);?>
			<?php if ($nIdDivisa != $divisa_default):?>
				<br/><span class="small"><?php echo format_price($linea['fBaseLocal']);?></span>
			<?php endif;?>
		</td>
		<td class="item-iva"><?php echo format_number($linea['fIVA']);?></td>
		<?php if ($show_iva):?>
		<td class="item-base"><?php echo format_number($linea['fIVAImporte2']);?>
			<?php if ($nIdDivisa != $divisa_default):?>
				<br/><span class="small"><?php echo format_price($linea['fIVAImporteLocal']);?></span>
			<?php endif;?>		
		</td>
		<?php endif;?>
		<?php if ($show_total):?>
		<td class="item-base"><?php echo format_price($linea['fTotal2'], FALSE);?>
			<?php if ($nIdDivisa != $divisa_default):?>
				<br/><span class="small"><?php echo format_price($linea['fTotalLocal']);?></span>
			<?php endif;?>			
		</td>
		<?php endif;?>
		<?php endif; ?>
	</tr>
	<?php
	if ($linea['nCantidad'] != 0)
	{
		$total += $linea['fTotal2'];
		$ivas[$linea['fIVA']] = (isset($ivas[$linea['fIVA']])?$ivas[$linea['fIVA']]:0) + ($linea['fIVAImporte2']);
		$bases[$linea['fIVA']] = (isset($bases[$linea['fIVA']])?$bases[$linea['fIVA']]:0) + ($linea['fBase2']);
		$totales[$linea['fIVA']] = (isset($totales[$linea['fIVA']])?$totales[$linea['fIVA']]:0) + ($linea['fTotal2']);

		$total2 += $linea['fTotalLocal'];
		$ivas2[$linea['fIVA']] = (isset($ivas2[$linea['fIVA']])?$ivas2[$linea['fIVA']]:0) + ($linea['fIVAImporteLocal']);
		$bases2[$linea['fIVA']] = (isset($bases2[$linea['fIVA']])?$bases2[$linea['fIVA']]:0) + ($linea['fBaseLocal']);
		$totales2[$linea['fIVA']] = (isset($totales2[$linea['fIVA']])?$totales2[$linea['fIVA']]:0) + ($linea['fTotalLocal']);

	}
	?>
	<?php endforeach;?>
</table>
<?php $actual++;?> <?php if ($actual != (count($paginas))):?>
<div class="page-break"></div>
<?php endif;?> <?php endforeach;?>
<div style="clear: both"></div>
<?php if (isset($extra_page)):?> <?php echo $extra_page;?>
	<br/>
<div style="clear: both"></div>
<?php endif; ?>
<div id="footer"><?php if ($totales):?> <?php
$iva = $base = $total = 0;
$iva2 = $base2 = $total2 = 0;
foreach($ivas as $k => $v)
{
	$i = format_iva($bases[$k], $k);
	$b = $bases[$k];
	$iva += $i;
	$base += $b;
	$total += $i + $b;

	$i = format_iva($bases2[$k], $k);
	$b = $bases2[$k];
	$iva2 += $i;
	$base2 += $b;
	$total2 += $i + $b;
}
?>
<table id="totals">
<?php if ($show_ejemplares):?>
	<tr>
		<td class="total-line"><?php echo $this->lang->line('report-Ejemplares');?></td>
		<td class="total-value"><?php echo format_number($ejemplares);?></td>
	</tr>
	<?php endif; ?>
	<?php if ($show_titulos):?>
	<tr>
		<td class="total-line"><?php echo $this->lang->line('report-Títulos');?></td>
		<td class="total-value"><?php echo format_number($titulos);?></td>
	</tr>
	<?php endif; ?>

	<?php if ($precio):?>
	<tr>
		<td class="total-line"><?php echo $this->lang->line('report-Base');?></td>
		<td class="total-value"><?php echo format_price($base, ($nIdDivisa == $divisa_default));?>
			<?php if ($nIdDivisa != $divisa_default):?>
				<span class="small"><?php echo format_price($base2);?></span>
			<?php endif;?>					
		</td>
	</tr>
	<tr>
		<td class="total-line"><?php echo $this->lang->line('report-IVA');?></td>
		<td class="total-value"><?php echo format_price($iva, ($nIdDivisa == $divisa_default));?>
			<?php if ($nIdDivisa != $divisa_default):?>
				<span class="small"><?php echo format_price($iva2);?></span>
			<?php endif;?>					
			
		</td>
	</tr>
	<tr>
		<td class="total-line"><?php echo $this->lang->line('report-Total');?></td>
		<td class="total-value"><?php echo format_price($total, ($nIdDivisa == $divisa_default));?>
			<?php if ($nIdDivisa != $divisa_default):?>
				<span class="small"><?php echo format_price($total2);?></span>
			<?php endif;?>								
		</td>
	</tr>
	<?php endif; ?>
</table>
	<?php if ($precio):?>

<table id="taxes">
	<tr>
		<td class="taxes-head"><?php echo $this->lang->line('report-IVA');?></td>
		<td class="taxes-head"><?php echo $this->lang->line('report-Base');?></td>
		<td class="taxes-head"><?php echo $this->lang->line('report-Importe');?></td>
	</tr>
	<?php $iva = 0;?>
	<?php $base = 0;?>
	<?php foreach($ivas as $k => $v):?>
	<tr>
		<td class="taxes-value"><?php echo format_number($k);?></td>
		<td class="taxes-value"><?php echo format_price($bases[$k], FALSE);?>
			<?php if ($nIdDivisa != $divisa_default):?>
				<span class="small"><?php echo format_price($bases2[$k]);?></span>
			<?php endif;?>					
		</td>
		<td class="taxes-value"><?php echo format_price(format_iva($bases[$k], $k), FALSE);?>
			<?php if ($nIdDivisa != $divisa_default):?>
				<span class="small"><?php echo format_price(format_iva($bases2[$k], $k));?></span>
			<?php endif;?>								
		</td>
	</tr>
	<?php $iva += $v;?>
	<?php $base += $bases[$k];?>
	<?php endforeach;?>
</table>
	<?php endif; ?>
<div style="clear: both"></div>
<?php if (isset($extra_impuestos) && ($show_extra_impuestos)):?> <?php echo $extra_impuestos;?>
<div style="clear: both"></div>
<?php endif; ?>
<?php endif; ?> 
<?php require(APPPATH . 'views' . DIRECTORY_SEPARATOR . 'reports' . DIRECTORY_SEPARATOR . 'terms.php'); ?>

</div>

</div>
