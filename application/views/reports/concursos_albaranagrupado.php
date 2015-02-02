<?php $num_lineas_1 = isset($num_lineas_1)?$num_lineas_1:16;?>
<?php $num_lineas_2 = isset($num_lineas_2)?$num_lineas_2:24;?>
<?php $titlelen = isset($titlelen)?$titlelen:100;?>
<?php $autorlen = isset($autorlen)?$autorlen:80;?>
<?php $refelen = isset($refelen)?$refelen:30;?>
<?php $clientelen = isset($clientelen)?$clientelen:40;?>
<?php
$obj = get_instance();
$obj->load->helper('asset');
$obj->load->model('concursos/m_configuracion');
$data = $obj->m_configuracion->get();
$configuracion = $data[0];
$obj->load->model('concursos/m_albaran');
$obj->load->model('concursos/m_albaranagrupado');

$count = 0;
$actual = 0;
$paginas = array();
$pagina = 0;
$lineas = $obj->m_albaranagrupado->lineas($nIdAlbaranAgrupado);
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

<div id="page-wrap">
<div id="header"><?php echo $this->lang->line('report-Albarán de Salida');?></div>
<div id="identity">
<div id="logo"><img
	src="<?php echo image_asset_url($this->config->item('company.logo.print'));?>" /></div>

<div id="address"><?php echo $this->config->item('company.name');?><br />
<?php echo ($this->config->item('company.address.1')!='')?$this->config->item('company.address.1') . '<br/>':'';?>
<?php echo ($this->config->item('company.address.2')!='')?$this->config->item('company.address.2') . '<br/>':'';?>
<?php echo ($this->config->item('company.address.3')!='')?$this->config->item('company.address.3') . '<br/>':'';?>
<?php echo $this->lang->line('report-NIF');?>: <?php echo $this->config->item('company.vat');?>
</div>
</div>
<div style="clear: both"></div>
<div id="customer">
<div id="customer-title"><?php echo wordwrap($biblioteca['cBiblioteca'], $clientelen, '<br/>');?><br />
</div>
<table id="meta">
	<tr>
		<td class="meta-head"><?php echo $this->lang->line('report-Número');?></td>
		<td><?php echo $nIdAlbaranAgrupado;?></td>
	</tr>
	<tr>
		<td class="meta-head"><?php echo $this->lang->line('report-Fecha');?></td>
		<td>
		<div id="date"><?php echo format_date($dCreacion);?></div>
		</td>
	</tr>
</table>
</div>
<?php 
$total = 0;
$ivas = array();
$bases = array();
$importes_pvp = array();
$actual = 0;
?>
<?php foreach($paginas as $pagina):?>
<table class="items">
	<tr>
		<th class="items-th"><?php echo $this->lang->line('report-Cant');?></th>
		<th class="items-th"><?php echo $this->lang->line('report-Título');?></th>
		<th class="items-th"><?php echo $this->lang->line('report-Precio c/i');?></th>
		<th class="items-th"><?php echo $this->lang->line('report-Desc.');?></th>
<!-- 		<th class="items-th"><?php echo $this->lang->line('report-Base');?></th>-->
		<th class="items-th"><?php echo $this->lang->line('report-Tipo IVA');?></th>
	</tr>
	<?php foreach($pagina as $linea):?>
	<?php $pvp = $linea['fPrecio'];?>
	<?php $iva = 4;?>
	<?php $importes = format_calculate_importes(array(
		'fPrecio' 		=> format_quitar_iva($pvp, $iva),
		'nCantidad' 	=> $linea['nCantidad'], 
		'fRecargo'		=> 0, 
		'fIVA' 			=> $iva, 
		'fDescuento' 	=> $configuracion['fDescuento']
	));
	?>
	<tr class="item-row">
		<td class="item-ct"><?php echo $linea['nCantidad'];?></td>
		<td class="item-name"><?php echo format_title($linea['cTitulo'], $titlelen);?><br />
		<?php echo (isset($linea['cISBN'])&&$linea['cISBN']!='')?'[' . $linea['cISBN'] . ']':'';?>
		<?php echo (isset($linea['cAutores'])&&$linea['cAutores']!='')?format_title($linea['cAutores'], $autorlen):'';?>
		</td>
		<td class="item-pvp"><?php echo format_price($importes['fPVP'], FALSE);?></td>
		<td class="item-dto"><?php echo format_number($configuracion['fDescuento']);?></td>
		<!-- <td class="item-base"><?php echo format_price($importes['fBase'], FALSE);?></td>-->
		<td class="item-iva"><?php echo format_number($iva);?></td>
	</tr>
	<?php #$ivas[$iva] = (isset($ivas[$iva])?$ivas[$iva]:0) + $importes['fIVAImporte'];?>
	<?php $importes_pvp[$iva] = (isset($importes_pvp[$iva])?$importes_pvp[$iva]:0) + $importes['fTotal'];?>
	<?php $bases[$iva] = (isset($bases[$iva])?$bases[$iva]:0) + $importes['fBase'];?>
	<?php $total += $importes['fIVAImporte'] + $importes['fBase'];?>
	<?php $s = format_decimals($importes['fBase'] * 0.04);?>
	<!-- <?php echo "<tr><td>{$pvp}</td><td>{$importes['fBase']}</td><td>{$importes['fIVAImporte']}</td><td>{$bases[$iva]}</td><td>{$ivas[$iva]}</td><td>{$total}</td></tr>";?>-->
	<?php endforeach;?>
</table>
<?php $actual++;?> <?php if ($actual != (count($paginas))):?>
<div class="page-break"></div>
<?php endif;?> <?php endforeach;?>
<div style="clear: both"></div>

<?php
$iva = $base = 0;
foreach($importes_pvp as $k => $v)
{
	$b = format_quitar_iva($v, $k);
	$iva += format_iva($b, $k);
	$base += $b;
}
$total = $base + $iva;
?>

<div id="footer">
<table id="totals">
	<tr>
		<td class="total-line"><?php echo $this->lang->line('report-Ejemplares');?></td>
		<td class="total-value"><?php echo format_number($ejemplares);?></td>
	</tr>
	<tr>
		<td class="total-line"><?php echo $this->lang->line('report-Títulos');?></td>
		<td class="total-value"><?php echo format_number($titulos);?></td>
	</tr>
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
</table>
<table id="taxes">
	<tr>
		<td class="taxes-head"><?php echo $this->lang->line('report-IVA');?></td>
		<td class="taxes-head"><?php echo $this->lang->line('report-Base');?></td>
		<td class="taxes-head"><?php echo $this->lang->line('report-Importe');?></td>
	</tr>
	<?php foreach($importes_pvp as $k => $v):?>
	<tr>
		<td class="taxes-value"><?php echo format_number($k);?></td>
		<td class="taxes-value"><?php echo format_price(format_quitar_iva($v, $k), FALSE);?></td>
		<td class="taxes-value"><?php echo format_price(format_iva(format_quitar_iva($v, $k), $k), FALSE);?></td>
	</tr>
	<?php endforeach;?>
</table>
<div style="clear: both"></div>

<div style="clear: both"></div>
<div id="terms"><?php if ($this->lang->line('report-text-albaran-concurso')!=''):?>
<h5><?php echo $this->lang->line('report-Condiciones');?></h5>
<div><?php echo $this->lang->line('report-text-albaran-concurso');?></div>
	<?php endif; ?>
<h5><?php echo $this->lang->line('report-Contacto');?></h5>
<div class="center"><?php echo $this->lang->line('report-Tel.');?>: <?php echo $this->config->item('company.telephone');?>&nbsp;/&nbsp;
<?php echo $this->lang->line('report-Fax.');?>: <?php echo $this->config->item('company.fax');?>
&nbsp;/&nbsp;<?php echo $this->lang->line('report-eMail');?>: <?php echo $this->config->item('company.email');?>
&nbsp;/&nbsp;<?php echo $this->lang->line('report-Web');?>: <?php echo $this->config->item('company.url');?><br />
</div>
</div>
</div>
</div>
