<?php $this->load->helper('asset');?>
<div id="page-wrap">
	<div id="header">
		<?php if ($borrador):
		?> <?php echo $this->lang->line('report-#BORRADOR#');?>&
		nbsp;<?php endif;?><?php echo $titulo;?>
	</div>
	<div style="clear: both"></div>
	<p>
		<?php echo $texto_email;?>
	</p>
	<div style="clear: both"></div>
	<div>
		<strong><?php echo $this->config->item('company.name');?></strong>
		<br />
		<hr />
		<div id="logo">
			<img
			src="<?php echo image_asset_url($this->config->item('company.logo.print'));?>" />
		</div>
		<div class="small">
			<?php echo $this->lang->line('report-NIF');?>:
			<?php echo $this->config->item('company.vat');?><br />
			<br />
			<?php echo ($this->config->item('company.address.1')!='')?$this->config->item('company.address.1') . '<br/>':'';
			?>
			<?php echo ($this->config->item('company.address.2')!='')?$this->config->item('company.address.2') . '<br/>':'';
			?>
			<?php echo ($this->config->item('company.address.3')!='')?$this->config->item('company.address.3') . '<br/>':'';
			?>
			<br />
			<?php echo $this->lang->line('report-Tel.');?>:
			<?php echo $this->config->item('company.telephone');?><br />
			<?php echo $this->lang->line('report-Fax.');?>:
			<?php echo $this->config->item('company.fax');?><br />
			<?php echo $this->lang->line('report-eMail');?>:
			<a href="email:<?php echo $this->config->item('company.email');?>"><?php echo $this->config->item('company.email');?></a>
			<br />
			<?php echo $this->lang->line('report-Web');?>:
			<a href="<?php echo $this->config->item('company.url');?>"><?php echo $this->config->item('company.url');?></a>
			<br />
		</div>
		<hr />
	</div>
</div>
