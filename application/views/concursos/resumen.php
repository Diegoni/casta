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
$concurso = $gastado = 0;
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
<?php 
$total = 0;
$ivas = array();
$bases = array();
$importes_pvp = array();
$actual = 0;
?>
<table class="items">
	<tr>
		<th class="items-th"><?php echo $this->lang->line('report-Albarán');?></th>
		<th class="items-th"><?php echo $this->lang->line('report-Precio c/i');?></th>
		<th class="items-th"><?php echo $this->lang->line('Concurso');?></th>
		<th class="items-th"><?php echo $this->lang->line('Pendiente');?></th>
	</tr>
	<?php foreach($albaranesagrupados as $linea):?>
	<?php 
	$ejemplares += $linea['ejemplares'];
	$titulos += $linea['titulos'];
	$iva = 4;
	?>
	<tr class="item-row">
		<td class="item-name">
			<?php echo $linea['nIdCliente'];?> - 
		<?php echo format_title($linea['cBiblioteca'], $titlelen);?>
		</td>
		<td class="item-pvp"><?php echo format_price($linea['fTotal'], FALSE);?></td>
		<td class="item-pvp"><?php echo format_price($linea['fImporte2'], FALSE);?></td>
		<td class="item-pvp"><?php $d = format_decimals($linea['fTotal'] - $linea['fImporte2']);
		 echo '<span style="color:' . (($d==0)?'black':(($d < 0)?'red':'green')) .';">'  . format_price($d, FALSE) .'</span>';?></td>
	</tr>
	<?php $bases[$iva] = (isset($bases[$iva])?$bases[$iva]:0) + $linea['fBase'];?>
	<?php $importes_pvp[$iva] = (isset($importes_pvp[$iva])?$importes_pvp[$iva]:0) + $linea['fTotal'];?>
	<?php $concurso += $linea['fImporte2']; ?>
	<?php $gastado += $linea['fTotal']; ?>
	<?php endforeach;?>
	</table>
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
		<td class="total-line"><?php echo $this->lang->line('Concurso');?></td>
		<td class="total-value"><?php echo format_price($concurso);?></td>
	</tr>
	<tr>
		<td class="total-line"><?php echo $this->lang->line('Gastado');?></td>
		<td class="total-value"><?php echo format_price($gastado);?></td>
	</tr>
	<tr>
		<td class="total-line"><?php echo $this->lang->line('Diferencia');?></td>
		<td class="total-value"><?php echo format_price($concurso - $gastado);?></td>
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
</div>
</div>
