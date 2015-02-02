<table
	summary="<?php echo $this->lang->line('Vacaciones');?> <?php echo $year;?>">
	<caption><?php echo $this->lang->line('Vacaciones');?> <?php echo $year;?>
	<br />
	<?php echo $trabajador['cNombre']?></caption>
	<thead>
		<tr>
			<th scope="col"><?php echo $this->lang->line('Dia');?></th>
		</tr>
	</thead>
	<tbody>
	<?php $odd = FALSE;?>
	<?php foreach($vacaciones as $h):?>
	<?php if (isset($h['dDia'])):?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td scope="row"><?php echo format_date($h['dDia']);?></td>
		</tr>
		<?php $odd = !$odd;?>
		<?php endif; ?>
		<?php endforeach;?>
	</tbody>
</table>
