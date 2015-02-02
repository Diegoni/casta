<?php $this->load->library('HtmlFile'); ?>
<?php #echo get_instance()->htmlfile->orientation(ORIENTATION_LANDSCAPE); ?>

<?php $libros = 0; $lineas = 0;?>

<?php foreach($pedidos as $pedido):?>
<div style='page-break-after: always;'><?php $odd = FALSE;?>

<TABLE border="0" bordercolor="#000000" cellspacing="0" cellpadding="3"
	class="SummaryDataGrid" width="100%"
	style="BORDER-TOP-WIDTH: 0px; BORDER-LEFT-WIDTH: 0px; BORDER-BOTTOM-WIDTH: 0px; BORDER-COLLAPSE: collapse; BORDER-RIGHT-WIDTH: 0px">

	<TR class="HeaderStyle">
		<th class="HeaderStyle"><?php echo $this->lang->line('Id');?></th>
		<th class="HeaderStyle"><?php echo $this->lang->line('Título');?></th>
		<th class="HeaderStyle"><?php echo $this->lang->line('Autores');?></th>
		<th class="HeaderStyle"><?php echo $this->lang->line('Cantidad');?></th>
		<th class="HeaderStyle"><?php echo $this->lang->line('Descuento');?></th>
		<th class="HeaderStyle"><?php echo $this->lang->line('Importe');?></th>
	</TR>
	<TR class="CategoryHeaderHier">
		<TD class="CategoryHeaderHier" colspan="12" nowrap="nowrap">
			<?php if (isset($pedido['id'])):?>
			<img src="<?php echo site_url('sys/codebar/out/' . $pedido['id']);?>" />
			<?php endif;?>
			[<?php echo isset($pedido['id'])?$pedido['id']:'-';?>]
		<?php echo $pedido['nIdProveedor'];?> - <?php echo $pedido['cProveedor'];?>
		<strong>(<?php echo $pedido['bDeposito']?$this->lang->line('Depósito'):$this->lang->line('Firme');?>)</strong></TD>
	</TR>
	<?php $cantidad = 0; ?>
	<?php foreach($pedido['lineas'] as $linea):?>
	<TR <?php if ($odd):?> class="alt" <?php endif;?>>
		<TD class="Line1" width="1%" nowrap="nowrap"><?php echo $linea['nIdLibro']; ?>
		<!--<img src="<?php echo site_url('sys/codebar/out/' . $linea['nIdLibro']);?>" />-->
		<?php echo format_cover($linea['nIdLibro'], $this->config->item('bp.catalogo.cover.small'));?>
		</TD>
		<TD class="Line1"><?php echo $linea['cTitulo'];?></TD>
		<TD class="Line1"><?php echo $linea['cAutores'];?></TD>
		<TD width="1%" align="right" class="Line1"><?php echo format_number($linea['nCantidad']);?></TD>
		<TD width="1%" align="center" class="Line1"><?php echo format_percent($linea['fDescuento']);?></TD>
		<TD width="1%" align="right" class="Line1"><?php echo format_price($linea['fPrecio']);?></TD>
	</TR>
	<?php $cantidad += $linea['nCantidad']; ?>
	<?php $odd = !$odd;?>
	<?php endforeach; ?>
	<TR class="CategoryHeader">
		<TD class="CategoryHeader" colspan="3"></TD>
		<TD class="CategoryHeader" align="right"><?php echo format_number($cantidad);?></TD>
		<TD class="CategoryHeader" colspan="2"></TD>
	</TR>
	<?php $libros += $cantidad; $lineas += count($pedido['lineas']);?>
</TABLE>
</div>
	<?php endforeach; ?>
<TABLE border="0" bordercolor="#000000" cellspacing="0" cellpadding="3"
	class="SummaryDataGrid"
	style="BORDER-TOP-WIDTH: 0px; BORDER-LEFT-WIDTH: 0px; BORDER-BOTTOM-WIDTH: 0px; BORDER-COLLAPSE: collapse; BORDER-RIGHT-WIDTH: 0px">

	<TR class="CategoryHeader">
		<TD class="FooterStyle" colspan="12"><?php echo $this->lang->line('Devoluciones');?>:
		<?php echo format_number(count($pedidos));?> <?php echo $this->lang->line('Títulos');?>:
		<?php echo format_number($lineas);?> <?php echo $this->lang->line('Unidades');?>:
		<?php echo format_number($libros);?></TD>
	</TR>
</TABLE>
