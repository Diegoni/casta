<?php $this->load->helper('asset');?>
<div class="details-panel">
<table width="100%">
	<tr >
		<th colspan="12" class="title"><b>(<?php echo $nIdLibro;?>) <?php echo $cTitulo;?></b>
		</th>
	</tr>
	<tr >
		<th><?php echo $this->lang->line('Sección');?></th>
		<th><?php echo $this->lang->line('Dis');?></th>
		<th><?php echo $this->lang->line('FM');?></th>
		<th><?php echo $this->lang->line('DP');?></th>
		<th><?php echo $this->lang->line('Rec');?></th>
		<th><?php echo $this->lang->line('APed');?></th>
		<th><?php echo $this->lang->line('AServ');?></th>
		<th><?php echo $this->lang->line('Res');?></th>
		<th><?php echo $this->lang->line('ADev');?></th>
	</tr>
	<?php if (count($secciones)>0):?>
<?php $par = FALSE;?>
	<?php foreach ($secciones as $seccion):?>
		<?php $class = ($par)? 'number2':'number';?>
	<tr>
		<td class="label"><b><?php echo $seccion['cNombre'];?> (<?php echo $seccion['nIdSeccion'];?>)</b></td>
		<td class="<?php echo $class;?>"><?php echo format_ceronada($seccion['nStockDisponible']);?></td>
		<td class="<?php echo $class;?>"><?php echo format_ceronada($seccion['nStockFirme']);?></td>
		<td class="<?php echo $class;?>"><?php echo format_ceronada($seccion['nStockDeposito']);?></td>
		<td class="<?php echo $class;?>"><?php echo format_ceronada($seccion['nStockRecibir']);?></td>
		<td class="<?php echo $class;?>"><?php echo format_ceronada($seccion['nStockAPedir']);?></td>
		<td class="<?php echo $class;?>"><?php echo format_ceronada($seccion['nStockServir']);?></td>
		<td class="<?php echo $class;?>"><?php echo format_ceronada($seccion['nStockReservado']);?></td>
		<td class="<?php echo $class;?>"><?php echo format_ceronada($seccion['nStockADevolver']);?></td>
	</tr>
<?php $par = !$par;?>
	<?php endforeach;?>
	<?php endif; ?>
	</tbody>
</table>
</div>
