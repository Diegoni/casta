<?php $this->load->library('HtmlFile'); ?>
<?php echo get_instance()->htmlfile->orientation(ORIENTATION_LANDSCAPE); ?>
<?php foreach($secciones as $k => $sec):?>
<table width="100%">
	<caption><strong><?php echo $this->lang->line('Ajustes de stock'); ?></strong></caption>
	<thead>
		<tr>
			<th colspan="7"><?php echo $k;?></th>
		</tr>
		<tr>
			<th><?php echo $this->lang->line('Fecha');?></th>
			<th width="100%"><?php echo $this->lang->line('Título');?></th>
			<th><?php echo $this->lang->line('Motivo');?></th>
			<th><?php echo $this->lang->line('F.');?></th>
			<th><?php echo $this->lang->line('D.');?></th>
		</tr>
	</thead>
	<tbody>
	<?php $odd = FALSE;?>
	<?php foreach($sec as $m):?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td width="1%"><?php echo format_date($m['dCreacion']);?></td>
			<td width="60%">(<?php echo format_enlace_cmd($m['nIdLibro'], site_url('catalogo/articulo/index/' . $m['nIdLibro']));?>)<?php echo $m['cTitulo'];?></td>
			<td nowrap="nowrap" width="35%"><?php echo $m['cMotivo'];?></td>
			<td width="1%" align="right"><?php echo (isset($m['nCantidadFirme']))?format_number($m['nCantidadFirme']):'&nbsp;';?></td>
			<td width="1%" align="right"><?php echo (isset($m['nCantidadDeposito']))?format_number($m['nCantidadDeposito']):'&nbsp;';?></td>
		</tr>
		<?php $odd = !$odd;?>
		<?php endforeach;?>
	</tbody>
</table>
<div class="page-break"></div>
		<?php endforeach; ?>
