<?php $importe = $descuento = 0;?>
<?php $total = array(); ?>
<?php foreach($bibliotecas as $biblio => $c):?>
<?php $importe += $bibliotecas[$biblio]['importe']; ?>
<?php $descuento = $bibliotecas[$biblio]['dto']; ?>
<div style='page-break-after: always;'>
<table class="sortable-onload-shpw-0 rowstyle-alt colstyle-alt">
	<thead>
		<tr>
			<th colspan="7" align="center"><?php echo $biblio?></th>
		</tr>
		<tr>
			<th><b><?php echo $this->lang->line('Estado');?></b></th>
			<th><?php echo $this->lang->line('Un.');?></th>
			<th><?php echo $this->lang->line('fCoste');?></th>
			<th><?php echo $this->lang->line('Venta (Base)');?></th>
			<th><?php echo $this->lang->line('Venta (Base - Dto)');?></th>
			<th><?php echo $this->lang->line('Venta (PVP)');?></th>
			<th><?php echo $this->lang->line('Venta (PVP - Dto)');?></th>
		</tr>
	</thead>
	<tbody>
	<?php $odd = FALSE;?>
	<?php foreach($datos[$biblio] as $estado => $c):?>
		<?php $t4 = $t5 = $t6 = 0 ?>
		<?php 
			if ($cuenta[$estado]) 
			{
				$bibliotecas[$biblio]['unidades'] += $c['nUnidades'];
				$bibliotecas[$biblio]['coste'] += $c['fCoste'];
				$bibliotecas[$biblio]['venta'] += $c['fVentaSinIVA'];
				$bibliotecas[$biblio]['venta2'] += $c['fVentaConIVA'];
				if (!isset($total[$estado]['venta'])) $total[$estado]['venta'] = $total[$estado]['venta2'] = 0;
			}
			$total[$estado]['venta'] += $c['fVentaSinIVA'];
			$total[$estado]['venta2'] += $c['fVentaConIVA'];
			$total[$estado]['unidades'] += $c['nUnidades'];
			$total[$estado]['coste'] += $c['fCoste'];
			?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td><?php echo $this->lang->line($estado) . (($cuenta[$estado])?'+':''); ?></td>
			<td align="right"><?php echo format_number($c['nUnidades']);?></td>
			<td align="right"><?php echo format_price($c['fCoste']);?></td>
			<td align="right"><?php echo format_price($c['fVentaSinIVA']);?></td>
			<td align="right"><?php echo format_price($c['fVentaSinIVA'] * (1 - $descuento/100));?></td>
			<td align="right"><?php echo format_price($c['fVentaConIVA']);?></td>
			<td align="right"><?php echo format_price($c['fVentaConIVA'] * (1 - $descuento/100));?></td>
		</tr>
		<?php $odd = !$odd;?>
		<?php endforeach;?>
	</tbody>
	<tfoot>
		<tr>
			<td><?php echo $this->lang->line('Unidades');?></td>
			<td colspan="6" align="right"><?php echo format_number($bibliotecas[$biblio]['unidades']);?></td>
		</tr>
		<tr>
			<td><?php echo $this->lang->line('Coste');?></td>
			<td colspan="6" align="right"><?php echo format_price($bibliotecas[$biblio]['coste']);?></td>
		</tr>
		<tr>
			<td><?php echo $this->lang->line('fDescuento');?></td>
			<td colspan="6"  align="right"><?php echo format_percent($bibliotecas[$biblio]['dto']);?></td>
		</tr>
		<tr>
			<td><?php echo $this->lang->line('Venta (Base)');?></td>
			<td  colspan="6" align="right"><?php echo format_price($bibliotecas[$biblio]['venta']);?></td>
		</tr>
		<tr>
			<td><?php echo $this->lang->line('Venta (Base) (-dto)');?></td>
			<td  colspan="6" align="right"><?php echo format_price($bibliotecas[$biblio]['venta'] * (1 - $bibliotecas[$biblio]['dto']/100));?></td>
		</tr>
		<tr>
			<td><?php echo $this->lang->line('Venta (PVP)');?></td>
			<td  colspan="6" align="right"><?php echo format_price($bibliotecas[$biblio]['venta2']);?></td>
		</tr>
		<tr>
			<td><?php echo $this->lang->line('Concurso (PVP)');?></td>
			<td colspan="6" align="right"><?php echo format_price($bibliotecas[$biblio]['importe']);?></td>
		</tr>
		<tr>
			<td><?php echo $this->lang->line('Venta (PVP) (-dto)');?></td>
			<td  colspan="6" align="right"><?php echo format_price($bibliotecas[$biblio]['venta2'] * (1 - $bibliotecas[$biblio]['dto']/100));?></td>
		</tr>
		<tr>
			<td><?php echo $this->lang->line('Pendiente');?></td>
			<td  colspan="6" align="right"><?php echo format_price($bibliotecas[$biblio]['importe'] - $bibliotecas[$biblio]['venta2'] * (1 - $bibliotecas[$biblio]['dto']/100));?></td>
		</tr>
	</tfoot>
</table>
</div>
<?php endforeach;?>
<div style='page-break-after: always;'>
<table class="sortable-onload-shpw-0 rowstyle-alt colstyle-alt">
	<thead>
		<tr>
			<th colspan="7" align="center"><?php echo $this->lang->line('TOTAL');?></th>
		</tr>
		<tr>
			<th><b><?php echo $this->lang->line('Estado');?></b></th>
			<th><?php echo $this->lang->line('Un.');?></th>
			<th><?php echo $this->lang->line('fCoste');?></th>
			<th><?php echo $this->lang->line('Venta (Base)');?></th>
			<th><?php echo $this->lang->line('Venta (Base - Dto)');?></th>
			<th><?php echo $this->lang->line('Venta (PVP)');?></th>
			<th><?php echo $this->lang->line('Venta (PVP - Dto)');?></th>
		</tr>
	</thead>
	<tbody>
	<?php $odd = FALSE;?>
	<?php $t4 = $t5 = $t6 = $t7 = $t8 = 0; ?>
	<?php foreach($total as $estado => $c):?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td><?php echo $this->lang->line($estado) . (($cuenta[$estado])?'+':''); ?></td>
			<td align="right"><?php echo format_number($c['unidades']);?></td>
			<td align="right"><?php echo format_price($c['coste']);?></td>
			<td align="right"><?php echo format_price($c['venta']);?></td>
			<td align="right"><?php echo format_price($c['venta'] * (1 - $descuento/100));?></td>
			<td align="right"><?php echo format_price($c['venta2']);?></td>
			<td align="right"><?php echo format_price($c['venta2'] * (1 - $descuento/100));?></td>
			<?php 
			if ($cuenta[$estado]) 
			{
				$t4 += $c['unidades']; 
				$t5 += $c['coste']; 
				$t6 += $c['venta']; 
				$t7 += $c['venta2']; 
				$t8 += isset($c['importe'])?$c['importe']:0;
			}
			?>
		</tr>
		<?php $odd = !$odd;?>
	<?php endforeach;?>
	</tbody>
	<tfoot>
		<tr>
			<td><?php echo $this->lang->line('Unidades');?></td>
			<td colspan="6" align="right"><?php echo format_number($t4);?></td>
		</tr>
		<tr>
			<td><?php echo $this->lang->line('Coste');?></td>
			<td colspan="6" align="right"><?php echo format_price($t5);?></td>
		</tr>
		<tr>
			<td><?php echo $this->lang->line('Concurso (PVP)');?></td>
			<td colspan="6" align="right"><?php echo format_price($importe);?></td>
		</tr>
		<tr>
			<td><?php echo $this->lang->line('Venta (PVP) (-dto)');?></td>
			<td  colspan="6" align="right"><?php echo format_price($t7 * (1 - $descuento/100));?></td>
		</tr>
		<tr>
			<td><?php echo $this->lang->line('Pendiente');?></td>
			<td  colspan="6" align="right"><?php echo format_price($importe - $t7 * (1 - $descuento/100));?></td>
		</tr>
	</tfoot>
</table>
</div>