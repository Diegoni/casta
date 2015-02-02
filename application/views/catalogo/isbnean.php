<table
	summary="<?php echo $this->lang->line('ISBN/EAN');?> <?php echo $code;?>">
	<caption><?php echo $this->lang->line('ISBN/EAN');?> <?php echo $code;?>
	</caption>
	<thead>
		<tr>
			<th colspan="2" scope="col"><?php echo $code;?></th>
		</tr>
	</thead>
	<tbody>
		<tr class="alt">
			<td scope="row" id="_co" nowrap="nowrap"><?php echo $this->lang->line('Es EAN');?>
			</td>
			<td align="right"><?php echo $is_ean;?></td>
		</tr>
		<tr>
			<td scope="row" id="_co" nowrap="nowrap"><?php echo $this->lang->line('Es ISBN10');?>
			</td>
			<td align="right"><?php echo $is_isbn10;?></td>
		</tr>
		<tr class="alt">
			<td scope="row" id="_co" nowrap="nowrap"><?php echo $this->lang->line('Es ISBN13');?>
			</td>
			<td align="right"><?php echo $is_isbn13;?></td>
		</tr>
		<tr>
			<td scope="row" id="_co" nowrap="nowrap"><?php echo $this->lang->line('ISBN10');?>
			</td>
			<td align="right"><?php echo $isbn10;?></td>
		</tr>
		<tr class="alt">
			<td scope="row" id="_co" nowrap="nowrap"><?php echo $this->lang->line('ISBN13');?>
			</td>
			<td align="right"><?php echo $isbn13;?></td>
		</tr>
		<tr>
			<td scope="row" id="_co" nowrap="nowrap"><?php echo $this->lang->line('EAN');?>
			</td>
			<td align="right"><?php echo $ean;?></td>
		</tr>
		<tr class="alt">
			<td scope="row" id="_co" nowrap="nowrap"><?php echo $this->lang->line('ISBN10') . ' =&gt; ' . $this->lang->line('EAN');?>
			</td>
			<td align="right"><?php echo $ean1;?></td>
		</tr>
		<tr>
			<td scope="row" id="_co" nowrap="nowrap"><?php echo $this->lang->line('ISBN13') . ' =&gt; ' . $this->lang->line('EAN');?>
			</td>
			<td align="right"><?php echo $ean2;?></td>
		</tr>
		<?php if (isset($parts)):?>
		<tr class="alt">
			<td scope="row" id="_co" nowrap="nowrap"><?php echo $this->lang->line('Editorial');?>
			</td>
			<td align="right"><?php echo $parts['publisher_id'];?></td>
		</tr>
		<?php endif;?>
		<?php if (isset($editorial)):?>
		<tr>
			<td scope="row" id="_co" nowrap="nowrap"><?php echo $this->lang->line('Editorial');?>
			</td>
			<td align="right"><?php echo $editorial;?></td>
		</tr>
		<?php endif;?>
		<?php if (isset($proveedor)):?>
		<tr class="alt">
			<td scope="row" id="_co" nowrap="nowrap"><?php echo $this->lang->line('Proveedor');?>
			</td>
			<td align="right"><?php echo $proveedor;?></td>
		</tr>
		<?php endif;?>
	</tbody>
</table>
