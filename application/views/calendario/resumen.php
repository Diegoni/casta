<table summary="<?php echo $this->lang->line('Resumen Año');?> <?php echo $year; ?>">
	<caption>
		<?php echo $this->lang->line('Resumen Año');?> <?php echo $year; ?>
		<br />
		<?php echo $trabajador['cNombre']
		?>
	</caption>
	<?php foreach($cals as $y => $cal):
	?>
	<thead>
		<tr>
			<th scope="col"><?php echo $y;?></th>
			<th scope="col"><?php echo $this->lang->line('Horas');?></th>
		</tr>
	</thead>
	<tbody>
		<tr class="alt">
			<td scope="row" id="_co" nowrap="nowrap"><?php echo $this->lang->line('horas-realizar');?></td>
			<td align="right"><?php echo format_number($cal['fHoras']);?></td>
		</tr>
		<tr>
			<td scope="row" id="_co2" nowrap="nowrap"><?php echo $this->lang->line('horas-realizadas');?></td>
			<td align="right"><?php echo format_number($cal['Total']);?></td>
		</tr>
		<?php if (isset($horas[$y])):
		?>
		<?php $odd = TRUE;?>
		<?php $diff = 0;?>
		<?php foreach($horas[$y] as $h):
		?>
		<tr<?php if ($odd):?> class="alt"<?php endif;?>>
			<td scope="row" nowrap="nowrap"><?php if (isset($h['nYear2'])):
			?>
			<?php if ($h['nYear'] == $h['nYear2']):
			?>
			<?php echo ($h['fHoras']>0)?
sprintf($this->lang->line('calendario-resumen-negativo'), format_number($h['fHoras']), $h['nYear'], $h['nYear']+1):
sprintf($this->lang->line('calendario-resumen-positivo'), format_number(-$h['fHoras']), $h['nYear'], $h['nYear']+1);
			?>
			<?php else:?>
			<?php echo ($h['fHoras']>0)?
				sprintf($this->lang->line('calendario-resumen-negativo'), format_number($h['fHoras']), $h['nYear2'], $h['nYear']):
				sprintf($this->lang->line('calendario-resumen-positivo'), format_number(-$h['fHoras']), $h['nYear2'], $h['nYear']);
			?>
			<?php endif;?>
			<?php else:?>
			<?php echo $h['cDescripcion'];?>
			<?php endif;?></td>
			<td scope="row" nowrap="nowrap" align="right"><?php echo format_number(($h['fHoras']>0)?$h['fHoras']:-$h['fHoras']);?></td>
			</tr> <?php $odd = !$odd;?>
			<?php $diff += $h['fHoras'];?>
			<?php endforeach;?>
			<?php endif;?>
		<?php $odd = TRUE;?>
		<?php foreach($extras as $h):
		?>
		<tr<?php if ($odd):?> class="alt"<?php endif;?>>
			<td scope="row" nowrap="nowrap">
			<?php echo ($h['fHoras']>0)?
				sprintf($this->lang->line('calendario-resumen-negativo-extra'), format_number($h['fHoras']), $h['cDescripcion']):
				sprintf($this->lang->line('calendario-resumen-positivo-extra'), format_number(-$h['fHoras']), $h['cDescripcion']);
			?>			
			</td>
			<td scope="row" nowrap="nowrap" align="right"><?php echo format_number(($h['fHoras']>0)?$h['fHoras']:-$h['fHoras']);?></td>
			</tr> <?php $odd = !$odd;?>
			<?php endforeach;?>
	</tbody>
	<tfoot>
			<tr>
				<td scope="row"><?php echo ($cal['Diferencia'] < 0)?$this->lang->line('HORAS A FAVOR'):(($cal['Diferencia']>0)?$this->lang->line('HORAS EN CONTRA'):$this->lang->line('RESUMEN'));
				?></td>
				<td align="right"><?php echo format_number(($cal['Diferencia'] < 0) ? -$cal['Diferencia'] : $cal['Diferencia']);?></td>
			</tr>
	</tfoot>
	<?php endforeach;?>
</table>
