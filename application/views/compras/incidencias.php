<?php $this->load->helper('extjs');?>
<?php $this->load->helper('asset');?>
<?php echo css_asset('thickbox.css');?>
<?php echo css_asset('icons.css', 'main');?>
<?php if (count($errores) > 0):?>
<div id="main-nav-holder" style=" position:fixed; top:0; width:100%;z-index:100;">
<table>
		<tr>
			<th><?php echo $this->lang->line('BUENO');?></th>
			<th><?php echo $this->lang->line('MALO');?></th>
		</tr>
	<tr>
		<td><span id="recibido"></span>
		</td>
		<td style="background: white;"><span id="pedido"></span>
		</td>		
	</tr>
	<tr>
		<td colspan="2" align="center">
			<button id="acc" href="#"><?php echo $this->lang->line('asignar');?></button>
			<button id="uni" href="#"><?php echo $this->lang->line('unificar');?></button>
		</td>
	</tr>
</table>
	</div>
<table style="margin-top: 130px;">
	<tr>
		<td valign="top" width="50%">
			<div  style="height: 500px; width: 100%; overflow: auto;">
<table 
summary="<?php echo $this->lang->line('Recibido Albarán');?>">
	<caption>
		<?php echo $this->lang->line('Recibido Albarán');?>
	</caption>
	<thead>
		<tr>
			<th scope="col">&nbsp;</th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Sección');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('nCantidad');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Asignar');?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($errores as $articulo):?>
			<tr id="recibido_<?php echo $articulo[0]['nIdLibro'];?>">
				<td colspan="4" id="recibido_i_<?php echo $articulo[0]['nIdLibro'];?>"><span style="color: green;">
					<strong><a style="cursor: pointer;" rel="<?php echo $articulo[0]['nIdLibro'];?>" class="recibido"><?php echo $articulo[0]['cTitulo'];?></a></strong>
				</span>
				<?php echo format_enlace_cmd('[' . $this->lang->line('abrir') . ']', site_url('catalogo/articulo/index/' . $articulo[0]['nIdLibro']));?>
				<br/>
					<?php echo $articulo[0]['nIdLibro']?> | <?php echo $articulo[0]['cISBN']?> |
					<span style="font-variant: italic;"><?php echo $articulo[0]['cAutores']?></span> | 
					<span style="color: blue;"><?php echo $articulo[0]['cEditorial']?></span> |
					<?php echo format_date($articulo[0]['dCreacion']);?> | <?php echo $articulo[0]['cCUser']?>
				</td>
			</tr>
			<!--<?php foreach($articulo as $m):?>
			<?php $odd = FALSE;?>
			<tr <?php if ($odd):?> class="alt" <?php endif;?>>
				<td>&nbsp;</td>
				<td ><?php echo $m['cSeccion'];?></td>
				<td align="right"><?php echo format_number($m['nCantidad']);?></td>
				<td align="right"><?php echo format_number($m['nAsignar']);?></td>
			</tr>
			<?php $odd = !$odd;?>
			<?php endforeach;?>-->
		<?php endforeach;?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="4" scope="row" align="right"><?php echo count($errores);?>
			<?php echo $this->lang->line('registros');?></td>
		</tr>
	</tfoot>
</table>
</div>
</td>
<td valign="top" width="50%">
	<div style="height: 500px; width: 100%; overflow: auto;">
<table 
summary="<?php echo $this->lang->line('Pendientes Concurso');?>">
	<caption>
		<?php echo $this->lang->line('Pendientes Concurso');?>
	</caption>
	<thead>
		<tr>
			<th scope="col">&nbsp;</th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Artículo');?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($alt as $m):?>
		<?php $odd = FALSE;?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?> id="pedido_<?php echo $m['nIdLineaPedidoConcurso'];?>">
			<td><button rel="<?php echo $m['nIdLineaPedidoConcurso'];?>" class="borrar"><?php echo $this->lang->line('borrar');?></button></td>
			<td id="pedido_i_<?php echo $m['nIdLineaPedidoConcurso'];?>"><span style="color: blue;">
				<?php echo format_enlace_cmd('[' . $this->lang->line('abrir') . ']', site_url('catalogo/articulo/index/' . $m['nIdLibro']));?>
				<strong><a style="cursor: pointer;" rel="<?php echo $m['nIdLineaPedidoConcurso'];?>" class="pedido"><?php echo $m['cTitulo'];?></a></strong>
				<br/>
			</span><br/>
				<?php echo $m['nIdLibro']?> | <?php echo $m['cISBN']?> |
				<span style="font-variant: italic;"><?php echo $m['cAutores']?></span> | 
				<span style="color: blue;"><?php echo $m['cEditorial']?></span> |
				<?php echo format_date($m['dCreacion']);?> | <?php echo $m['cCUser']?>
			</td>
		</tr>
		<?php $odd = !$odd;?>
		<?php endforeach;?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="4" scope="row" align="right"><?php echo count($alt);?>
			<?php echo $this->lang->line('registros');?></td>
		</tr>
	</tfoot>
</table>
</div>
	</td>
</tr>
</table>
<?php endif; ?>
<?php if (count($exceso) > 0): ?>
<table 
summary="<?php echo $this->lang->line('Cantidades sobrantes');?>">
	<caption>
		<?php echo $this->lang->line('Cantidades sobrantes');?>
	</caption>
	<thead>
		<tr>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Artículo');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('nCantidad');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Asignar');?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($exceso as $articulo):?>
			<?php $odd = FALSE;?>
			<tr <?php if ($odd):?> class="alt" <?php endif;?>>
				<td colspan="3" id="recibido_i_<?php echo $articulo['nIdLibro'];?>"><span style="color: green;">
					<?php echo format_enlace_cmd($articulo['cTitulo'], site_url('catalogo/articulo/index/' . $articulo['nIdLibro']));?>
				</span>				
				<br/>
					<?php echo $articulo['nIdLibro']?> | <?php echo $articulo['cISBN']?> |
					<span style="font-variant: italic;"><?php echo $articulo['cAutores']?></span> | 
					<span style="color: blue;"><?php echo $articulo['cEditorial']?></span> |
					<?php echo format_date($articulo['dCreacion']);?> | <?php echo $articulo['cCUser']?>
				</td>
			</tr>
			<?php $odd = !$odd;?>
		<?php endforeach;?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="4" scope="row" align="right"><?php echo count($exceso);?>
			<?php echo $this->lang->line('registros');?></td>
		</tr>
	</tfoot>
</table>

<?php endif; ?>

<?php if (count($errores) > 0):?>
<?php echo js_asset('jQuery/jquery.min.js');?>

<script type="text/javascript">
	$(function() {
		var id_pedido = null;
		var id_recibido = null;
		jQuery('#acc').bind('click', function(item) {
			if ((id_pedido != null) && (id_recibido != null))
			{
				parent.Ext.app.callRemote({
					url: "<?php echo site_url('concursos/pedidoconcursolinea/asignar');?>",
					params: {
						malo: id_pedido,
						bueno: id_recibido
					},
					fnok: function()
					{
						jQuery('#pedido_' + id_pedido).fadeOut('slow');
						jQuery('#recibido_' + id_recibido).fadeOut('slow');
						id_pedido = id_recibido = null;
						jQuery('#recibido').html('<span style="color:green;">Asignado</span>');
						jQuery('#pedido').html('<span style="color:red;">OK</span>');
					}
				});
			}
			return;
		});
		jQuery('#uni').bind('click', function(item) {
			if ((id_pedido != null) && (id_recibido != null))
			{
				parent.Ext.app.callRemote({
					url: "<?php echo site_url('concursos/pedidoconcursolinea/unificar');?>",
					params: {
						malo: id_pedido,
						bueno: id_recibido
					},
					fnok: function()
					{
						jQuery('#pedido_' + id_pedido).fadeOut('slow');
						jQuery('#recibido_' + id_recibido).fadeOut('slow');
						id_pedido = id_recibido = null;
						jQuery('#recibido').html('<span style="color:green;">Unificado</span>');
						jQuery('#pedido').html('<span style="color:red;">OK</span>');
					}
				});
			}
			return;
		});
		jQuery('.borrar').bind('click', function(item) {
			console.dir(item);
			var v = item.currentTarget.attributes.getNamedItem('rel').value;
			jQuery('#pedido_' + v).fadeOut('slow');
			return;
		});
		jQuery('.recibido').bind('click', function(item) {
			var v = item.currentTarget.rel;
			var t = jQuery('#recibido_i_' + v).html();
			id_recibido = v;
			jQuery('#recibido').html(t);
		});
		jQuery('.pedido').bind('click', function(item) {
			var v = item.currentTarget.rel;
			var t = jQuery('#pedido_i_' + v).html();
			id_pedido = v;
			jQuery('#pedido').html(t);
		});
		//jQuery('#main-nav-holder').waypoint({offset: 100});
		$(window).scroll(function(e){ 
			$el = $('#main-nav-holder'); 
		  	if ($(this).scrollTop() > 200 && $el.css('position') != 'fixed'){ 
		    	$('#main-nav-holder').css({'position': 'fixed', 'top': '0px'}); 
		  } 
		});
	});
</script>
<?php endif; ?>
