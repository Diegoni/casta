<?php $this->load->helper('asset'); ?>
<h1><?php echo $fecha1; ?></h1>
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
	<?php endforeach; ?>
	<?php
	$v1= $total1;
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
	<?php if (isset($depreciacion_fecha)):?>
	<tr>
		<td class="SelectedSpecial"><?php echo $depreciacion_fecha ?></td>
		<td class="tablaimparright"><?php echo format_price($depreciacion_ant);?>
		</td>
	</tr>
	<tr>
		<td class="SelectedSpecial"><?php echo $this->lang->line('Diff'); ?></td>
		<td class="tablaimparright"><?php echo format_price($depreciacion1 - $depreciacion_ant);?>
		</td>
	</tr>
	<?php endif;?>
</table>
