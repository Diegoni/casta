<table border="0" bordercolor="#000000" cellspacing="0" cellpadding="3"
	class="SummaryDataGrid"
	style="BORDER-TOP-WIDTH: 0px; BORDER-LEFT-WIDTH: 0px; BORDER-BOTTOM-WIDTH: 0px; BORDER-COLLAPSE: collapse; BORDER-RIGHT-WIDTH: 0px">

	<?php $super = array('f1' => 0, 'f2' => 0, 'f3' => 0, 'f4' => 0); ?>
	<?php foreach($datos as $valor):?>
		<tr>
			<td colspan="6"
				class="CategoryHeaderHier"><?php echo $valor['nombre']; ?></td>
		</tr>
	<tr class="HeaderStyle">
		<td align="center" class="HeaderStyle">&nbsp;</td>
		<td align="center" class="HeaderStyle"><?php echo $this->lang->line('F1'); ?></td>
		<td align="center" class="HeaderStyle"><?php echo $this->lang->line('F2'); ?></td>
		<td align="center" class="HeaderStyle"><?php echo $this->lang->line('F3'); ?></td>
		<td align="center" class="HeaderStyle"><?php echo $this->lang->line('F4'); ?></td>
		<td align="center" class="HeaderStyle"><?php echo $this->lang->line('Total'); ?></td>
	</tr>
		<?php $subtotal = array('f1' => 0, 'f2' => 0, 'f3' => 0, 'f4' => 0); ?>
		<?php foreach ($valor['lineas'] as $linea): ?>
			<?php $total = $linea['f1'] + $linea['f2'] + $linea['f3'] + $linea['f4']; ?>
			<tr>
				<td nowrap="true" class="CategoryHeader3"><?php echo $linea['cNombre']; ?></td>
				<td class="tablaimparright"><?php echo format_number($linea['f1']); ?></td>
				<td class="tablaimparright"><?php echo format_number($linea['f2']); ?></td>
				<td class="tablaimparright"><?php echo format_number($linea['f3']); ?></td>
				<td class="tablaimparright"><?php echo format_number($linea['f4']); ?></td>
				<td align="right" class="SelectedBold"><?php echo format_number($total); ?></td>
			</tr>
			<tr>
				<td nowrap="true" class="CategoryHeader3">&nbsp;</td>
				<td class="tablaimparright"><?php echo format_percent(100*$linea['f1']/$total); ?></td>
				<td class="tablaimparright"><?php echo format_percent(100*$linea['f2']/$total); ?></td>
				<td class="tablaimparright"><?php echo format_percent(100*$linea['f3']/$total); ?></td>
				<td class="tablaimparright"><?php echo format_percent(100*$linea['f4']/$total); ?></td>
				<td class="tablaimparright">&nbsp;</td>
			</tr>
			<?php 
				$subtotal['f1'] += $linea['f1']; 
				$subtotal['f2'] += $linea['f2']; 
				$subtotal['f3'] += $linea['f3'];
				$subtotal['f4'] += $linea['f4'];
			?>
		<?php endforeach; ?>
		<tr>
			<td class="tablapie">&nbsp;</td>
			<?php $total = $subtotal['f1'] + $subtotal['f2'] + $subtotal['f3'] + $subtotal['f4']; ?>
			<td align="right" class="tablapie"><?php echo format_number($subtotal['f1']); ?></td>
			<td align="right" class="tablapie"><?php echo format_number($subtotal['f2']); ?></td>
			<td align="right" class="tablapie"><?php echo format_number($subtotal['f3']); ?></td>
			<td align="right" class="tablapie"><?php echo format_number($subtotal['f4']); ?></td>
			<td align="right" class="tablapie"><?php echo format_number($total); ?></td>
		</tr>
		<tr>
			<td class="tablapie">&nbsp;</td>
			<td align="right" class="tablapie"><?php echo format_percent(100*$subtotal['f1']/$total); ?></td>
			<td align="right" class="tablapie"><?php echo format_percent(100*$subtotal['f2']/$total); ?></td>
			<td align="right" class="tablapie"><?php echo format_percent(100*$subtotal['f3']/$total); ?></td>
			<td align="right" class="tablapie"><?php echo format_percent(100*$subtotal['f4']/$total); ?></td>
			<td class="tablapie">&nbsp;</td>
		</tr>
		<?php 
			$super['f1'] += $subtotal['f1']; 
			$super['f2'] += $subtotal['f2']; 
			$super['f3'] += $subtotal['f3'];
			$super['f4'] += $subtotal['f4'];
		?>
	<?php endforeach; ?>
		<?php $total = $super['f1'] + $super['f2'] + $super['f3'] + $super['f4']; ?>
	<tr  class="HeaderStyle">
		<td class="FooterStyle"><?php echo $this->lang->line('Total'); ?></td>
		<td align="right" class="FooterStyle"><?php echo format_number($super['f1']); ?></td>
		<td align="right" class="FooterStyle"><?php echo format_number($super['f2']); ?></td>
		<td align="right" class="FooterStyle"><?php echo format_number($super['f3']); ?></td>
		<td align="right" class="FooterStyle"><?php echo format_number($super['f4']); ?></td>
		<td align="right" class="FooterStyle"><?php echo format_number($total); ?></td>
	</tr>
		<tr>
		<td class="FooterStyle">&nbsp;</td>
		<td align="right" class="FooterStyle"><?php echo format_percent(100*$super['f1']/$total); ?></td>
		<td align="right" class="FooterStyle"><?php echo format_percent(100*$super['f2']/$total); ?></td>
		<td align="right" class="FooterStyle"><?php echo format_percent(100*$super['f3']/$total); ?></td>
		<td align="right" class="FooterStyle"><?php echo format_percent(100*$super['f4']/$total); ?></td>
		<td class="FooterStyle">&nbsp;</td>
		</tr>
</table>
