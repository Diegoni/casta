<h1><?php echo $fecha1; ?> &lt;-> <?php echo $fecha2; ?></h1>
<TABLE border="0" bordercolor="#000000" cellspacing="0" cellpadding="3"
	class="SummaryDataGrid"
	style="BORDER-TOP-WIDTH: 0px; BORDER-LEFT-WIDTH: 0px; BORDER-BOTTOM-WIDTH: 0px; BORDER-COLLAPSE: collapse; BORDER-RIGHT-WIDTH: 0px">

	<?php $cajas = array(); ?>
	<?php $total = array(); ?>
	<?php $acumulado = array();?>
	<?php foreach($valores['data'] as $dia => $valor):?>
	<TR>
		<TD colspan="<?php echo count($valores['modos']) + 2;?>"
			class="CategoryHeaderHier"><?php echo format_date($dia); ?></TD>
	</TR>
	<TR class="HeaderStyle">
		<TD align="center" class="HeaderStyle"><?php echo $this->lang->line('Caja'); ?></TD>
		<?php foreach($valores['modos'] as $modo => $shit): ?>
		<TD align="center" class="HeaderStyle"><?php echo $this->lang->line($modo .'-short'); ?></TD>
		<?php endforeach; ?>
		<TD align="center" class="HeaderStyle"><?php echo $this->lang->line('Total'); ?></TD>
	</TR>
	<?php $total_dia = array(); ?>

	<?php foreach($valor['cajas'] as $caja => $shit):?>
	<TR>
		<TD nowrap="true" class="CategoryHeader3"><?php echo $caja; ?></TD>
		<?php $subtotal = 0; ?>
		<?php foreach($valores['modos'] as $modo => $shit): ?>
		<?php $importe = (isset($valor['data'][$modo][$caja]))?$valor['data'][$modo][$caja]:0; ?>
		<?php $acumulado[$modo][$caja] = $importe + (isset($acumulado[$modo][$caja])?$acumulado[$modo][$caja]:0); ?>
		<?php $cajas[$caja] = $caja;?>
		<TD class="tablaimparright">
				<?php echo format_enlace_cmd(format_price($importe), site_url('oltp/oltp/caja_dia_modo/' .
		str_replace('/', '-', $dia) . '/' . str_replace('/', '-', $dia)
		. '/' . $valores['cajas'][$caja] 
		. '/' . $valores['modos'][$modo]));?>		
		</TD>
		<?php $total_dia[$modo] = (isset($total_dia[$modo])? $total_dia[$modo]:0) + $importe; ?>
		<?php $total[$modo] = (isset($total[$modo])?$total[$modo]:0) + $importe; ?>
		<?php $subtotal += $importe; ?>
		<?php endforeach; ?>
		<TD align="right" class="SelectedBold"><?php echo format_price($subtotal); ?></TD>
	</TR>
	<?php endforeach; ?>

	<TR>
		<TD class="tablapie">&nbsp;</TD>
		<?php $subtotal = 0; ?>
		<?php foreach($valores['modos'] as $modo => $shit): ?>
		<TD align="right" class="tablapie"><?php echo format_price($total_dia[$modo]); ?></TD>
		<?php $subtotal += $total_dia[$modo]; ?>
		<?php endforeach; ?>
		<TD align="right" class="tablapie"><?php echo format_price($subtotal); ?></TD>
	</TR>
	<?php endforeach; ?>
	<?php if (count($valores['data'])>1):?>
	<TR>
		<TD colspan="<?php echo count($valores['modos']) + 2;?>"
			class="CategoryHeaderHier"><?php echo $this->lang->line('Total'); ?></TD>
	</TR>

	<?php $total_dia = array(); ?>
	<?php foreach($cajas as $caja => $shit):?>
	<TR>
		<TD nowrap="true" class="CategoryHeader3"><?php echo $caja; ?></TD>
		<?php $subtotal = 0; ?>
		<?php foreach($valores['modos'] as $modo => $shit): ?>
		<?php $importe = (isset($acumulado[$modo][$caja]))?$acumulado[$modo][$caja]:0; ?>
		<TD class="tablaimparright">
		<?php echo format_enlace_cmd(format_price($importe), site_url('oltp/oltp/caja_dia_modo/' .
		str_replace('/', '-', $fecha1) . '/' . str_replace('/', '-', $fecha2)
		. '/' . $valores['cajas'][$caja] 
		. '/' . $valores['modos'][$modo]));?>
		</TD>
		<?php $total_dia[$modo] = (isset($total_dia[$modo])? $total_dia[$modo]:0) + $importe; ?>
		<?php $subtotal += $importe; ?>
		<?php endforeach; ?>
		<TD align="right" class="SelectedBold"><?php echo format_price($subtotal); ?></TD>
	</TR>
	<?php endforeach; ?>

	<TR class="HeaderStyle">
		<TD class="FooterStyle">&nbsp;</TD>
		<?php $subtotal = 0; ?>
		<?php foreach($valores['modos'] as $modo => $shit): ?>
		<TD align="right" class="FooterStyle"><?php echo format_price($total[$modo]); ?></TD>
		<?php $subtotal += $total[$modo]; ?>
		<?php endforeach; ?>
		<TD align="right" class="FooterStyle"><?php echo format_price($subtotal); ?></TD>
	</TR>
	<?php endif;?>
</TABLE>
