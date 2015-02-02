<?php $max = min($this->config->item('max.excel.preview'), $data['numRows']);?>
<div class="details-panel">
	<table>
	<tr>
		<?php for($i = 0; $i < $data['numCols']; $i++):
		?>
		<th>
		<?php echo $i;?>
		</th>
		<?php endfor;?>
	</tr>
	<?php for($j = 0; $j < $max; $j++):
	?>
	<tr>
		<?php for($i = 0; $i < $data['numCols']; $i++):
		?>
		<td class="info">
		<?php echo $data['cells'][$j][$i];?>
		</td>
		<?php endfor;?>
	</tr>

	<?php endfor;?>
	<?php if ($max < $data['numRows']):
	?>
	<tr>
		<td colspan="<?php echo $data['numCols'];?>" align="left">....	
		</td>
	</tr>
	<tr>
		<td colspan="<?php echo $data['numCols'];?>" align="right">
		<?php echo $data['numRows'];?>
		<?php echo $this->lang->line('registros');?>
		</td>
	</tr>
	<?php endif;?>
</table>
</div>