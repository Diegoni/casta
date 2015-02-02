<table summary="<?php echo $this->lang->line('Cambio divisa');?>">
	<caption><?php echo $this->lang->line('Cambio divisa');?> </caption>
	<tbody>
		<tr>
			<th colspan="2"><?php echo $divisa1;?> -&gt; <?php echo $divisa2;?></th>
		</tr>
		<tr>
			<td scope="row" id="_co2" nowrap="nowrap"><?php echo sprintf($this->lang->line('divisa_importe_orignal'), $divisa1);?>
			</td>
			<td align="right"><?php echo format_number($importe1);?></td>
		</tr>
		<tr>
			<td scope="row" id="_co2" nowrap="nowrap"><?php echo $this->lang->line('Cambio');?>
			</td>
			<td align="right"><?php echo format_number($cambio);?></td>
		</tr>
		<tr>
			<td scope="row" id="_co2" nowrap="nowrap"><?php echo sprintf($this->lang->line('divisa_importe_cambio'), $divisa2);?>
			</td>
			<td align="right"><?php echo format_number($importe2);?></td>
		</tr>
	</tbody>
</table>

