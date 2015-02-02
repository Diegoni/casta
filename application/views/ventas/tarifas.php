<?php $this->load->helper('extjs');?>
<?php $this->load->helper('asset');?>
<div class="details-panel"><table summary="<?php echo $this->lang->line('Cálculo Tarifas');?>">
	<tbody>
		<tr>
			<td style="font-size: 14px;" class="label" scope="row" id="_co2" nowrap="nowrap"><?php echo sprintf($this->lang->line('divisa_importe_orignal'), $divisa);?>
			</td>
			<td style="font-size: 14px;" class="number" align="right"><?php echo format_number($importe_divisa);?></td>
		</tr>
		<tr>
			<td style="font-size: 14px;" class="label" scope="row" id="_co2" nowrap="nowrap"><?php echo $this->lang->line('Cambio');?>
			</td>
			<td style="font-size: 14px;" class="number" align="right"><?php echo format_number($cambio);?></td>
		</tr>
		<tr>
			<td style="font-size: 14px;" class="label" scope="row" id="_co2" nowrap="nowrap"><?php echo $this->lang->line('Margen moneda');?>
			</td>
			<td style="font-size: 14px;" class="number" align="right"><?php echo format_percent($margen_moneda);?></td>
		</tr>
		<tr>
			<td style="font-size: 14px;" class="label" scope="row" id="_co2" nowrap="nowrap"><?php echo sprintf($this->lang->line('divisa_importe_cambio'), $divisa2);?>
			</td>
			<td style="font-size: 14px;" class="number" align="right"><?php echo format_price($importe);?></td>
		</tr>
		<tr>
			<td style="font-size: 14px;" class="label" scope="row" id="_co2" nowrap="nowrap"><?php echo $this->lang->line('Portes');?>
			</td>
			<td style="font-size: 14px;" class="number" align="right"><?php echo format_number($portes);?></td>
		</tr>
		<tr>
			<td style="font-size: 14px;" class="label" scope="row" id="_co2" nowrap="nowrap"><?php echo $this->lang->line('IVA');?>
			</td>
			<td style="font-size: 14px;" class="number" align="right"><?php echo format_percent($iva);?></td>
		</tr>
		<tr>
			<td style="font-size: 14px;" class="label" scope="row" id="_co2" nowrap="nowrap"><?php echo $this->lang->line('Dto. Proveedor');?>
			</td>
			<td style="font-size: 14px;" class="number" align="right"><?php echo format_percent($dto);?></td>
		</tr>
	</tbody>
</table>

<table summary="<?php echo $this->lang->line('Venta');?>">
	<thead>
		<tr style="font-size: 14px;" class="label">
			<th><?php echo $this->lang->line('Tarifa');?></th>
			<th><?php echo $this->lang->line('Margen');?></th>
			<th><?php echo $this->lang->line('Portes?');?></th>
			<th><?php echo $this->lang->line('Dto?');?></th>
			<th><?php echo $this->lang->line('Base');?></th>
			<th><?php echo $this->lang->line('Con Dto');?></th>
			<th><?php echo $this->lang->line('Con Margen');?></th>
			<th><?php echo $this->lang->line('Con Portes');?></th>
			<th><?php echo $this->lang->line('fPrecio');?></th>
			<th><?php echo $this->lang->line('PVP');?></th>
		</tr>
	</thead>
	<tbody>
	<?php $odd = FALSE;?>
	<?php foreach ($tarifas as $t):?>
		<tr style="font-size: 14px;" <?php if ($odd):?> class="alt" <?php endif;?>>
			<td style="font-size: 14px;" class="label" scope="row" id="_co2" nowrap="nowrap"><?php echo $t['text'];?></td>
			<td style="font-size: 14px;" class="number" align="right"><?php echo format_percent($t['margen']);?></td>
			<td style="font-size: 14px;" class="number" align="center"><?php echo $this->lang->line(($t['portes']==1)?'si':'no');?></td>
			<td style="font-size: 14px;" class="number" align="center"><?php echo $this->lang->line(($t['dto']==1)?'si':'no');?></td>
			<td style="font-size: 14px;" class="number" align="right"><?php echo format_price($t['base']);?></td>
			<td style="font-size: 14px;" class="number" align="right"><?php echo format_price($t['base_dto']);?></td>
			<td style="font-size: 14px;" class="number" align="right"><?php echo format_price($t['base_margen']);?></td>
			<td style="font-size: 14px;" class="number" align="right"><?php echo format_price($t['base_portes']);?></td>
			<td style="font-size: 14px;" class="number" align="right"><strong><?php echo format_price($t['importe']);?></strong></td>
			<td style="font-size: 14px;" class="number" align="right"><strong><?php echo format_price($t['importeiva']);?></strong></td>
		</tr>
		<?php $odd = !$odd;?>
		<?php endforeach;?>
	</tbody>
</table>
</div>