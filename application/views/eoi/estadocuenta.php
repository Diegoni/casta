<?php $this->load->helper('extjs');?>
<table class="rowstyle-alt colstyle-alt no-arrow"
	summary="<?php echo $this->lang->line('estado-cuenta');?> <?php echo $fecha;?>">
	<caption>
		<?php echo $this->lang->line('estado-cuenta');?>
		<strong><?php echo $escuela['cDescripcion'];?></strong> <br />
		<?php echo $fecha;?>
	</caption>
	<thead>
	<tr class="HeaderStyle">
		<th class="sortable-date-dmy" scope="col"><?php echo $this->lang->line('Fecha');?></th>
		<th class="sortable" scope="col"><?php echo $this->lang->line('cConcepto');?></th>
		<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('fEntrada');?></th>
		<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('fSalida');?></th>
	</tr>
	<?php if (isset($pre)):
	?>
	<tr>
		<td colspan="4"
		><strong><?php echo $this->lang->line('Saldo anterior');?>: <?php echo format_price(-$pre);?></strong></td>
	</tr>
	<?php endif;?>
	</thead>
	<tbody>
	<?php $odd = FALSE;?>
	<?php foreach($valores as $valor):
	?>
	<tr <?php if ($odd):?> class="alt" <?php endif;?>>
		<td ><?php echo format_date($valor['dFecha']);?></td>
		<?php if (isset($valor['nIdImporte'])):?>
			<td align="left">
			<?php echo $valor['cConcepto'];?>
			</td>
		<?php else:?>
			<td align="left">
				<strong><?php echo $valor['cDescripcion'];?></strong><br/>
					<?php echo $this->lang->line('Albarán');?>
			<?php
			$link = format_enlace_cmd($valor['nIdAlbaran'], site_url('ventas/albaransalida/index/' . $valor['nIdAlbaran']));
			echo $link;
			?><br/>
				<?php if (isset($valor['nIdLibro'])):?>
					<?php echo $this->lang->line('Renovación suscripción');?>
			<?php
			$link = format_enlace_cmd($valor['nIdSuscripcion'], site_url('suscripciones/suscripcion/index/' . $valor['nIdSuscripcion']));
			echo $link . '<br/><strong>' .$valor['cTitulo'] .'</strong>';
			?>
				<?php endif; ?> 
			</td>
		<?php endif;?>
			<td align="right" style="color:blue;"> 
				<?php if ($valor['fImporte'] < 0):?>
				<?php echo format_price(-$valor['fImporte']);?>
				<?php endif;?>
			</td>
			<td align="right" style="color:red;"><?php 
			if ($valor['fImporte'] >= 0):?>
				<?php echo format_price(-$valor['fImporte']);?>
			<?php endif;?>
			</td>

	</tr>
	<?php $odd = !$odd;?>
	<?php endforeach;?>

	<?php if (isset($post)):
	?>
	</tbody>
	<tfoot>
	<tr>
		<td colspan="4"
		><strong><?php echo $this->lang->line('Saldo actual');?>: <?php echo format_price(-$post);?></strong></td>
	</tr>
	</tfoot>
	<?php endif;?>
</table>
