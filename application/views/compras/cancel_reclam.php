<?php $this->load->helper('asset');?>
<?php

$borrador = FALSE;
$cliente = $proveedor;
$nIdCliente = $nIdProveedor;
$cRefCliente = null;
$clpv = $this->lang->line('report-Proveedor');
$logo = FALSE;

//Une las líneas iguales
$esta = array();
?>
<?php require(APPPATH . 'views' . DIRECTORY_SEPARATOR . 'reports' . DIRECTORY_SEPARATOR . 'params.php'); ?>
<?php
$count = 0;
$actual = 0;
$paginas = array();
$pagina = 0;
$titulos = count($lineas);
$ejemplares = 0;
foreach($lineas as $linea)
{
	$paginas[$pagina][] = $linea;
	$ejemplares += $linea['nCantidad'];
	$actual++;
}
?>

<?php $total = 0;?>
<?php $ivas = array();?>
<?php $bases = array();?>
<?php $totales = array();?>
<?php $actual = 0;?>
<div id="page-wrap"><?php require(APPPATH . 'views' . DIRECTORY_SEPARATOR . 'reports' . DIRECTORY_SEPARATOR . 'header.php'); ?>
<?php foreach($paginas as $pagina):?>
<table class="items">
	<tr>
		<th class="items-th"><?php echo $this->lang->line('report-Cant');?></th>
		<th class="items-th"><?php echo $this->lang->line('report-Título');?></th>
		<th class="items-th"><?php echo $this->lang->line('report-Precio');?></th>
		<th class="items-th"><?php echo $this->lang->line('report-Desc.');?></th>
		<th class="items-th"><?php echo $this->lang->line('report-Base');?></th>
		<th class="items-th"><?php echo $this->lang->line('report-P/U');?></th>
		<th class="items-th"><?php echo $this->lang->line('report-Tipo IVA');?></th>
		<th class="items-th"><?php echo $this->lang->line('report-IVA');?></th>
		<th class="items-th"><?php echo $this->lang->line('report-Total');?></th>
	</tr>
	<?php foreach($pagina as $linea):?>
	<tr class="item-row">
		<td class="item-ct"><?php echo $linea['nCantidad'];?></td>
		<td class="item-name"><?php echo format_title($linea['cTitulo'], $titlelen);?><br />
		<?php echo (isset($linea[$ref_cliente])&& (trim($linea[$ref_cliente])!=''))?$this->lang->line('report-Ref.'). ': ' . format_title($linea[$ref_cliente], $reflen):'';?>
		<?php echo (isset($linea['cISBN'])&&trim($linea['cISBN'])!='')?'[' . $linea['cISBN'] . ']':'';?>
		<?php echo (isset($linea['cEditorial'])&&trim($linea['cEditorial'])!='')?$linea['cEditorial']:'';?><br />
		<?php if (isset($linea['nIdPedido'])&&$linea['nIdPedido']!=''):?> <?php echo $this->lang->line('report-Pedido Original')?>:
		<?php echo $linea['nIdPedido'];?> <?php echo (isset($linea['dFechaEntrega']))?'[' . format_date($linea['dFechaEntrega']) .']':'';?>
		<?php endif;?></td>
		<td class="item-pvp"><?php echo format_price($base_mode?$linea['fPrecio']:$linea['fPVP'], FALSE);?></td>
		<td class="item-dto"><?php echo ($linea['fDescuento']>0)?format_number($linea['fDescuento']):'&nbsp;';?></td>
		<td class="item-base"><?php echo format_price($base_mode?$linea['fImporte2']:$linea['fImporte'], FALSE);?></td>
		<td class="item-base"><?php echo format_price($base_mode?$linea['fBase2']:$linea['fBase'], FALSE);?></td>
		<td class="item-iva"><?php echo format_number($linea['fIVA']);?></td>
		<td class="item-base"><?php echo format_number($base_mode?$linea['fIVAImporte2']:$linea['fIVAImporte']);?></td>
		<td class="item-base"><?php echo format_price($base_mode?$linea['fTotal2']:$linea['fTotal'], FALSE);?></td>
	</tr>
	<?php
	if ($linea['nCantidad'] != 0)
	{
		$total += $base_mode?$linea['fTotal2']:$linea['fTotal'];
		$ivas[$linea['fIVA']] = (isset($ivas[$linea['fIVA']])?$ivas[$linea['fIVA']]:0) + ($base_mode?$linea['fIVAImporte2']:$linea['fIVAImporte']);
		$bases[$linea['fIVA']] = (isset($bases[$linea['fIVA']])?$bases[$linea['fIVA']]:0) + ($base_mode?$linea['fBase2']:$linea['fBase']);
		$totales[$linea['fIVA']] = (isset($totales[$linea['fIVA']])?$totales[$linea['fIVA']]:0) + ($base_mode?$linea['fTotal2']:$linea['fTotal']);
	}
	?>
	<?php endforeach;?>
</table>
<?php $actual++;?> <?php endforeach;?>
<div style="clear: both"></div>
<div id="footer"><?php
$iva = 0;
$base = 0;
$total = 0;
foreach($ivas as $k => $v)
{
	$i = format_iva((($pvp)?format_quitar_iva($totales[$k], $k):$bases[$k]), $k);
	$b = ($pvp)?format_quitar_iva($totales[$k], $k):$bases[$k];
	$iva += $i;
	$base += $b;
	$total += $i + $b;
}
?>
<table id="totals">
	<tr>
		<td class="total-line"><?php echo $this->lang->line('report-Ejemplares');?></td>
		<td class="total-value"><?php echo format_number($ejemplares);?></td>
	</tr>
	<tr>
		<td class="total-line"><?php echo $this->lang->line('report-Títulos');?></td>
		<td class="total-value"><?php echo format_number($titulos);?></td>
	</tr>
	<?php if ($precio):?>

	<tr>
		<td class="total-line"><?php echo $this->lang->line('report-Base');?></td>
		<td class="total-value"><?php echo format_price($base);?></td>
	</tr>
	<tr>
		<td class="total-line"><?php echo $this->lang->line('report-IVA');?></td>
		<td class="total-value"><?php echo format_price($iva);?></td>
	</tr>
	<tr>
		<td class="total-line"><?php echo $this->lang->line('report-Total');?></td>
		<td class="total-value"><?php echo format_price($total);?></td>
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
		<td class="taxes-value"><?php echo format_price((($pvp)?format_quitar_iva($totales[$k], $k):$bases[$k]), FALSE);?></td>
		<td class="taxes-value"><?php echo format_price(format_iva((($pvp)?format_quitar_iva($totales[$k], $k):$bases[$k]), $k), FALSE);?></td>
	</tr>
	<?php $iva += $v;?>
	<?php $base += $bases[$k];?>
	<?php endforeach;?>
</table>
	<?php endif; ?>
<div style="clear: both"></div>
<?php if (isset($extra_impuestos) && ($show_extra_impuestos)):?> <?php echo $extra_impuestos;?>
<?php endif; ?>
<div style="clear: both"></div>
</div>
</div>

