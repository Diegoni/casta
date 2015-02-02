<?php $num_lineas_1 = isset($num_lineas_1)?$num_lineas_1:16;?>
<?php $num_lineas_2 = isset($num_lineas_2)?$num_lineas_2:24;?>
<?php $titlelen = isset($titlelen)?$titlelen:100;?>
<?php $autorlen = isset($autorlen)?$autorlen:80;?>
<?php $refelen = isset($refelen)?$refelen:30;?>
<?php $clientelen = isset($clientelen)?$clientelen:40;?>
<?php $this->load->helper('asset');?>
<?php
$count = 0;
$actual = 0;
$paginas = array();
$pagina = 0;
$titulos = 0;
$ejemplares = 0;
foreach($albaranesagrupados as $linea)
{
	$paginas[$pagina][] = $linea;
	$actual++;
	if ((($pagina == 0) && ($actual == $num_lineas_1))||(($pagina > 0) && ($actual == $num_lineas_2)))
	{
		$pagina++;
		$actual = 0;
	}
}

$obj = get_instance();
$obj->load->model('concursos/m_configuracion');
$data = $obj->m_configuracion->get();
$configuracion = $data[0];
$obj->load->model('concursos/m_albaranagrupado');

?>

<div id="page-wrap">
<div id="header"><?php if (!isset($nNumero)):?> <?php echo $this->lang->line('report-#BORRADOR#');?>&nbsp;<?php endif;?><?php echo $this->lang->line('report-Factura');?></div>
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
<div id="customer-title"><?php echo wordwrap(format_name($cliente['cNombre'], $cliente['cApellido'], $cliente['cEmpresa']), $clientelen, '<br/>');?><br />
<?php echo format_address_print($direccion);?> <?php if (isset($cliente['cNIF']) && ($cliente['cNIF'] != '') && ($cliente['cNIF'] != '0')):?>
<br />
<?php echo $this->lang->line('report-NIF');?>: <?php echo $cliente['cNIF'];?> <?php endif;?>
</div>
<table id="meta">
	<tr>
		<td class="meta-head"><?php echo $this->lang->line('report-Número');?></td>
		<td><?php echo isset($nNumero)?format_numerofactura($nNumero, $serie['nNumero']):$nIdFactura;?></td>
	</tr>
	<tr>

		<td class="meta-head"><?php echo $this->lang->line('report-Fecha');?></td>
		<td>
		<div id="date"><?php echo format_date($dFecha);?></div>
		</td>
	</tr>
	<tr>
		<td class="meta-head"><?php echo $this->lang->line('report-Cliente');?></td>
		<td>
		<div class="due"><?php echo $nIdCliente;?></div>
		</td>
	</tr>
	<tr>
		<td class="meta-head"><?php echo $this->lang->line('report-Referencia');?></td>
		<td>
		<div class="due"><?php echo $cRefCliente;?></div>
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
		<th class="items-th"><?php echo $this->lang->line('report-Albarán');?></th>
		<th class="items-th"><?php echo $this->lang->line('report-Precio c/i');?></th>
		<!-- <th class="items-th"><?php echo $this->lang->line('report-Desc.');?></th>-->
		<!-- <th class="items-th"><?php echo $this->lang->line('report-Base s/i');?></th>-->
		<th class="items-th"><?php echo $this->lang->line('report-Tipo IVA');?></th>
	</tr>
	<?php foreach($pagina as $linea):?>
	<?php 
	$importes = $this->m_albaranagrupado->importe($linea['nIdAlbaranAgrupado'], $configuracion);
	$ejemplares += $importes['ejemplares'];
	$titulos += $importes['titulos'];
	$iva = 4;
	?>
	<tr class="item-row">
		<td class="item-name">
		<?php echo $linea['nIdAlbaranAgrupado']?> - 
		<?php echo format_title($linea['cBiblioteca'], $titlelen);?>
		</td>
		<td class="item-pvp"><?php echo format_price($importes['fTotal'], FALSE);?></td>
		<!-- <td class="item-base"><?php echo format_price($importes['fBase'], FALSE);?></td>-->
		<td class="item-iva"><?php echo format_number($iva);?></td>
	</tr>
	<?php $bases[$iva] = (isset($bases[$iva])?$bases[$iva]:0) + $importes['fBase'];?>
	<?php $importes_pvp[$iva] = (isset($importes_pvp[$iva])?$importes_pvp[$iva]:0) + $importes['fTotal'];?>
	<?php endforeach;?>
	</table>
	<?php $actual++;?>
	<?php if ($actual != (count($paginas))):?>
	<div class="page-break"></div>
	<?php endif;?>
	<?php $iva = 0;?>
	<?php $base = 0;?>
	<?php endforeach;?> 
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
		<td class="total-line"><?php echo $this->lang->line('report-Albaranes');?></td>
		<td class="total-value"><?php echo format_number(count($albaranesagrupados));?></td>
	</tr>
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
	<?php foreach($bases as $k => $v):?>
	<tr>
		<td class="taxes-value"><?php echo format_number($k);?></td>
		<td class="taxes-value"><?php echo format_price($v, FALSE);?></td>
		<td class="taxes-value"><?php echo format_price(format_iva($v, $k), FALSE);?></td>
	</tr>
	<?php endforeach;?>
</table>

<div style="clear: both"></div>
<div id="terms">
<?php if ($this->lang->line('report-text-albaran-concurso')!=''):?>
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