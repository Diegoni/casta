<?php $this->load->helper('asset'); ?>
<h1><?php echo $fecha1; ?> &lt;- &gt; <?php echo $fecha2; ?></h1>
<table cellspacing="0" cellpadding="3"
	class="SummaryDataGrid"
	style="BORDER-TOP-WIDTH: 0px; BORDER-LEFT-WIDTH: 0px; BORDER-BOTTOM-WIDTH: 0px; BORDER-COLLAPSE: collapse; BORDER-RIGHT-WIDTH: 0px">
	<tr class="HeaderStyle">
		<td class="HeaderStyle"><?php echo $this->lang->line('Sección'); ?></td>
		<td class="HeaderStyle">&nbsp;</td>
		<td class="HeaderStyle"><?php echo $this->lang->line('Stock Total'); ?>
		</td>
		<td class="HeaderStyle"><?php echo $this->lang->line('Importe'); ?></td>
		<td class="HeaderStyle"><?php echo $this->lang->line('Firme01'); ?></td>
		<td class="HeaderStyle"><?php echo $this->lang->line('Importe01'); ?></td>
		<td class="HeaderStyle"><?php echo $this->lang->line('Firme12'); ?></td>
		<td class="HeaderStyle"><?php echo $this->lang->line('Importe12'); ?></td>
		<td class="HeaderStyle"><?php echo $this->lang->line('Firme23'); ?></td>
		<td class="HeaderStyle"><?php echo $this->lang->line('Importe23'); ?></td>
		<td class="HeaderStyle"><?php echo $this->lang->line('Firme3+'); ?></td>
		<td class="HeaderStyle"><?php echo $this->lang->line('Importe3+'); ?></td>
	</tr>
	<?php foreach($valores as $name => $seccion):
	$v1 = $seccion['valor1'];
	$v2 = $seccion['valor2'];
	?>
	<tr>
		<td class="SelectedSpecial"><?php echo $name; ?></td>
		<td class="SelectedSpecial"><?php echo $fecha1; ?></td>
		<td class="tablaimparright"><?php echo format_number($v1['StockTotal']);?>
		</td>
		<td class="tablaimparright"><?php echo format_price($v1['ImporteTotal']); ?></td>
		<td class="tablaimparright"><?php echo format_number($v1['Firme1']); ?></td>
		<td class="tablaimparright"><?php echo format_price($v1['Importe1']); ?></td>
		<td class="tablaimparright"><?php echo format_number($v1['Firme2']); ?></td>
		<td class="tablaimparright"><?php echo format_price($v1['Importe2']); ?></td>
		<td class="tablaimparright"><?php echo format_number($v1['Firme3']); ?></td>
		<td class="tablaimparright"><?php echo format_price($v1['Importe3']); ?></td>
		<td class="tablaimparright"><?php echo format_number($v1['Firme4']); ?></td>
		<td class="tablaimparright"><?php echo format_price($v1['Importe4']); ?></td>
	</tr>
	<tr>
		<td class="SelectedSpecial">&nbsp;</td>
		<td class="SelectedSpecial">&nbsp;</td>
		<td class="tablaimparright">&nbsp;</td>
		<td class="tablaimparright">&nbsp;</td>
		<td class="tablaimparright">&nbsp;</td>
		<td class="tablaimparright"><?php echo ($v1['ImporteTotal']!=0)?format_percent($v1['Importe1'] * 100 / $v1['ImporteTotal']):0; ?></td>
		<td class="tablaimparright">&nbsp;</td>
		<td class="tablaimparright"><?php echo ($v1['ImporteTotal']!=0)?format_percent($v1['Importe2'] * 100 / $v1['ImporteTotal']):0; ?></td>
		<td class="tablaimparright">&nbsp;</td>
		<td class="tablaimparright"><?php echo ($v1['ImporteTotal']!=0)?format_percent($v1['Importe3'] * 100 / $v1['ImporteTotal']):0; ?></td>
		<td class="tablaimparright">&nbsp;</td>
		<td class="tablaimparright"><?php echo ($v1['ImporteTotal']!=0)?format_percent($v1['Importe4'] * 100 / $v1['ImporteTotal']):0; ?></td>
	</tr>
	<tr>
		<td class="SelectedSpecial">&nbsp;</td>
		<td class="SelectedSpecial"><?php echo $fecha2; ?></td>
		<td class="tablaimparright"><?php echo format_number($v2['StockTotal']);?>
		</td>
		<td class="tablaimparright"><?php echo format_price($v2['ImporteTotal']); ?></td>
		<td class="tablaimparright"><?php echo format_number($v2['Firme1']); ?></td>
		<td class="tablaimparright"><?php echo format_price($v2['Importe1']); ?></td>
		<td class="tablaimparright"><?php echo format_number($v2['Firme2']); ?></td>
		<td class="tablaimparright"><?php echo format_price($v2['Importe2']); ?></td>
		<td class="tablaimparright"><?php echo format_number($v2['Firme3']); ?></td>
		<td class="tablaimparright"><?php echo format_price($v2['Importe3']); ?></td>
		<td class="tablaimparright"><?php echo format_number($v2['Firme4']); ?></td>
		<td class="tablaimparright"><?php echo format_price($v2['Importe4']); ?></td>
	</tr>
	<tr>
		<td class="SelectedSpecial">&nbsp;</td>
		<td class="SelectedSpecial">&nbsp;</td>
		<td class="tablaimparright">&nbsp;</td>
		<td class="tablaimparright">&nbsp;</td>
		<td class="tablaimparright">&nbsp;</td>
		<td class="tablaimparright"><?php echo format_percent($v2['Importe1'] * 100 / $v2['ImporteTotal']); ?></td>
		<td class="tablaimparright">&nbsp;</td>
		<td class="tablaimparright"><?php echo format_percent($v2['Importe2'] * 100 / $v2['ImporteTotal']); ?></td>
		<td class="tablaimparright">&nbsp;</td>
		<td class="tablaimparright"><?php echo format_percent($v2['Importe3'] * 100 / $v2['ImporteTotal']); ?></td>
		<td class="tablaimparright">&nbsp;</td>
		<td class="tablaimparright"><?php echo format_percent($v2['Importe4'] * 100 / $v2['ImporteTotal']); ?></td>
	</tr>
	<tr>
		<td class="SelectedSpecial">&nbsp;</td>
		<td class="SelectedSpecial"><?php echo $this->lang->line('Diff'); ?></td>
		<td class="tablapie"><?php echo format_number($v2['StockTotal'] - $v1['StockTotal']);?>
		</td>
		<td class="tablapie"><?php echo format_price($v2['ImporteTotal'] - $v1['ImporteTotal']); ?></td>
		<td class="tablapie"><?php echo format_number($v2['Firme1'] - $v1['Firme1']); ?></td>
		<td class="tablapie"><?php echo format_price($v2['Importe1'] - $v1['Importe1']); ?></td>
		<td class="tablapie"><?php echo format_number($v2['Firme2'] - $v1['Firme2']); ?></td>
		<td class="tablapie"><?php echo format_price($v2['Importe2'] - $v1['Importe2']); ?></td>
		<td class="tablapie"><?php echo format_number($v2['Firme3'] - $v1['Firme3']); ?></td>
		<td class="tablapie"><?php echo format_price($v2['Importe3'] - $v1['Importe3']); ?></td>
		<td class="tablapie"><?php echo format_number($v2['Firme4'] - $v1['Firme4']); ?></td>
		<td class="tablapie"><?php echo format_price($v2['Importe4'] - $v1['Importe4']); ?></td>
	</tr>
	<tr>
		<td class="SelectedSpecial">&nbsp;</td>
		<td class="SelectedSpecial">%</td>
		<td class="tablapie"><?php echo format_percent(($v2['StockTotal'] - $v1['StockTotal'])* 100/$v1['StockTotal']);?>
		</td>
		<td class="tablapie"><?php echo format_percent(($v2['ImporteTotal'] - $v1['ImporteTotal']) * 100/$v1['ImporteTotal']); ?></td>
		<td class="tablapie"><?php echo format_percent((($v1['Firme1'] != 0)?(($v2['Firme1'] - $v1['Firme1'])* 100/$v1['Firme1']):0)); ?></td>
		<td class="tablapie"><?php echo format_percent(($v1['Importe1'] != 0)?(($v2['Importe1'] - $v1['Importe1'])* 100/$v1['Importe1']):0); ?></td>
		<td class="tablapie"><?php echo format_percent((($v1['Firme2'] != 0)?(($v2['Firme2'] - $v1['Firme2'])* 100/$v1['Firme2']):0)); ?></td>
		<td class="tablapie"><?php echo format_percent(($v1['Importe2'] != 0)?(($v2['Importe2'] - $v1['Importe2'])* 100/$v1['Importe2']):0); ?></td>
		<td class="tablapie"><?php echo format_percent((($v1['Firme3'] != 0)?(($v2['Firme3'] - $v1['Firme3'])* 100/$v1['Firme3']):0)); ?></td>
		<td class="tablapie"><?php echo format_percent(($v1['Importe3'] != 0)?(($v2['Importe3'] - $v1['Importe3'])* 100/$v1['Importe3']):0); ?></td>
		<td class="tablapie"><?php echo format_percent((($v1['Firme4'] != 0)?(($v2['Firme4'] - $v1['Firme4'])* 100/$v1['Firme4']):0)); ?></td>
		<td class="tablapie"><?php echo format_percent(($v1['Importe4'] != 0)?(($v2['Importe4'] - $v1['Importe4'])* 100/$v1['Importe4']):0); ?></td>
	</tr>
	<?php
	//$total1 = array_add($v1, $total1);
	//$total2 = array_add($v2, $total2);
	?>
	<?php endforeach; ?>
	<?php
	$v1= $total1;
	$v2 = $total2;
	?>
	<tr>
		<td class="FooterStyle"><?php echo $this->lang->line('TOTAL'); ?></td>
		<td class="FooterStyleRight"><?php echo $fecha1; ?></td>
		<td class="FooterStyleRight"><?php echo format_number($v1['StockTotal']);?>
		</td>
		<td class="FooterStyleRight"><?php echo format_price($v1['ImporteTotal']); ?></td>
		<td class="FooterStyleRight"><?php echo format_number($v1['Firme1']); ?></td>
		<td class="FooterStyleRight"><?php echo format_price($v1['Importe1']); ?></td>
		<td class="FooterStyleRight"><?php echo format_number($v1['Firme2']); ?></td>
		<td class="FooterStyleRight"><?php echo format_price($v1['Importe2']); ?></td>
		<td class="FooterStyleRight"><?php echo format_number($v1['Firme3']); ?></td>
		<td class="FooterStyleRight"><?php echo format_price($v1['Importe3']); ?></td>
		<td class="FooterStyleRight"><?php echo format_number($v1['Firme4']); ?></td>
		<td class="FooterStyleRight"><?php echo format_price($v1['Importe4']); ?></td>
	</tr>
	<tr>
		<td class="FooterStyle">&nbsp;</td>
		<td class="FooterStyleRight">&nbsp;</td>
		<td class="FooterStyleRight">&nbsp;</td>
		<td class="FooterStyleRight">&nbsp;</td>
		<td class="FooterStyleRight">&nbsp;</td>
		<td class="FooterStyleRight"><?php echo format_percent($v1['Importe1'] * 100 / $v1['ImporteTotal']); ?></td>
		<td class="FooterStyleRight">&nbsp;</td>
		<td class="FooterStyleRight"><?php echo format_percent($v1['Importe2'] * 100 / $v1['ImporteTotal']); ?></td>
		<td class="FooterStyleRight">&nbsp;</td>
		<td class="FooterStyleRight"><?php echo format_percent($v1['Importe3'] * 100 / $v1['ImporteTotal']); ?></td>
		<td class="FooterStyleRight">&nbsp;</td>
		<td class="FooterStyleRight"><?php echo format_percent($v1['Importe4'] * 100 / $v1['ImporteTotal']); ?></td>
	</tr>
	<tr>
		<td class="FooterStyle">&nbsp;</td>
		<td class="FooterStyleRight"><?php echo $fecha2; ?></td>
		<td class="FooterStyleRight"><?php echo format_number($v2['StockTotal']);?>
		</td>
		<td class="FooterStyleRight"><?php echo format_price($v2['ImporteTotal']); ?></td>
		<td class="FooterStyleRight"><?php echo format_number($v2['Firme1']); ?></td>
		<td class="FooterStyleRight"><?php echo format_price($v2['Importe1']); ?></td>
		<td class="FooterStyleRight"><?php echo format_number($v2['Firme2']); ?></td>
		<td class="FooterStyleRight"><?php echo format_price($v2['Importe2']); ?></td>
		<td class="FooterStyleRight"><?php echo format_number($v2['Firme3']); ?></td>
		<td class="FooterStyleRight"><?php echo format_price($v2['Importe3']); ?></td>
		<td class="FooterStyleRight"><?php echo format_number($v2['Firme4']); ?></td>
		<td class="FooterStyleRight"><?php echo format_price($v2['Importe4']); ?></td>
	</tr>
	<tr>
		<td class="FooterStyle">&nbsp;</td>
		<td class="FooterStyleRight">&nbsp;</td>
		<td class="FooterStyleRight">&nbsp;</td>
		<td class="FooterStyleRight">&nbsp;</td>
		<td class="FooterStyleRight">&nbsp;</td>
		<td class="FooterStyleRight"><?php echo format_percent($v2['Importe1'] * 100 / $v2['ImporteTotal']); ?></td>
		<td class="FooterStyleRight">&nbsp;</td>
		<td class="FooterStyleRight"><?php echo format_percent($v2['Importe2'] * 100 / $v2['ImporteTotal']); ?></td>
		<td class="FooterStyleRight">&nbsp;</td>
		<td class="FooterStyleRight"><?php echo format_percent($v2['Importe3'] * 100 / $v2['ImporteTotal']); ?></td>
		<td class="FooterStyleRight">&nbsp;</td>
		<td class="FooterStyleRight"><?php echo format_percent($v2['Importe4'] * 100 / $v2['ImporteTotal']); ?></td>
	</tr>
	<tr>
		<td class="FooterStyle">&nbsp;</td>
		<td class="FooterStyleRight"><?php echo $this->lang->line('Diff'); ?></td>
		<td class="FooterStyleRight"><?php echo format_number($v2['StockTotal'] - $v1['StockTotal']);?>
		</td>
		<td class="FooterStyleRight"><?php echo format_price($v2['ImporteTotal'] - $v1['ImporteTotal']); ?></td>
		<td class="FooterStyleRight"><?php echo format_number($v2['Firme1'] - $v1['Firme1']); ?></td>
		<td class="FooterStyleRight"><?php echo format_price($v2['Importe1'] - $v1['Importe1']); ?></td>
		<td class="FooterStyleRight"><?php echo format_number($v2['Firme2'] - $v1['Firme2']); ?></td>
		<td class="FooterStyleRight"><?php echo format_price($v2['Importe2'] - $v1['Importe2']); ?></td>
		<td class="FooterStyleRight"><?php echo format_number($v2['Firme3'] - $v1['Firme3']); ?></td>
		<td class="FooterStyleRight"><?php echo format_price($v2['Importe3'] - $v1['Importe3']); ?></td>
		<td class="FooterStyleRight"><?php echo format_number($v2['Firme4'] - $v1['Firme4']); ?></td>
		<td class="FooterStyleRight"><?php echo format_price($v2['Importe4'] - $v1['Importe4']); ?></td>
	</tr>
	<tr>
		<td class="FooterStyle">&nbsp;</td>
		<td class="FooterStyleRight"><?php echo $this->lang->line('Inc'); ?></td>
		<td class="FooterStyleRight"><?php echo format_percent(($v2['StockTotal'] - $v1['StockTotal']) * 100/$v1['StockTotal']);?>
		</td>
		<td class="FooterStyleRight"><?php echo format_percent(($v2['ImporteTotal'] - $v1['ImporteTotal']) * 100/$v1['ImporteTotal']); ?></td>
		<td class="FooterStyleRight"><?php echo format_percent(($v2['Firme1'] - $v1['Firme1']) * 100/$v1['Firme1']); ?></td>
		<td class="FooterStyleRight"><?php echo format_percent(($v2['Importe1'] - $v1['Importe1']) * 100/$v1['Importe1']); ?></td>
		<td class="FooterStyleRight"><?php echo format_percent(($v2['Firme2'] - $v1['Firme2'])*100/$v1['Firme2']); ?></td>
		<td class="FooterStyleRight"><?php echo format_percent(($v2['Importe2'] - $v1['Importe2'])*100/$v1['Importe2']); ?></td>
		<td class="FooterStyleRight"><?php echo format_percent(($v2['Firme3'] - $v1['Firme3'])*100/$v1['Firme3']); ?></td>
		<td class="FooterStyleRight"><?php echo format_percent(($v2['Importe3'] - $v1['Importe3'])*100/$v1['Importe3']); ?></td>
		<td class="FooterStyleRight"><?php echo format_percent(($v2['Firme4'] - $v1['Firme4'])*100/$v1['Firme4']); ?></td>
		<td class="FooterStyleRight"><?php echo format_percent(($v2['Importe4'] - $v1['Importe4'])*100/$v1['Importe4']); ?></td>
	</tr>
</table>
<table border="0" bordercolor="#000000" cellspacing="0" cellpadding="3"
	class="SummaryDataGrid"
	style="BORDER-TOP-WIDTH: 0px; BORDER-LEFT-WIDTH: 0px; BORDER-BOTTOM-WIDTH: 0px; BORDER-COLLAPSE: collapse; BORDER-RIGHT-WIDTH: 0px">
	<tr class="HeaderStyle">
		<td colspan="2" class="HeaderStyle"><?php echo $this->lang->line('Depreciacion'); ?></td>
	</tr>
	<tr>
		<td class="SelectedSpecial"><?php echo $fecha1 ?></td>
		<td class="tablaimparright"><?php echo format_price($depreciacion1);?>
		</td>
	</tr>
	<tr>
		<td class="SelectedSpecial"><?php echo $fecha2 ?></td>
		<td class="tablaimparright"><?php echo format_price($depreciacion2);?>
		</td>
	</tr>
	<tr>
		<td class="SelectedSpecial"><?php echo $this->lang->line('Diff'); ?></td>
		<td class="tablaimparright"><?php echo format_price($depreciacion2 - $depreciacion1);?>
		</td>
	</tr>
	<?php if (isset($depreciacion_fecha)):?>
	<tr>
		<td class="SelectedSpecial"><?php echo $depreciacion_fecha ?></td>
		<td class="tablaimparright"><?php echo format_price($depreciacion_ant);?>
		</td>
	</tr>
	<tr>
		<td class="SelectedSpecial"><?php echo $this->lang->line('Diff'); ?></td>
		<td class="tablaimparright"><?php echo format_price($depreciacion2 - $depreciacion_ant);?>
		</td>
	</tr>
	<?php endif;?>
</table>
