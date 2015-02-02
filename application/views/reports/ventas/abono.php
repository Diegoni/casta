<?php $this->load->helper('asset');?>
<?php
$titulo = $this->lang->line('report-Abono');
$borrador = FALSE;
$nIdDocumento = $nIdAbono;
$texto_condiciones = $this->lang->line('text-abono');
if ($bNoCaduca !== 1) $texto_condiciones .= (($texto_condiciones!='')?'<br/>':'') . str_replace('%cd%', $this->config->item('bp.abono.caducidad'), $this->lang->line('text-caducidad'));
#$texto_email = $this->lang->line('text-devolucion-email');
?>
<?php require(APPPATH . 'views' . DIRECTORY_SEPARATOR . 'reports' . DIRECTORY_SEPARATOR . 'params.php'); ?>
<div id="page-wrap"><?php require(APPPATH . 'views' . DIRECTORY_SEPARATOR . 'reports' . DIRECTORY_SEPARATOR . 'header.php'); ?>

<table class="items">
	<tr>
		<th class="items-th"><?php echo $this->lang->line('report-Fecha');?></th>
		<th class="items-th"><?php echo $this->lang->line('report-Factura');?></th>
		<th class="items-th"><?php echo $this->lang->line('report-Importe');?></th>
	</tr>
	<?php foreach($modospago as $linea):?>
	<tr class="item-row">
		<td><?php echo format_date($linea['dFecha']);?></td>
		<td><?php echo $linea['nIdFactura'];?></td>
		<td class="item-pvp"><?php echo ($linea['fImporte'])<0?$this->lang->line('report-CREADO'). '<br/>'.format_price(-$linea['fImporte'], FALSE):$this->lang->line('report-USADO') . '<br/>'.format_price($linea['fImporte'], FALSE);?></td>
	</tr>
	<?php endforeach;?>
</table>
<div id="footer">
<table id="totals">
	<tr>
		<td class="total-line"><?php echo $this->lang->line('report-Importe');?></td>
		<td class="total-value"><?php echo format_price($fImporte);?></td>
	</tr>
	<tr>
		<td class="total-line"><?php echo $this->lang->line('report-Usado');?></td>
		<td class="total-value"><?php echo format_price($fUsado);?></td>
	</tr>
	<tr>
		<td class="total-line"><?php echo $this->lang->line('report-Pendiente');?></td>
		<td class="total-value"><?php echo format_price($fPendiente);?></td>
	</tr>
</table>
<div style="clear: both"></div>
<?php require(APPPATH . 'views' . DIRECTORY_SEPARATOR . 'reports' . DIRECTORY_SEPARATOR . 'terms.php'); ?>
</div>
</div>
