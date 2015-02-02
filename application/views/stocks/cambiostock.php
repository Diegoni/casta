<?php $this->load->helper('asset');?>
<?php $this->load->library('HtmlFile'); ?>
<?php echo get_instance()->htmlfile->orientation(ORIENTATION_LANDSCAPE); ?>
<?php foreach ($cambios as $k => $padres):?>
	<h1><?php echo $k;?></h1>
	<?php foreach ($padres as $k2 => $regs):?>
	<div style='page-break-after: always;'>
<div class="details-panel">
<table class="sortable-onload-shpw-0 rowstyle-alt colstyle-alt no-arrow"
	summary="<?php echo $this->lang->line('Asignar stock contado');?>">
	<caption><strong><?php echo $this->lang->line('Asignar stock contado');?></strong></caption>
	<thead>
		<tr>
			<th colspan="9"><?php echo $k2;?></th>
		</tr>
		<tr>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Sección');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Id');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('cTitulo');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('FM Ant.');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('FM Nvo.');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Diff');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('DP Ant.');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('DP Nvo.');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Diff');?></th>
		</tr>
	</thead>
	<tbody>
	<?php $odd = FALSE;?>
	<?php $dp = $fm = $fold = $fnew = $dold = $dnew = 0; ?>
	<?php foreach ($regs as $seccion):?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
		<td nowrap="nowrap"><b><?php echo $seccion['cNombre'];?></b></td>
		<td><?php echo format_enlace_cmd($seccion['nIdLibro'], site_url('catalogo/articulo/index/' . $seccion['nIdLibro']));?></td>
		<td><?php echo format_title($seccion['cTitulo'], 60);?></td>
		<td align="right"><?php echo format_number($seccion['nStockFirme']);?></td>
		<td align="right"><?php echo format_number($seccion['nStockFirmeReal']);?></td>
		<td align="right"><strong><?php echo format_number($seccion['df']);?></strong></td>
		<td align="right"><?php echo format_number($seccion['nStockDeposito']);?></td>
		<td align="right"><?php echo format_number($seccion['nStockDepositoReal']);?></td>
		<td align="right"><strong><?php echo format_number($seccion['dd']);?></strong></td>
	</tr>
		<?php $odd = !$odd;?>
		<?php $fm += $seccion['df'];?>
		<?php $dp += $seccion['dd'];?>
		<?php $fold += $seccion['nStockFirme'];?>
		<?php $fnew += $seccion['nStockFirmeReal'];?>
		<?php $dold += $seccion['nStockDeposito'];?>
		<?php $dnew += $seccion['nStockDepositoReal'];?>
	<?php endforeach;?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="3" scope="row" align="right">&nbsp;</td>
			<td scope="row" align="right"><?php echo format_number($fold);?></td>
			<td scope="row" align="right"><?php echo format_number($fnew);?></td>
			<td scope="row" align="right"><strong><?php echo format_number($fm);?></strong></td>
			<td scope="row" align="right"><?php echo format_number($dold);?></td>
			<td scope="row" align="right"><?php echo format_number($dnew);?></td>
			<td scope="row" align="right"><strong><?php echo format_number($dp);?></strong></td>
		</tr>
		<tr>
			<td colspan="9" scope="row" align="right"><?php echo count($cambios);?>
			<?php echo $this->lang->line('registros');?></td>
		</tr>
	</tfoot>
</table>
</div>
</div>
<?php endforeach;?>
<?php endforeach;?>