<h2><?php echo $this->lang->line(($obras)?'Con Obras':'Sin Obras')?> / <?php echo $this->lang->line(($activas)?'Solo Activas':'Activas y Canceladas')?></h2>
<table border="0" bordercolor="#000000" cellspacing="0" cellpadding="3"
	class="SummaryDataGrid"
	style="BORDER-TOP-WIDTH: 0px; BORDER-LEFT-WIDTH: 0px; BORDER-BOTTOM-WIDTH: 0px; BORDER-COLLAPSE: collapse; BORDER-RIGHT-WIDTH: 0px">
	<tr class="HeaderStyle">
		<td align="center" class="HeaderStyle"><?php echo $this->lang->line('Id'); ?></td>
		<td colspan="6" align="left" class="HeaderStyle"><?php echo $this->lang->line('Cliente'); ?></td>
		<td align="center" class="HeaderStyle"><?php echo $this->lang->line('NIF'); ?></td>
	</tr>
	<?php
	$activas0 = $canceladas0 = 0;
	$precio0 = $coste0 = 0;
	?>

	<?php foreach($clientes as $cliente):?>
	<tr>
		<td class="CategoryHeaderHier"><?php echo format_enlace_cmd($cliente['cliente']['id'], site_url('clientes/cliente/index/' . $cliente['cliente']['id']));?>
		</td>
		<td colspan="6" class="CategoryHeaderHier"><?php echo $cliente['cliente']['Nombre']; ?></td>
		<td class="CategoryHeaderHier"><?php echo $cliente['cliente']['nif']; ?></td>
	</tr>
	<tr>
		<td class="tablaimpar">&nbsp;</td>
		<td class="tablaimpar" align="left"><?php echo $this->lang->line('Id'); ?>
		</td>
		<td class="tablaimpar" align="left"><?php echo $this->lang->line('Título'); ?></td>
		<td class="tablaimpar" align="left"><?php echo $this->lang->line('Tipo'); ?></td>
		<td class="tablaimpar" align="right"><?php echo $this->lang->line('fCoste'); ?></td>
		<td class="tablaimpar" align="right"><?php echo $this->lang->line('fPrecio'); ?></td>
		<td class="tablaimpar" align="right"><?php echo $this->lang->line('Renovación'); ?></td>
		<td class="tablaimpar" align="left"><?php echo $this->lang->line('Estado'); ?></td>
	</tr>

	<?php
	$activas = $canceladas = 0;
	$precio = $coste = 0;
	?>
	<?php foreach($cliente['suscripciones'] as $suscripcion):?>
	<tr>
		<td class="tablapie">&nbsp;</td>
		<td align="left"><?php echo format_enlace_cmd($suscripcion['id'], site_url('suscripciones/suscripcion/index/' . $suscripcion['id']));?>
		</td>
		<td align="left"><?php echo format_enlace_cmd($suscripcion['revista_id'], site_url('catalogo/articulo/index/' . $suscripcion['revista_id']));?>
		- <?php echo $suscripcion['revista']; ?></td>
		<td align="left"><?php echo $suscripcion['tipo']; ?></td>
		<td align="right"><?php echo format_price($suscripcion['coste']); ?></td>
		<td align="right"><?php echo format_price($suscripcion['precio']); ?></td>
		<td align="right"><?php echo format_date($suscripcion['renovacion']); ?></td>
		<td align="left"><?php echo $this->lang->line((!$suscripcion['activa'])?'Cancelada':'Activa'); ?></td>
	</tr>
	<?php
	($suscripcion['activa'])?++$activas:++$canceladas;
	$precio += $suscripcion['precio'];
	$coste+= $suscripcion['coste'];
	($suscripcion['activa'])?++$activas0:++$canceladas0;
	$precio0 += $suscripcion['precio'];
	$coste0 += $suscripcion['coste'];
	?>
	<?php endforeach;?>
	<tr>
		<td class="SelectedBold">&nbsp;</td>
		<td class="SelectedBold" align="left" colspan="3"><?php echo $canceladas + $activas; ?>
		<?php echo $this->lang->line('suscripciones'); ?></td>
		<td class="SelectedBold" align="right"><?php echo format_price($coste); ?></td>
		<td class="SelectedBold" align="right"><?php echo format_price($precio); ?></td>
		<td class="SelectedBold" align="left" colspan="2"><?php echo sprintf($this->lang->line('suscripciones-estado'), $activas, $canceladas); ?></td>
	</tr>
	<?php endforeach;?>
	<tr>
		<td class="FooterStyle">&nbsp;</td>
		<td class="FooterStyle" align="left" colspan="3"><?php echo $canceladas0 + $activas0; ?>
		<?php echo $this->lang->line('suscripciones'); ?></td>
		<td class="FooterStyle" align="right"><?php echo format_price($coste0); ?></td>
		<td class="FooterStyle" align="right"><?php echo format_price($precio0); ?></td>
		<td class="FooterStyle" align="left" colspan="2"><?php echo sprintf($this->lang->line('suscripciones-estado'), $activas0, $canceladas0); ?></td>
	</tr>
</table>
