<?php $this->load->library('HtmlFile'); ?>
<?php echo get_instance()->htmlfile->orientation(ORIENTATION_LANDSCAPE); ?>
<div style='page-break-after: always;'>
<?php if ($this->config->item('bp.concursos.general')):?>
<table class="sortable-onload-shpw-0 rowstyle-alt colstyle-alt">
	<thead>
		<tr>
			<th colspan="11" align="center"><b><?php echo $this->lang->line('General');?></b></th>
		</tr>
		<tr>
			<th><b><?php echo $this->lang->line('Código');?></b></th>
			<th><b><?php echo $this->lang->line('Biblioteca');?></b></th>
			<th align="right"><b><?php echo $this->lang->line('Presupuesto');?></b></th>
			<th align="right"><b><?php echo $this->lang->line('Entregado');?></b></th>
			<th align="right"><b><?php echo $this->lang->line('Pedido');?></b></th>
			<th align="right"><b><?php echo $this->lang->line('Abonado');?></b></th>
			<th align="right"><b><?php echo $this->lang->line('Catalogar');?></b></th>
			<th align="right"><b><?php echo $this->lang->line('Catalogado');?></b></th>
			<th align="right"><b><?php echo $this->lang->line('Pendiente');?></b></th>
			<th align="right"><b>%</b></th>
		</tr>
	</thead>
	<?php $t1 = $t2 = $t3 = $t4 = $t5 = $t6 = $t7 = $t8 = $t9 = $t10 = $t11 = $t12 = 0;?>
	<tbody>
	<?php $odd = FALSE;?>
	<?php foreach($datos as $c):?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td><?php echo $c['nIdCliente']?></td>
			<td><strong><?php echo $c['cEmpresa']?></strong></td>
			<td align="right"><?php echo format_price($c['fImporte1']);?></td>
			<td align="right"><?php echo format_price($c['fAlbaranG']);?></td>
			<td align="right"><?php echo format_price($c['fPendienteG']);?></td>
			<td align="right"><?php echo format_price($c['fAbonoG']);?></td>
			<td align="right"><?php echo format_price($c['fACatalogarG']);?></td>
			<td align="right"><?php echo format_price($c['fCatalogadoG']);?></td>
			<td align="right"><?php echo format_price($c['fImporte1'] - $c['fAbonoG'] - $c['fAlbaranG'] - $c['fPendienteG'] );?></td>
			<td align="right"><?php echo format_percent((100 * ($c['fImporte1'] - $c['fAbonoG'] - $c['fAlbaranG'] 
			- $c['fPendienteG'])) / $c['fImporte1']);?></td>
		</tr>
		<?php
		$t1 += $c['fImporte1'];
		$t3 += $c['fAlbaranG'];
		$t5 += $c['fPendienteG'];
		$t7 += $c['fAbonoG'];
		$t9 += $c['fACatalogarG'];
		$t11 += $c['fCatalogadoG'];
		?>
		<?php $odd = !$odd;?>
		<?php endforeach;?>
	</tbody>
	<tfoot>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td align="right"><strong><?php echo format_price($t1);?></strong></td>
			<td align="right"><strong><?php echo format_price($t3);?></strong></td>
			<td align="right"><strong><?php echo format_price($t5);?></strong></td>
			<td align="right"><strong><?php echo format_price($t7);?></strong></td>
			<td align="right"><strong><?php echo format_price($t9);?></strong></td>
			<td align="right"><strong><?php echo format_price($t11);?></strong></td>
			<td align="right"><strong><?php echo format_price($t1 - $t3 - $t7 - $t5);?></strong></td>
			<td align="right"><strong><?php echo format_percent((100 * ($t1 - $t3 - $t7 - $t5)) / $t1);?></strong></td>
		</tr>
	</tfoot>
</table>
<?php endif; ?>
</div>
<div style='page-break-after: always;'>
<?php if ($this->config->item('bp.concursos.narrativa')):?>
<table class="sortable-onload-shpw-0 rowstyle-alt colstyle-alt">
	<thead>
		<tr>
			<th colspan="11" align="center"><b><?php echo $this->lang->line('Narrativa');?></b></th>
		</tr>
		<tr>
			<th><b><?php echo $this->lang->line('Código');?></b></th>
			<th><b><?php echo $this->lang->line('Biblioteca');?></b></th>
			<th><b><?php echo $this->lang->line('Contraseña');?></b></th>
			<th align="right"><b><?php echo $this->lang->line('Presupuesto');?></b></th>
			<th align="right"><b><?php echo $this->lang->line('Entregado');?></b></th>
			<th align="right"><b><?php echo $this->lang->line('Pedido');?></b></th>
			<th align="right"><b><?php echo $this->lang->line('Abonado');?></b></th>
			<th align="right"><b><?php echo $this->lang->line('Catalogar');?></b></th>
			<th align="right"><b><?php echo $this->lang->line('Catalogado');?></b></th>
			<th align="right"><b><?php echo $this->lang->line('Pendiente');?></b></th>
			<th align="right"><b>%</b></th>
		</tr>
	</thead>
	<tbody>
	<?php $odd = FALSE;?>
	<?php foreach($datos as $c):?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td><?php echo $c['nIdCliente']?></td>
			<td><strong><?php echo $c['cEmpresa']?></strong></td>
			<td><?php echo $c['cPass']?></td>
			<td align="right"><?php echo format_price($c['fImporte2']);?></td>
			<td align="right"><?php echo format_price($c['fAlbaranN']);?></td>
			<td align="right"><?php echo format_price($c['fPendienteN']);?></td>
			<td align="right"><?php echo format_price($c['fAbonoN']);?></td>
			<td align="right"><?php echo format_price($c['fACatalogarN']);?></td>
			<td align="right"><?php echo format_price($c['fCatalogadoN']);?></td>
			<td align="right"><?php echo format_price($c['fImporte2'] - $c['fAbonoN'] - $c['fAlbaranN'] - $c['fPendienteN']);?></td>
			<td align="right"><?php echo format_percent((100*($c['fImporte2'] - $c['fAbonoN'] 
			- $c['fAlbaranN'] - $c['fPendienteN'])) / $c['fImporte2']);?></td>
		</tr>
		<?php
		$t2 += $c['fImporte2'];
		$t4 += $c['fAlbaranN'];
		$t6 += $c['fPendienteN'];
		$t8 += $c['fAbonoN'];
		$t10 += $c['fACatalogarN'];
		$t12 += $c['fCatalogadoN'];
		?>
		<?php $odd = !$odd;?>
		<?php endforeach;?>
	</tbody>
	<tfoot>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td align="right"><strong><?php echo format_price($t2);?></strong></td>
			<td align="right"><strong><?php echo format_price($t4);?></strong></td>
			<td align="right"><strong><?php echo format_price($t6);?></strong></td>
			<td align="right"><strong><?php echo format_price($t8);?></strong></td>
			<td align="right"><strong><?php echo format_price($t10);?></strong></td>
			<td align="right"><strong><?php echo format_price($t12);?></strong></td>
			<td align="right"><strong><?php echo format_price($t2 - $t4 - $t8 - $t6);?></strong></td>
			<td align="right"><strong><?php echo format_percent((100*($t2 - $t4 - $t8 - $t6))/ $t2);?></strong></td>
		</tr>
	</tfoot>
</table>
<?php endif;?>
</div>