<h1><?php echo $this->lang->line('Fecha Inicial'); ?>: <?php echo $fecha1; ?> - <?php echo $this->lang->line('Fecha Final'); ?>: <?php echo $fecha2; ?>
</h1>

<table cellspacing="0" cellpadding="3"
	class="SummaryDataGrid"
	style="BORDER-TOP-WIDTH: 0px; BORDER-LEFT-WIDTH: 0px; BORDER-BOTTOM-WIDTH: 0px; BORDER-COLLAPSE: collapse; BORDER-RIGHT-WIDTH: 0px">
	<tr class="HeaderStyle">
		<td rowspan="2" class="HeaderStyle"><?php echo $this->lang->line('Área'); ?></td>
		<td rowspan="2" class="HeaderStyle"><?php echo $this->lang->line('Serie'); ?></td>
		<td colspan="2" align="center" class="HeaderStyle"><?php echo $year2; ?>
		</td>
		<td colspan="2" align="center" class="HeaderStyle"><?php echo $year1; ?>
		</td>
		<td colspan="2" align="center" class="HeaderStyle"><?php echo $this->lang->line('Comparativa'); ?></td>
		<td colspan="2" align="center" class="HeaderStyle"><?php echo $this->lang->line('Pesos'); ?></td>
	</tr>
	<tr class="HeaderStyle">
		<td colspan="1" align="center" class="HeaderStyle"><?php echo $this->lang->line('Mes'); ?></td>
		<td colspan="1" align="center" class="HeaderStyle"><?php echo $this->lang->line('Año'); ?></td>
		<td colspan="1" align="center" class="HeaderStyle"><?php echo $this->lang->line('Mes'); ?></td>
		<td colspan="1" align="center" class="HeaderStyle"><?php echo $this->lang->line('Año'); ?></td>
		<td colspan="1" align="center" class="HeaderStyle"><?php echo $this->lang->line('Mes'); ?></td>
		<td colspan="1" align="center" class="HeaderStyle"><?php echo $this->lang->line('Año'); ?></td>
		<td colspan="1" align="center" class="HeaderStyle"><?php echo $this->lang->line('Mes'); ?></td>
		<td colspan="1" align="center" class="HeaderStyle"><?php echo $this->lang->line('Año'); ?></td>
	</tr>
	<?php foreach($valores['areas'] as $area):?>
	<tr class="CategoryHeaderHier">
		<td class="CategoryHeaderHier" colspan="10"><?php echo $area['Area'];?>
		</td>
	</tr>
	<?php foreach($area['series'] as $serie): ?>
	<tr class="tablaimpar">
		<td class="Line1" colspan="2"><?php echo $serie['nNumero']; ?>-<?php echo $serie['Serie']; ?>
		</td>
		<td align="right" class="Line2"><?php echo format_price($serie['mes1']['fImporte']); ?>
		</td>
		<td align="right" class="Line2"><?php echo format_price($serie['anno1']['fImporte']); ?>
		</td>
		<td align="right" class="Line1"><?php echo format_price($serie['mes2']['fImporte']); ?>
		</td>
		<td align="right" class="Line1"><?php echo format_price($serie['anno2']['fImporte']); ?>
		</td>
		<td align="right" class="Line2"><?php 
		$p = $serie['m_diff'];
		$d = ($serie['mes2']['fImporte'] != 0)?($p / $serie['mes2']['fImporte'])*100:0;
		$p2 = $serie['a_diff'];
		$d2 = ($serie['anno2']['fImporte'] != 0)?($p2 / $serie['anno2']['fImporte'])*100:0;
		?> <?php echo format_price($p); ?> <BR />
		<?php echo format_percent($d); ?></td>
		<td align="right" class="Line2"><?php echo format_price($p2); ?> <BR />
		<?php echo format_percent($d2); ?></td>
		<?php
		$d = ($area['mes1'] != 0)?($serie['mes1']['fImporte'] / $area['mes1'])*100:0;
		$d2 = ($valores['anno1'] != 0)?($serie['anno1']['fImporte'] / $valores['anno1'])*100:0;
		?>
		<td align="right" class="Line3">
		<?php echo format_percent($d); ?></td>
		<td align="right" class="Line3">
		<?php echo format_percent($d2); ?></td>
	</tr>
	<?php endforeach; ?>
	<tr class="CategoryHeader">
		<td class="CategoryHeader" colspan="2"><?php echo $this->lang->line('TOTAL'); ?>
		<?php echo $area['Area'];?></td>
		<td align="right" class="CategoryHeader"><?php echo format_price($area['mes1']); ?>
		</td>
		<td align="right" class="CategoryHeader"><?php echo format_price($area['anno1']); ?>
		</td>
		<td align="right" class="CategoryHeader2"><?php echo format_price($area['mes2']); ?>

		</td>
		<td align="right" class="CategoryHeader2"><?php echo format_price($area['anno2']); ?>

		</td>
		<td align="right" class="CategoryHeader"><?php 
		$p = $area['m_diff'];
		$d = ($area['mes2'] != 0)?($p / $area['mes2'])*100:0;
		$p2 = $area['a_diff'];
		$d2 = ($area['anno2'] != 0)?($p2 / $area['anno2'])*100:0;
		?> <?php echo format_price($p); ?> <BR />
		<?php echo format_percent($d); ?></td>
		<td align="right" class="CategoryHeader"><?php echo format_price($p2); ?>
		<BR />
		<?php echo format_percent($d2); ?></td>
		<?php
		$d = ($valores['mes1'] != 0)?($area['mes1'] / $valores['mes1'])*100:0;
		$d2 = ($valores['anno1'] != 0)?($area['anno1'] / $valores['anno1'])*100:0;
		?>
		<td align="right" class="CategoryHeader3">
		<?php echo format_percent($d); ?></td>
		<td align="right" class="CategoryHeader3">
		<?php echo format_percent($d2); ?></td>
	</tr>
	<?php endforeach; ?>

	<tr class="FooterStyle">
		<td class="FooterStyle" colspan="2"><?php echo $this->lang->line('TOTAL Periodo'); ?></td>
		<td align="right" class="FooterStyle"><?php echo format_price($valores['mes1']); ?>
		</td>
		<td align="right" class="FooterStyle"><?php echo format_price($valores['anno1']); ?>
		</td>
		<td align="right" class="FooterStyle"><?php echo format_price($valores['mes2']); ?>
		</td>
		<td align="right" class="FooterStyle"><?php echo format_price($valores['anno2']); ?>
		</td>
		<td align="right" class="FooterStyle"><?php 
		$p = $valores['m_diff'];
		$d = ($valores['mes2'] != 0)?($p / $valores['mes2'])*100:0;
		$p2 = $valores['a_diff'];
		$d2 = ($valores['anno2'] != 0)?($p2 / $valores['anno2'])*100:0;
		?> <?php echo format_price($p); ?> <BR />
		<?php echo format_percent($d); ?></td>
		<td align="right" class="FooterStyle"><?php echo format_price($p2); ?>
		<BR />
		<?php echo format_percent($d2); ?></td>
	</tr>
</table>
