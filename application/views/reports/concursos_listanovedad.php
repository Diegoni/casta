<?php $num_lineas_1 = isset($num_lineas_1)?$num_lineas_1:18;?>
<?php $num_lineas_2 = isset($num_lineas_2)?$num_lineas_2:24;?>
<?php $titlelen = isset($titlelen)?$titlelen:100;?>
<?php $autorlen = isset($autorlen)?$autorlen:80;?>
<?php $refelen = isset($refelen)?$refelen:30;?>
<?php $clientelen = isset($clientelen)?$clientelen:40;?>
<?php
$obj = get_instance();
$obj->load->helper('asset');
$count = 0;
$actual = 0;
$paginas = array();
$pagina = 0;
sksort($lineas, 'cTitulo');
foreach($lineas as $linea)
{
	$paginas[$pagina][] = $linea;
	$actual++;
	if ((($pagina == 0) && ($actual == $num_lineas_1))||(($pagina > 0) && ($actual == $num_lineas_2)))
	{
		$pagina++;
		$actual = 0;
	}
}
?>

<div id="page-wrap">
<div id="header"><?php echo $this->lang->line('report-Servicio de novedades');?></div>
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
<div id="customer-title"><?php echo wordwrap($cDescripcion, $clientelen, '<br/>');?><br />
</div>
<table id="meta">
	<tr>
		<td class="meta-head"><?php echo $this->lang->line('report-Número');?></td>
		<td><?php echo $nIdLista;?></td>
	</tr>
	<tr>
		<td class="meta-head"><?php echo $this->lang->line('report-Fecha');?></td>
		<td>
		<div id="date"><?php echo format_date(isset($dCreacion)?$dCreacion:time());?></div>
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
?> <?php foreach($paginas as $pagina):?>
<table class="items">
	<tr>
		<th class="items-th"><?php echo $this->lang->line('report-Cant');?></th>
		<th class="items-th"><?php echo $this->lang->line('report-Título');?></th>
		<th class="items-th"><?php echo $this->lang->line('report-Precio c/i');?></th>
	</tr>
	<?php foreach($pagina as $linea):?>
	<tr class="item-row">
		<td class="border">&nbsp;
		</td>
		<td class="item-name"><?php echo format_title($linea['cTitulo'], $titlelen);?><br />
		<?php echo (isset($linea['cISBN'])&&$linea['cISBN']!='')?'[' . $linea['cISBN'] . ']':'';?>
		<?php echo (isset($linea['cAutores'])&&$linea['cAutores']!='')?format_title($linea['cAutores'], $autorlen):'';?>
		</td>
		<td class="item-pvp"><?php echo format_price($linea['fPVP'], FALSE);?></td>
	</tr>
	<?php endforeach;?>
</table>
<?php $actual++;?> <?php if ($actual != (count($paginas))):?>
<div class="page-break"></div>
<?php endif;?> <?php endforeach;?>
<div style="clear: both"></div>

<div id="footer">
<table id="totals">
	<tr>
		<td class="total-line"><?php echo $this->lang->line('report-Títulos');?></td>
		<td class="total-value"><?php echo format_number(count($lineas));?></td>
	</tr>
</table>
<div style="clear: both"></div>

<div style="clear: both"></div>
<div id="terms"><?php if ($this->lang->line('report-text-lista-novedad')!=''):?>
<h5><?php echo $this->lang->line('report-Condiciones');?></h5>
<div><?php echo $this->lang->line('report-text-lista-novedad');?></div>
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
