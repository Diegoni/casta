<?php $this->load->helper('asset');?>
<?php echo css_asset('calendar.css', 'main');?>
<table summary="<?php echo $this->lang->line('Calendario');?>"
	align="center">
	<caption><?php echo $this->lang->line('Calendario');?> <br />
	<?php echo $trabajador['cNombre']?> <br />
	<?php echo $fecha1;?> - <?php echo $fecha2;?></caption>
	<thead>
		<tr>
			<th><?php echo $this->lang->line('Mes');?></th>
			<th colspan="2"><?php echo $this->lang->line('Dia');?></th>
			<th ><?php echo $this->lang->line('Horas');?></th>
			<th ><?php echo $this->lang->line('Información');?></th>
			<th ><?php echo $this->lang->line('Comentario');?></th>
		</tr>
	</thead>
	<tbody>
	<?php $odd = FALSE;?>
	<?php $horas = 0;?>
	<?php foreach($calendario as $h):?>
	<?php $d = getdate($h['dDia']); ?>
		<tr class="mes<?php echo ($d['mon']& 1)?'2':'1';?>">
			<td ><?php echo $h['Mes'];?></td>
			<?php $cls = ($d['wday'] == 6)?(($h['fHoras']<>0)?'dia6h':'dia6'):('dia' .$d['wday']);?>
			<td class="<?php echo $cls;?>"><?php echo $h['Dia'];?></td>
			<td class="<?php echo $cls;?>"><?php echo format_date($h['dDia']);?></td>
			<td align="right" class="normal<?php echo $d['wday'];?>"><?php echo format_number($h['fHoras']);?></td>
			<td class="normal<?php echo $d['wday'];?>"><?php echo $h['cDescripcion'];?></td>
			<td class="normal<?php echo $d['wday'];?>"><?php echo $h['cComentario'];?></td>
		</tr>
		<?php $horas += $h['fHoras'];?>
		<?php $odd = !$odd;?>
		<?php endforeach;?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="4" align="right"><?php echo format_number($horas);?></td>
			<td colspan="2">&nbsp;</td>
		</tr>
	</tfoot>
</table>
