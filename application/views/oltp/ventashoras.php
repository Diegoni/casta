<?php $this->load->library('HtmlFile'); ?>
<?php echo get_instance()->htmlfile->orientation(ORIENTATION_LANDSCAPE); ?>
<?php $this->load->helper('asset');?>
<h1><?php echo $this->lang->line('Ventas por horas y días');?> <?php echo $fecha1; ?> &lt;-&gt; <?php echo $fecha2; ?>
<?php if (!empty($seccion)):?>
<br/><?php echo $this->lang->line('Sección'); ?>: <?php echo $seccion; ?>
<?php endif;?>
<br/><?php echo $this->lang->line(($sj)?'SANT JORDI INCLUIDO':'SANT JORDI NO INCLUIDO'); ?>
</h1>

<?php foreach($valores as $year => $data):?>
	<h2><?php echo $year; ?></h2>
	<?php foreach($data as $mes => $data_mes):?>
	<div style="page-break-inside: avoid;">
	<h3><?php echo $this->lang->line('mes_'.$mes); ?></h3>
	<table cellspacing="0" cellpadding="3" class="SummaryDataGrid"
		style="BORDER-TOP-WIDTH: 0px; BORDER-LEFT-WIDTH: 0px; BORDER-BOTTOM-WIDTH: 0px; BORDER-COLLAPSE: collapse; BORDER-RIGHT-WIDTH: 0px">
		<tr class="HeaderStyle">
			<th class="HeaderStyle">#</th>
			<?php foreach($data_mes['datos'] as $dia => $data_dia):?>
			<?php $d = array_pop($data_dia); ?>
			<th class="HeaderStyle"><?php echo $dia;?><br/><?php echo $this->lang->line('dia_s_' . $d['dw']);?><br/><?php echo $d['wk'];?></th>
			<?php endforeach; ?>
			<th class="HeaderStyle">&nbsp;</th>
		</tr>
		<?php $h = $data_mes['min']; ?>
		<?php while ($h <= $data_mes['max']): ?>
			<tr class="Line2">
				<td align="left" class="SelectedBold"><?php echo $h;?></td>
				<?php foreach($data_mes['datos'] as $dia => $data_dia):?>
					<td align="right" class="tablaimparright"><?php echo (isset($data_dia[$h]['vv'])?format_price($data_dia[$h]['vv'], FALSE):'');?></td>
				<?php endforeach; ?>
				<td align="right" class="tablapie"><?php echo (isset($data_mes['total_hh'][$h])?format_price($data_mes['total_hh'][$h], FALSE):'');?></td>
			</tr>
			<?php ++$h;?>
		<?php endwhile; ?>		
		<tr>
			<td class="Line2">&nbsp;</td>
			<?php foreach($data_mes['datos'] as $dia => $data_dia):?>
				<td class="tablapie"><?php echo format_price($data_mes['total_dia'][$dia], FALSE); ?></td>
			<?php endforeach; ?>
			<td class="SelectedSpecial"><?php echo format_price($data_mes['total'], FALSE); ?></td>
		</tr>
	</table>
	<br/>

	<table>
	<tr>
		<td>
			<table cellspacing="0" cellpadding="3" class="SummaryDataGrid"
				style="BORDER-TOP-WIDTH: 0px; BORDER-LEFT-WIDTH: 0px; BORDER-BOTTOM-WIDTH: 0px; BORDER-COLLAPSE: collapse; BORDER-RIGHT-WIDTH: 0px">
				<tr class="HeaderStyle">
					<th class="HeaderStyle"><?php echo $this->lang->line('Hora'); ?></th>
					<th class="HeaderStyle"><?php echo $this->lang->line('Total'); ?></th>
				</tr>
				<?php $h = $data_mes['min']; ?>
				<?php while ($h <= $data_mes['max']): ?>
					<tr class="Line2">
						<td align="left" class="SelectedBold"><?php echo $h;?></td>
						<td align="right" class="tablapie"><?php echo (isset($data_mes['total_hh'][$h])?format_price($data_mes['total_hh'][$h], FALSE):'');?></td>
					</tr>
					<?php ++$h;?>
				<?php endwhile; ?>		
				<tr>
					<td class="Line2">&nbsp;</td>
					<td class="SelectedSpecial"><?php echo format_price($data_mes['total'], FALSE); ?></td>
				</tr>
			</table>
		</td>
		<td>
			<table cellspacing="0" cellpadding="3" class="SummaryDataGrid"
				style="BORDER-TOP-WIDTH: 0px; BORDER-LEFT-WIDTH: 0px; BORDER-BOTTOM-WIDTH: 0px; BORDER-COLLAPSE: collapse; BORDER-RIGHT-WIDTH: 0px">
				<tr class="HeaderStyle">
					<th class="HeaderStyle">#</th>
					<th class="HeaderStyle"><?php echo $this->lang->line('Total'); ?></th>
				</tr>
				<?php $h = 1; ?>
				<?php while ($h <= 7): ?>
					<tr class="Line2">
						<td align="left" class="SelectedBold"><?php echo $this->lang->line('dia_s_' . $h);?></td>
						<td align="right" class="tablapie"><?php echo (isset($data_mes['total_dw'][$h])?format_price($data_mes['total_dw'][$h], FALSE):'');?></td>
					</tr>
					<?php ++$h;?>
				<?php endwhile; ?>		
				<tr>
					<td class="Line2">&nbsp;</td>
					<td class="SelectedSpecial"><?php echo format_price($data_mes['total'], FALSE); ?></td>
				</tr>
			</table>
		</td>
		<td>
			<div id="dias_<?php echo $mes;?>" style="width:300px;height:200px"></div>
		</td>
		<td>
			<div id="horas_<?php echo $mes;?>" style="width:300px;height:200px"></div>
		</td>
		<td>
			<div id="dw_<?php echo $mes;?>" style="width:300px;height:200px"></div>
		</td>
		<td>
			<div id="wk_<?php echo $mes;?>" style="width:300px;height:200px"></div>
		</td>
	</tr>
	</table>
	</div>
	<?php endforeach; ?>
	<div style="page-break-inside: avoid;">
	<h3><?php echo $this->lang->line('Total') .' ' . $year; ?></h3>
	<?php $data=$subs[$year]; ?>
	<table>
	<tr>
		<td>
			<table cellspacing="0" cellpadding="3" class="SummaryDataGrid"
				style="BORDER-TOP-WIDTH: 0px; BORDER-LEFT-WIDTH: 0px; BORDER-BOTTOM-WIDTH: 0px; BORDER-COLLAPSE: collapse; BORDER-RIGHT-WIDTH: 0px">
				<tr class="HeaderStyle">
					<th class="HeaderStyle"><?php echo $this->lang->line('Hora'); ?></th>
					<th class="HeaderStyle"><?php echo $this->lang->line('Total'); ?></th>
				</tr>
				<?php $h = $data['min']; ?>
				<?php while ($h <= $data['max']): ?>
					<tr class="Line2">
						<td align="left" class="SelectedBold"><?php echo $h;?></td>
						<td align="right" class="tablapie"><?php echo (isset($data['total_hh'][$h])?format_price($data['total_hh'][$h], FALSE):'');?></td>
					</tr>
					<?php ++$h;?>
				<?php endwhile; ?>		
				<tr>
					<td class="Line2">&nbsp;</td>
					<td class="SelectedSpecial"><?php echo format_price($data['total'], FALSE); ?></td>
				</tr>
			</table>
		</td>
		<td>
			<table cellspacing="0" cellpadding="3" class="SummaryDataGrid"
				style="BORDER-TOP-WIDTH: 0px; BORDER-LEFT-WIDTH: 0px; BORDER-BOTTOM-WIDTH: 0px; BORDER-COLLAPSE: collapse; BORDER-RIGHT-WIDTH: 0px">
				<tr class="HeaderStyle">
					<th class="HeaderStyle">#</th>
					<th class="HeaderStyle"><?php echo $this->lang->line('Total'); ?></th>
				</tr>
				<?php $h = 1; ?>
				<?php while ($h <= 7): ?>
					<tr class="Line2">
						<td align="left" class="SelectedBold"><?php echo $this->lang->line('dia_s_' . $h);?></td>
						<td align="right" class="tablapie"><?php echo (isset($data['total_dw'][$h])?format_price($data['total_dw'][$h], FALSE):'');?></td>
					</tr>
					<?php ++$h;?>
				<?php endwhile; ?>		
				<tr>
					<td class="Line2">&nbsp;</td>
					<td class="SelectedSpecial"><?php echo format_price($data['total'], FALSE); ?></td>
				</tr>
			</table>
		</td>
		<td>
			<table cellspacing="0" cellpadding="3" class="SummaryDataGrid"
				style="BORDER-TOP-WIDTH: 0px; BORDER-LEFT-WIDTH: 0px; BORDER-BOTTOM-WIDTH: 0px; BORDER-COLLAPSE: collapse; BORDER-RIGHT-WIDTH: 0px">
				<tr class="HeaderStyle">
					<th class="HeaderStyle"><?php echo $this->lang->line('Mes'); ?></th>
					<th class="HeaderStyle"><?php echo $this->lang->line('Total'); ?></th>
				</tr>
				<?php foreach($data['total_mm'] as $mes => $datos): ?>
					<tr class="Line2">
						<td align="left" class="SelectedBold"><?php echo $this->lang->line('mes_s_' . $mes);?></td>
						<td align="right" class="tablapie"><?php echo (isset($datos)?format_price($datos, FALSE):'');?></td>
					</tr>
				<?php endforeach; ?>		
				<tr>
					<td class="Line2">&nbsp;</td>
					<td class="SelectedSpecial"><?php echo format_price($data['total'], FALSE); ?></td>
				</tr>
			</table>
		</td>
		<td>
			<div id="year_horas_<?php echo $year;?>" style="width:300px;height:200px"></div>
		</td>
		<td>
			<div id="year_dw_<?php echo $year;?>" style="width:300px;height:200px"></div>
		</td>
		<td>
			<div id="year_mm_<?php echo $year;?>" style="width:300px;height:200px"></div>
		</td>
	</tr>
	</table>
	</div>

<?php endforeach; ?>

<!--[if lte IE 8]><?php echo js_asset('flot/excanvas.min.js');?></script><![endif]-->
<?php echo js_asset('jQuery/jquery.min.js');?>
<?php echo js_asset('flot/jquery.flot.min.js');?>
<?php echo js_asset('flot/jquery.flot.categories.js');?>
<script type="text/javascript">
$(function () {
<?php 
	foreach($valores as $year => $data) 
	{
		foreach($data as $mes => $data_mes) 
		{
	 		echo format_data_plot('horas_'. $mes, $this->lang->line('Hora'), $data_mes['total_hh']);
	 		$dias = array();
	 		for($h = 1; $h <=7; $h++)
	 		{
	 			if (isset($data_mes['total_dw'][$h]))
	 				$dias[$this->lang->line('dia_s_' . $h)] = $data_mes['total_dw'][$h];
	 		}
	 		echo format_data_plot('dw_'. $mes, $this->lang->line('Día Semana'), $dias);
	 		echo format_data_plot('dias_'. $mes, $this->lang->line('Día'), $data_mes['total_dia']);
	 		$semanas = array();
	 		foreach ($data_mes['total_wk_dw'] as $k => $values)
	 		{
		 		for($h = 1; $h <=7; $h++)
		 		{		 			
		 			$semanas[$k][$this->lang->line('dia_s_' . $h)] = isset($values[$h])?$values[$h]:0;
		 		}	 		
	 		}
	 		echo format_data_plot_multi('wk_'. $mes, $semanas);
	 	}
	 	$data = $subs[$year];
 		echo format_data_plot('year_horas_'. $year, $this->lang->line('Hora'), $data['total_hh']);
 		$dias = array();
 		for($h = 1; $h <=7; $h++)
 		{
 			if (isset($data['total_dw'][$h]))
 				$dias[$this->lang->line('dia_s_' . $h)] = $data['total_dw'][$h];
 		}
 		echo format_data_plot('year_dw_'. $year, $this->lang->line('Día Semana'), $dias);
 		echo format_data_plot('year_mm_'. $year, $this->lang->line('Mes'), $data['total_mm']);
 		/*$semanas = array();
 		foreach ($data['total_wk_dw'] as $k => $values)
 		{
	 		for($h = 1; $h <=7; $h++)
	 		{		 			
	 			$semanas[$k][$this->lang->line('dia_s_' . $h)] = isset($values[$h])?$values[$h]:0;
	 		}	 		
 		}
 		echo format_data_plot_multi('year_wk_'. $year, $semanas);
 		*/
	}
?>
});
</script>
