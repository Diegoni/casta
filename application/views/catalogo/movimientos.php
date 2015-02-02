<?php $this->load->library('HtmlFile'); ?>
<?php echo get_instance()->htmlfile->orientation(ORIENTATION_LANDSCAPE); ?>
<table width="100%"
	class="sortable-onload-shpw-0 rowstyle-alt colstyle-alt">
	<caption><strong><?php echo $this->lang->line('Consultar movimientos sección'); ?></strong></caption>
	<thead>
		<tr>
			<th class="sortable-date-dmy" width="1%"><?php echo $this->lang->line('Fecha');?></th>
			<th class="sortable" width="1%"><?php echo $this->lang->line('Usuario');?></th>
			<th class="sortable" width="1%"><?php echo $this->lang->line('Origen');?></th>
			<th class="sortable" width="1%"><?php echo $this->lang->line('Destino');?></th>
			<th class="sortable" width="90%"><?php echo $this->lang->line('Título');?></th>
			<th class="sortable-numeric" width="1%"><?php echo $this->lang->line('Uni.');?></th>
			<th class="sortable-numeric" width="1%"><?php echo $this->lang->line('F.');?></th>
			<th class="sortable-numeric" width="1%"><?php echo $this->lang->line('D.');?></th>
		</tr>
	</thead>
	<tbody>
	<?php $odd = FALSE;?>
	<?php foreach($movimientos as $m):?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td width="1%"><?php echo format_datetime($m['dCreacion']);?></td>
			<td width="1%"><?php echo $m['cCUser'];?></td>
			<td nowrap="nowrap" width="1%"><?php echo $m['cSeccionOrigen'];?></td>
			<td nowrap="nowrap" width="1%"><?php echo $m['cSeccionDestino'];?></td>
			<td width="90%">(<?php echo format_enlace_cmd($m['nIdLibro'], site_url('catalogo/articulo/index/' . $m['nIdLibro']));?>)<?php echo $m['cTitulo'];?></td>
			<td width="1%" align="right"><?php echo (isset($m['nCantidad']))?format_number($m['nCantidad']):'&nbsp;';?></td>
			<td width="1%" align="right"><?php echo (isset($m['nEnFirme']))?format_number($m['nEnFirme']):'&nbsp;';?></td>
			<td width="1%" align="right"><?php echo (isset($m['nEnDeposito']))?format_number($m['nEnDeposito']):'&nbsp;';?></td>
		</tr>
		<?php $odd = !$odd;?>
		<?php endforeach;?>
	</tbody>
</table>

