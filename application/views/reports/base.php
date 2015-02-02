<?php $this->load->helper('asset');?>
<?php require(APPPATH . 'views' . DIRECTORY_SEPARATOR . 'reports' . DIRECTORY_SEPARATOR . 'params.php'); ?>
<?php if (isset($num_lineas_1_minus)) $num_lineas_1 -= $num_lineas_1_minus; ?>
<?php
$grupo = array();
$idportes = $this->config->item('bp.idportes');
foreach($lineas as $k => $linea)
{
	$id = $linea['nIdLibro'] . $linea['fPVP'] . $linea['fDescuento']. $linea['fPrecio'] . $linea[$ref_cliente] .(($linea['nCantidad'] > 0)?'+':'-');
	if (isset($linea['cExtra'])) $id .= $linea['cExtra'];
	if (isset($linea['cExtra2'])) $id .= $linea['cExtra2'];

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
	if ($linea['nIdLibro'] == $idportes)
	{
		$lineas[] = $linea;
		unset($lineas[$k]);
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
<?php if (!$excel):?>
<div id="page-wrap"><?php require(APPPATH . 'views' . DIRECTORY_SEPARATOR . 'reports' . DIRECTORY_SEPARATOR . 'header.php'); ?>
<?php endif; ?>
<?php $total = 0; $ivas = array(); $bases = array(); $totales = array(); $actual = 0;?> 
<?php $first = TRUE; ?>
<?php foreach($paginas as $pagina):?>
<?php if (($excel && $first)||!$excel):?>
<table class="items">
	<tr>
		<th class="items-th"><?php echo $this->lang->line('report-Cant');?></th>
		<!--  <th class="items-th"><?php echo $this->lang->line('report-Referencia');?></th>-->
		<?php if ($excel):?>
			<th class="items-th"><?php echo $this->lang->line('report-ISBN');?></th>
			<th class="items-th"><?php echo $this->lang->line('report-Editorial');?></th>
			<th class="items-th"><?php echo $this->lang->line('report-Autores');?></th>
		<?php endif; ?>
		<th class="items-th"><?php echo $this->lang->line('report-Título');?></th>
		<?php if ($precio):?>
		<?php if ($show_pvp):?>
		<th class="items-th"><?php echo $this->lang->line('report-Precio');?></th>
		<?php endif;?>
		<?php if ($show_dto):?>
		<th class="items-th"><?php echo $this->lang->line('report-Desc.');?></th>
		<?php endif;?>
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
<?php $first = FALSE;?>
<?php endif; ?>
	<?php foreach($pagina as $linea):?>
	<tr class="item-row">
		<td class="item-ct"><?php echo $linea['nCantidad'];?></td>
	<?php if ($excel):?>
			<td class="items-name"><?php echo (isset($linea['cISBN'])&&trim($linea['cISBN'])!='')?trim($linea['cISBN']):'';?></td>
			<td class="items-name"><?php echo (isset($linea['cEditorial'])&&trim($linea['cEditorial'])!='')?$linea['cEditorial'] . ' - ':'';?></td>
			<td class="items-name"><?php echo (isset($linea['cAutores'])&&trim($linea['cAutores'])!='')?format_title($linea['cAutores'], $autorlen):'';?></td>
		<?php endif; ?>
			<td class="item-name"><?php echo format_title($linea['cTitulo'], $titlelen);?>
		<?php if (!$excel):?>
			<br />
			<?php echo (isset($linea['cExtra'])&&trim($linea['cExtra'])!='')?($linea['cExtra']. '<br/>'):'';?>
			<?php echo (isset($linea[$ref_cliente])&& (trim($linea[$ref_cliente])!=''))?'<strong>' . $this->lang->line('report-Ref.'). '</strong>: ' . format_title($linea[$ref_cliente], $reflen):'';?>
			<?php echo (isset($linea['cISBN'])&&trim($linea['cISBN'])!='')?'[' . trim($linea['cISBN']) . ']':'';?>
			<?php echo (isset($linea['cEditorial'])&&trim($linea['cEditorial'])!='')?$linea['cEditorial'] . ' - ':'';?>
			<?php echo (isset($linea['cAutores'])&&trim($linea['cAutores'])!='')?format_title($linea['cAutores'], $autorlen):'';?>
			<?php echo (isset($linea['cExtra2'])&&trim($linea['cExtra2'])!='')?('<br/>' . $linea['cExtra2']):'';?>
		<?php endif; ?>
		</td>
		<?php if ($precio):?>
		<?php if ($show_pvp):?>
		<td class="item-pvp"><?php echo format_price($base_mode?$linea['fPrecio']:$linea['fPVP'], FALSE);?></td>
		<?php endif; ?>
		<?php if ($show_dto):?>
		<td class="item-dto"><?php echo ($linea['fDescuento']>0)?format_number($linea['fDescuento']):'&nbsp;';?></td>
		<?php endif; ?>
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
<?php if (!$excel):?>
</table>
<?php endif; ?>
<?php $actual++;?> 
<?php if ($actual != (count($paginas))):?>
	<?php if (!$excel):?>
	<div class="page-break"></div>
	<?php endif; ?>
<?php endif;?> 
<?php endforeach;?>
<?php if (!$excel):?>
<div style="clear: both"></div>
<?php if (isset($extra_page)):?> <?php echo $extra_page;?>
<div style="clear: both"></div>
<?php endif; ?>
<?php require(APPPATH . 'views' . DIRECTORY_SEPARATOR . 'reports' . DIRECTORY_SEPARATOR . 'footer.php'); ?>
</div>
<?php endif; ?>
<?php if ($excel):?>
</table>
<?php endif; ?>
