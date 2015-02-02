<?php $this->load->helper('asset');?>
<?php
$titulo = ($bDeposito)?$this->lang->line('report-Devolución Depósito'):$this->lang->line('report-Devolución');
$borrador = ($nIdEstado == 1);
$nIdDocumento = $nIdDevolucion;
$cliente = $proveedor;
$nIdCliente = $nIdProveedor;
if (isset($cTipoDevolucion)) $cRefProveedor .= ' ' . $cTipoDevolucion;
$cRefCliente = $cRefProveedor;
$ref_cliente = 'cRefProveedor';
$dCreacion = $dCierre;
$texto_condiciones = $this->lang->line('text-devolucion');
$texto_email = $this->lang->line('text-devolucion-email');
$clpv = $this->lang->line('report-Proveedor');
//Une las líneas iguales
$esta = array();
?>
<?php require(APPPATH . 'views' . DIRECTORY_SEPARATOR . 'reports' . DIRECTORY_SEPARATOR . 'params.php'); ?>
<?php
$grup = array();
foreach($lineas as $k => $linea)
{
	$id = $linea['nIdDevolucionRechazada'] .$linea['nIdLibro'] . $linea['fPVP'] . $linea['fDescuento']. $linea['fPrecio'] . $linea['nIdAlbaran'] .(($linea['nCantidad'] > 0)?'+':'-');

	#echo $id .'<br/>';
	if (isset($grup[$id]))
	{
		$k2 = $grup[$id];
		$lineas[$k2]['nCantidad'] += $linea['nCantidad'];
		$lineas[$k2]['fBase'] += $linea['fBase'];
		$lineas[$k2]['fIVAImporte'] += $linea['fIVAImporte'];
		$lineas[$k2]['fTotal'] += $linea['fTotal'];
		$lineas[$k2]['fTotal2'] += $linea['fTotal2'];
		$lineas[$k2]['fBase2'] += $linea['fBase2'];
		$lineas[$k2]['fIVAImporte2'] += $linea['fIVAImporte2'];
		unset($lineas[$k]);
	}
	else
	{
		$grup[$id] = $k;
	}
}

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
	if ((($pagina == 0) && ($actual == $num_lineas_1))||(($pagina > 0) && ($actual == $num_lineas_2)))
	{
		$pagina++;
		$actual = 0;
	}
}
?>

<div id="page-wrap"><?php require(APPPATH . 'views' . DIRECTORY_SEPARATOR . 'reports' . DIRECTORY_SEPARATOR . 'header.php'); ?>
<?php $total = 0;?> <?php $ivas = array();?> <?php $bases = array();?> <?php $totales = array();?>
<?php $actual = 0;?> <?php foreach($paginas as $pagina):?>
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
		<?php if (isset($linea['nIdDevolucionRechazada'])):?>
		<?php echo $this->lang->line('report-Dev. Rechazada')?>: <?php echo $linea['nIdDevolucionRechazada'];?>
		<?php endif;?>		
		<?php if (isset($linea['cAlbaranProveedor'])&&$linea['cAlbaranProveedor']!=''):?>
		<?php echo $this->lang->line('report-Alb./Fac. Original')?>: <?php echo $linea['cAlbaranProveedor'];?>
		<?php echo (isset($linea['dFecha']))?'[' . format_date($linea['dFecha']) .']':'';?>
		<?php endif;?></td>
		<?php if ($precio):?>
		<td class="item-pvp"><?php echo format_price($base_mode?$linea['fPrecio']:$linea['fPVP'], FALSE);?></td>
		<td class="item-dto"><?php echo ($linea['fDescuento']>0)?format_number($linea['fDescuento']):'&nbsp;';?></td>
		<?php if ($show_unitario):?>
		<td class="item-base"><?php echo format_price($base_mode?$linea['fImporte2']:$linea['fImporte'], FALSE);?></td>
		<?php endif;?>
		<td class="item-base"><?php echo format_price($base_mode?$linea['fBase2']:$linea['fBase'], FALSE);?></td>
		<td class="item-iva"><?php echo format_number($linea['fIVA']);?></td>
		<?php if ($show_iva):?>
		<td class="item-base"><?php echo format_number($base_mode?$linea['fIVAImporte2']:$linea['fIVAImporte']);?></td>
		<?php endif;?>
		<?php if ($show_total):?>
		<td class="item-base"><?php echo format_price($base_mode?$linea['fTotal2']:$linea['fTotal'], FALSE);?></td>
		<?php endif;?>
		<?php endif; ?>
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
<?php $actual++;?> <?php if ($actual != (count($paginas))):?>
<div class="page-break"></div>
<?php endif;?> <?php endforeach;?>
<div style="clear: both"></div>
<?php require(APPPATH . 'views' . DIRECTORY_SEPARATOR . 'reports' . DIRECTORY_SEPARATOR . 'footer.php'); ?>
</div>
