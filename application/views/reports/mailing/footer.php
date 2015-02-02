<?php $this->load->helper('asset');?>
<div class="nopagebreak">
<table class="fonsgris" cellspacing="0" cellpadding="5" width="100%"
	border="0">
	<tbody>
		<tr>
			<td valign="top">
			<table cellspacing="0" cellpadding="0" width="100%" border="0">
				<tbody>
					<tr>
						<td class="caixaresuminf" align="center" colspan="4"></td>
					</tr>
					<tr>
						<td class="taula_idiomas" align="center" colspan="4">
						<div class="c1"><?php echo $this->config->item('company.name');?><br />
						<?php echo $this->config->item('company.address.1');?> <?php echo $this->config->item('company.address.2');?>
						<?php echo $this->config->item('company.address.3');?><br />
						<?php echo $this->lang->line('report-tel-short');?> <?php echo $this->config->item('company.telephone');?>
						<?php echo $this->lang->line('report-fax-short');?> <?php echo $this->config->item('company.fax');?><br />
						<table align="center">
						<tr>
						<td><?php echo image_asset('social/16px/home.png')?></td>
						<td><a 
							href="<?php echo $this->config->item('company.url');?>"><div style="color:white;"><?php echo $this->config->item('company.url');?></div></a></td>
						</tr>
						</table>
						<table align="center">
						<tr>
						<td><?php echo image_asset('social/16px/email.png')?></td>
						<td><a 
							href="mailto:<?php echo $this->config->item('company.email');?>"><div style="color:white;"><?php echo $this->config->item('company.email');?></div></a></td>
						</tr>
						</table>
						<table align="center">
						<tr>
						<td><?php echo image_asset('social/16px/facebook.png')?></td>
						<td><a 
							href="<?php echo $this->config->item('company.facebook');?>"><div style="color:white;"><?php echo $this->config->item('company.facebook');?></div></a></td>
						</tr>
						</table>
						<table align="center">
						<tr>
						<td><?php echo image_asset('social/16px/twitter.png')?></td>
						<td><a 
							href="<?php echo $this->config->item('company.twitter');?>"><div style="color:white;"><?php echo $this->config->item('company.twitter');?></div></a></td>
						</tr>
						</table>
						</div>
						</td>
					</tr>
					<tr>
						<td class="caixaresuminf" align="center" height="3"><img
							src="http://www.alibri.es/templates/alibri/images/transp_5h.gif"
							width="7" /></td>
					</tr>
					<tr>
						<td align="center"><?php echo $this->lang->line('report-text-delete'); ?>
						</td>
					</tr>
				</tbody>
			</table>
			</td>
		</tr>
	</tbody>
</table>
</div>
