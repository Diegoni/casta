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
<div class="data"><table>
	<tr>
		<td
			width="<?php echo $this->config->item('bp.catalogo.cover.articulo');?>">
			<?php echo format_cover($articulo['nIdLibro'], $this->config->item('bp.catalogo.cover.articulo'), 'portada');?>
		</td>
	<td>
		<h1 class="titulo">(<?php echo $articulo['nIdLibro']; ?>) <?php echo $articulo['cTitulo']; ?></h1>
	</td>
</tr>
</table>
</div>

<div style="page-break-inside: avoid;">
	<h2><?php echo $this->lang->line('TOTAL');?></h2>
<table><tr><td>
<div id="totales" style="width:600px;height:300px"></div>
</td><td>
<?php show_table($totales, $this, $mes); ?>
</td></tr></table>'
</div>
<?php 
$j = 0;
foreach ($secciones as $sec => $v)
{
	echo '<div style="page-break-inside: avoid;">';
	echo '<h2>' . $sec . '</h2>';
	echo '<table><tr><td>';
	echo '<div id="sec_' . $j . '" style="width:600px;height:300px"></div>';
	echo '</td><td>';
	show_table($v, $this, $mes);
	echo '</td></tr></table>';
	echo '</div>';
	++$j;
}

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

function show_data($id, $totales, $anos, $mes)
{
	$t = '';
	foreach ($anos as $a) 
	{
		if (isset($totales[$a])) 
		{
			$data = array();
			foreach($totales[$a] as $i => $v)
			{
				$data[] = '["' . $mes[$i] . '", ' . $v . ']';
			}
			$data = '[' . implode(',', $data) . ']';
			$t[] = '{ label: "'. $a . '", data: ' . $data . ' }';
		}
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
	<?php echo show_data('totales', $totales, $anos, $mes); ?>
	<?php 
	$i = 0;
	foreach ($secciones as $sec => $v)
	{
		echo show_data('sec_' . $i, $v, $anos, $mes);
		++$i;
	}
	?>

});
</script>
