<?php $this->load->helper('asset');?>
<?php $this->load->library('HtmlFile'); ?>
<?php echo get_instance()->htmlfile->orientation(ORIENTATION_LANDSCAPE); ?>
<?php echo css_asset('calendar.css', 'main');?>
<TABLE border="1" bordercolor="#000000" cellspacing="0" cellpadding="3"
	style="BORDER-TOP-WIDTH: 0px; BORDER-LEFT-WIDTH: 0px; BORDER-BOTTOM-WIDTH: 0px; BORDER-COLLAPSE: collapse; BORDER-RIGHT-WIDTH: 0px">
	<tr class="HeaderRow">
		<th colspan="3" class="HeaderRow"><?php echo $this->lang->line('Día'); ?></th>
		<?php foreach($grupos as $g): ?>
		<th class="HeaderRow" id="<?php echo $this->lang->line($g); ?>_c"><?php echo $this->lang->line($g); ?></th>
		<?php endforeach; ?>
	</tr>

	<?php $i = 0;?>
	<?php $par = false; ?>
	<?php foreach($valores as $dia => $valor):?>
	<?php
	$ndia = to_date($dia);
	$d = getdate($ndia);
	?>
	<tr>
		<td rowSpan="2" class="mes<?php echo ($d['mon']& 1)?'2':'1';?>"><?php echo $this->lang->line($d['month']); ?></td>
		<td rowSpan="2" class="dia<?php echo $d['wday'];?>"><?php echo $d['mday']; ?></td>
		<td rowSpan="2" class="dia<?php echo $d['wday'];?>"><?php echo $this->lang->line($d['weekday']); ?></td>
		<?php $cab = false; ?>
		<?php foreach($grupos as $g): ?>
		<?php if (!$cab):?>
		<?php $cab = true; ?>
		<?php endif;?>
		<td nowrap="nowrap" class="normal<?php echo $d['wday'];?>" id="<?php echo $this->lang->line($g); ?>_<?php echo $i;?>">
			<?php 
			if (isset($valor[$g]))
			{
				$usrs = array();
				foreach($valor[$g]['manana'] as $u)
				{
					if (isset($u['cNombre']))
						$usrs[] = $u['cNombre'];
				}
				echo implode('<br/>', $usrs);
			}
			?>
		</td>
		<?php endforeach; ?>
	</tr>
	<tr>
	<?php foreach($grupos as $g): ?>
		<td nowrap="nowrap" class="normal<?php echo $d['wday'];?>t" id="<?php echo $this->lang->line($g); ?>_<?php echo $i;?>_t">
			<?php 
			if (isset($valor[$g]))
			{
				$usrs = array();
				foreach($valor[$g]['tarde'] as $u)
				{
					if (isset($u['cNombre']))
						$usrs[] = $u['cNombre'];
				}
				echo implode('<br/>', $usrs);
			}
			?>
		</td>
	<?php endforeach; ?>
	</tr>

	<?php $i++; ?>
	<?php $par = !$par;?>
	<?php endforeach; ?>
</TABLE>
