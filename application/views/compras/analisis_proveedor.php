<?php $this->load->helper('extjs');?>
<?php $this->load->helper('asset');?>

<?php 
	$mes[1] = $this->lang->line('Ene.');
	$mes[2] = $this->lang->line('Feb.');
	$mes[3] = $this->lang->line('Mar.');
	$mes[4] = $this->lang->line('Abr.');
	$mes[5] = $this->lang->line('May.');
	$mes[6] = $this->lang->line('Jun.');
	$mes[7] = $this->lang->line('Jul.');
	$mes[8] = $this->lang->line('Ago.');
	$mes[9] = $this->lang->line('Sep.');
	$mes[10] = $this->lang->line('Oct.');
	$mes[11] = $this->lang->line('Nov.');
	$mes[12] = $this->lang->line('Dic.');
?>

<div class="data">
		<h1 class="titulo">
			(<?php echo $proveedor['nIdProveedor']; ?>) <?php echo format_name($proveedor['cNombre'], $proveedor['cApellido'], $proveedor['cEmpresa']);?>
		</h1>
</div>


<?php 
echo '<div style="page-break-inside: avoid;">';
echo '<h2>' . $this->lang->line('Compras') . '</h2><hr/>';
echo '<div id="compras" style="width:600px;height:300px"></div>';
show_table($compras, $this, $mes);
echo '</div>';

echo '<div style="page-break-inside: avoid;">';
echo '<h2>' . $this->lang->line('Devoluciones') . '</h2><hr/>';
echo '<div id="devoluciones" style="width:600px;height:300px"></div>';
show_table($devoluciones, $this, $mes);
echo '</div>';
?>

<div style="page-break-inside: avoid;">
<h2><?echo $this->lang->line('% Devoluciones'); ?></h2><hr/>
<div class="data">
	<table style="width: 1%;">
	<tr>
		<th><?php echo $this->lang->line('Año');?></th>
		<th>%</th>
	</tr>
<?php foreach ($devoluciones as $a => $v): ?>	
	<?php 
		$total = 0;
		$total2 = 0;
		foreach ($v as $valor)
		{
			$total += $valor;
		}
		foreach ($compras[$a] as $valor)
		{
			$total2 += $valor;
		}
	?>
	<tr>
		<td class="label"><?php echo $a;?></td>
		<td class="number"><?php echo format_percent(100 * $total / $total2); ?></td>
	<tr>
<?php endforeach; ?>
	</table>
</div>
</div>
<?php 
function show_table($totales, $lang, $mes)
{
?>
<div class="data">
	<table>
	<tr>
		<th>&nbsp;</th>
		<?php for($i=1; $i<13; $i++):?>
			<th><?php echo $mes[$i];?></th>
		<?php endfor; ?>
		<th><?php echo $lang->lang->line('TOTAL');?></th>
	</tr>

<?php foreach ($totales as $sec => $v): ?>	
	<tr>
		<td class="label"><?php echo $sec;?></td>
		<?php $total = 0; ?>
		<?php foreach ($v as $value):?>
			<td class="number"><?php echo format_price($value, FALSE); ?></td>
			<?php $total += $value; ?>
		<?php endforeach; ?>		
		<td class="number"><?php echo format_price($total, FALSE); ?></td>
	<tr>
<?php endforeach; ?>
	</table>
</div>
<?php
}

function show_data($id, $totales, $mes)
{
	$t = '';
	foreach ($totales as $ano => $a) 
	{
		$data = array();
		foreach($a as $i => $v)
		{
			$data[] = '["' . $mes[$i+1] . '", ' . $v . ']';
		}
		$data = '[' . implode(',', $data) . ']';
		$t[] = '{ label: "'. $ano . '", data: ' . $data . ' }';
	}
	$t = implode(',', $t);

	return '$.plot("#' . $id .'", [ ' . $t . '			
		], {
			series: {
				lines: { show: true },
				points: { show: true }
			},
			xaxis: {
				mode: "categories",
				tickLength: 0
			}
		});';
}

?>
<!--[if lte IE 8]><?php echo js_asset('flot/excanvas.min.js');?></script><![endif]-->
<?php echo js_asset('jQuery/jquery.min.js');?>
<?php echo js_asset('flot/jquery.flot.min.js');?>
<?php echo js_asset('flot/jquery.flot.categories.js');?>
<script type="text/javascript">
$(function () {
	<?php 
	$i = 0;
	echo show_data('compras', $compras, $mes);
	echo show_data('devoluciones', $devoluciones, $mes);
	?>

});
</script>
