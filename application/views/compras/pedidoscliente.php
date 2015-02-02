<?php $this->load->helper('asset');?>
<?php echo css_asset('thickbox.css');?>
<?php echo css_asset('icons.css', 'main');?>
<table class="sortable-onload-shpw-0 rowstyle-alt colstyle-alt no-arrow"
summary="<?php echo $this->lang->line('Pedidos cliente de albarán de entrada');?>">
	<caption>
		<strong><?php echo $this->lang->line('Pedidos cliente de albarán de entrada');?></strong>
		<br />
		<?php echo $nIdAlbaran;?>
	</caption>
	<thead>
		<tr>
			<th>&nbsp;</th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('nCantidad');?></th>
			<th>&nbsp;</th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Pedido');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Sección');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('cTitulo');?></th>
		</tr>
	</thead>
	<tbody>
		<?php $odd = FALSE;?>
		<?php $count = 0;?>
		<?php foreach($pedidos as $m):
		?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?> id="linea_<?php echo $count;?>">
			<td width="1%"><a class="icon-delete" style="width:16px;cursor: pointer;" rel = "<?php echo $count;?>">&nbsp;&nbsp;&nbsp;&nbsp;</a></td>
			<td align="right"><span style='font-size: 300%'><?php echo format_number($m['nCantidad']);?></span></td>
			<td><?php echo format_lightbox(format_cover($m['nIdLibro'], $this->config->item('bp.catalogo.cover.articulo'), 'portada'), format_url_cover($m['nIdLibro']));?></td>
			<td><span style='font-size: 200%'><?php echo format_enlace_cmd($m['nIdPedido'], site_url('ventas/pedidocliente/index/' . $m['nIdPedido']));?></span>
			<br/>
			<span style='font-size: 150%'><?php echo($m['cCliente']);?></span>
			<br/>
			<?php echo format_date($m['dCreacion']);?><br/><?php echo $m['cRefCliente'];?>-<?php echo $m['cRefInterna'];?>-<?php echo $m['cRefCliente2'];?>-<?php echo $m['cRefInterna2'];?></td>
			<td><span style="color: blue;"><?php echo($m['cSeccion']);?></span></td>
			<td><?php echo format_enlace_cmd($m['nIdLibro'], site_url('catalogo/articulo/index/' . $m['nIdLibro']));?><br/><span style="color: green; font-weight: bold"><?php echo $m['cTitulo'];?></span>
			<br/>
			<?php echo $m['cISBN'];?></td>
		</tr>
		<?php $odd = !$odd;?>
		<?php ++$count;?>
		<?php endforeach;?>
	</tbody>
</table>
<?php echo js_asset('jQuery/jquery.min.js');?>
<?php echo js_asset('jQuery/jquery.thickbox.min.js');?>
<script type="text/javascript">
	$(function() {
		jQuery('.icon-delete').bind('click', function(item) {
			jQuery('#linea_' + item.currentTarget.rel).fadeOut('slow');
			return false;
		});
	});
</script>