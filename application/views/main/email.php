<?php 
$this->load->helper('asset');
$obj = get_instance();
$obj->load->library('Configurator');
$obj->load->library('Userauth');
$email = $obj->configurator->user('bp.email.from');
if (empty($email)) $email = $this->config->item('company.email');?>
<div id="page-wrap">
	<div style="clear: both"></div>
	<p>
		<?php echo $texto_email;?>
	</p>
	<div style="clear: both"></div>
	<div>
		<strong><?php echo $obj->userauth->get_name();?></strong><br/>
		<?php echo $this->config->item('company.name');?>
		<br />
		<hr />
		<div id="logo">
			<img
			src="<?php echo image_asset_url($this->config->item('company.logo.print'));?>" />
		</div>
		<div class="small">
			<?php echo $this->lang->line('NIF');?>:
			<?php echo $this->config->item('company.vat');?><br />
			<br />
			<?php echo ($this->config->item('company.address.1')!='')?$this->config->item('company.address.1') . '<br/>':'';
			?>
			<?php echo ($this->config->item('company.address.2')!='')?$this->config->item('company.address.2') . '<br/>':'';
			?>
			<?php echo ($this->config->item('company.address.3')!='')?$this->config->item('company.address.3') . '<br/>':'';
			?>
			<br />
			<?php echo $this->lang->line('Tel.');?>:
			<?php echo $this->config->item('company.telephone');?><br />
			<?php echo $this->lang->line('Fax.');?>:
			<?php echo $this->config->item('company.fax');?><br />
			<?php echo $this->lang->line('eMail');?>:
			<a href="email:<?php $email;?>"><?php echo $email;?></a>
			<br />
			<?php echo $this->lang->line('Web');?>:
			<a href="<?php echo $this->config->item('company.url');?>"><?php echo $this->config->item('company.url');?></a>
			<br />
		</div>
		<hr />
	</div>
