<?php $this->load->helper('extjs');?>
<table class="sortable-onload-shpw-0 rowstyle-alt colstyle-alt no-arrow"
summary="<?php echo $this->lang->line('Artículos sin idioma');?> <?php echo $mes;?>-<?php echo $year;?>">
	<caption>
	<?php echo $this->lang->line('Artículos sin idioma');?> <?php echo $mes;?>-<?php echo $year;?>
	</caption>
	<thead>
		<tr class="HeaderStyle">
			<th class="sortable" scope="col"><?php echo $this->lang->line('Id');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Título');?></th>
		</tr>
	</thead>
	<tbody>
		<?php $odd = FALSE;?>
		<?php foreach($valores as $valor): ?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td align="left">
			<?php echo format_enlace_cmd($valor['nIdLibro'], site_url('catalogo/articulo/index/' . $valor['nIdLibro']));?>
			</td>
			<td align="left"><?php echo $valor['cTitulo'];?></td>
		</tr>
		<?php $odd = !$odd;?>
		<?php endforeach;?>
	</tbody>
</table>
