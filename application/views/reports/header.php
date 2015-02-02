<div id="header"><?php if ($borrador):?> <?php echo $this->lang->line('report-#BORRADOR#');?>&nbsp;<?php endif;?><?php echo $titulo;?></div>
<?php if($logo):?>
<div id="identity">
<?php if ($this->config->item('codebar.documents')):?>
<div id="codebar">
<img
	src="<?php echo site_url('sys/codebar/out/' . (isset($nIdCode)?$nIdCode:$nIdDocumento));?>" />
</div>
<?php endif; ?>	
	<div id="logo">
<img
	src="<?php echo image_asset_url($this->config->item('company.logo.print'));?>" /></div>

<div id="address"><?php echo $this->config->item('company.name');?><br />
<?php echo ($this->config->item('company.address.1')!='')?$this->config->item('company.address.1') . '<br/>':'';?>
<?php echo ($this->config->item('company.address.2')!='')?$this->config->item('company.address.2') . '<br/>':'';?>
<?php echo ($this->config->item('company.address.3')!='')?$this->config->item('company.address.3') . '<br/>':'';?>
<?php echo $this->lang->line('report-NIF');?>: <?php echo $this->config->item('company.vat');?>
</div>
</div>
<?php endif; ?>
<div style="clear: both"></div>
<?php if($email && isset($texto_email) && ($texto_email != '')):?>
<div><?php echo $texto_email;?><br />
<br />
</div>
<div style="clear: both"></div>
<?php endif; ?>
<div id="customer">
<div id="customer-title">
<?php if (isset($extra_name)):?>
	<span id="extra_meta"><?php echo $extra_name; ?></span>
<?php endif; ?>
<?php if (!isset($direccion) || (isset($direccion) && (/*!isset($direccion['cTitular']) || */trim($direccion['cTitular'])==''))):?>
	<?php echo wordwrap(str_replace("\n", '<br />', format_name($cliente['cNombre'], $cliente['cApellido'], $cliente['cEmpresa'], TRUE)), $clientelen, '<br/>');?><br />
<?php endif;?>	
<?php if (isset($direccion)) echo format_address_print($direccion);?> <?php if (isset($cliente['cNIF']) && ($cliente['cNIF'] != '') && ($cliente['cNIF'] != '0')):?>
<br />
<?php echo $this->lang->line('report-NIF');?>: <?php echo $cliente['cNIF'];?>
<?php endif;?></div>
<table id="meta">
	<tr>
		<td class="meta-head"><?php echo $this->lang->line('report-Número');?></td>
		<td><?php echo $nIdDocumento;?></td>
	</tr>
	<tr>

		<td class="meta-head"><?php echo $this->lang->line('report-Fecha');?></td>
		<td>
		<div id="date"><?php echo format_date($dCreacion);?></div>
		</td>
	</tr>
	<tr>
		<td class="meta-head"><?php echo $clpv;?></td>
		<td>
		<div class="due"><?php echo $nIdCliente;?></div>
		</td>
	</tr>
	<tr>
		<td class="meta-head"><?php echo $this->lang->line('report-Referencia');?></td>
		<td>
		<div class="due"><?php echo $cRefCliente;?></div>
		</td>
	</tr>
</table>
</div>

<div style="clear: both"></div>
<?php if (isset($extra_head)):?>
	<?php echo $extra_head; ?>
<div style="clear: both"></div>
<?php endif; ?>
