<table>
	<tr>
		<th colspan="2"><?php echo $this->lang->line('Usuario');?>: <?php echo $user_name;?></th>
	</tr>
	<?php if (isset($user)):?>
	<?php foreach ($user as $k => $v):?>
	<tr>
		<td><?php echo $k;?></td>
		<td><?php echo $v;?></td>
	</tr>
	<?php endforeach;?>
	<?php endif;?>
	<tr>
		<th colspan="2"><?php echo $this->lang->line('Terminal');?>: <?php echo $terminal_name;?></th>
	</tr>
	<?php if (isset($terminal)):?>
	<?php foreach ($terminal as $k => $v):?>
	<tr>
		<td><?php echo $k;?></td>
		<td><?php echo $v;?></td>
	</tr>
	<?php endforeach;?>
	<?php endif;?>
	<tr>
		<th colspan="2"><?php echo $this->lang->line('Sistema');?></th>
	</tr>
	<?php if (isset($system)):?>
	<?php foreach ($system as $k => $v):?>
	<tr>
		<td><?php echo $k;?></td>
		<td><?php echo $v;?></td>
	</tr>
	<?php endforeach;?>
	<?php endif;?>
</table>
