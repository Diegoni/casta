<?php $this->load->helper('extjs');?>
<?php $this->load->helper('asset');?>
<?php echo css_asset('icons.css', 'main');?>
<?php echo css_asset('jquery-ui-1.9.2.custom.css');?>
<h1><?php echo $this->lang->line('Obras concurso');?> <strong><?php echo $concurso['cDescripcion'];?></strong></h1>
<?php if (count($datos) > 0):?>
		<table class="sortable-onload-shpw-0 rowstyle-alt colstyle-alt">
			</caption>
			<thead>
				<tr>
					<th><?php echo $this->lang->line('Pedido'); ?></th>
					<th><?php echo $this->lang->line('Estado'); ?></th>					
					<th><?php echo $this->lang->line('Asignado'); ?></th>
					<th><?php echo $this->lang->line('Estado'); ?></th>
					<th><?php echo $this->lang->line('Acciones'); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach($datos as $m):?>
				<?php $odd = FALSE; ?>
				<tr id="t_<?php echo $m['nIdLineaPedidoConcurso'];?>" <?php if ($odd):?> class="alt" <?php endif;?>>
				<td>
					<?php echo format_enlace_cmd($m['cTitulo'], site_url('catalogo/articulo/index/' . $m['nIdLibro']));?>
				</td>
				<td><?php echo $m['cEstado']; ?></td>
				<td><?php echo $m['cTitulo2']; ?></td>
				<td><?php echo $m['cEstadoLibro']; ?></td>
				<td>
					<?php if(in_array($m['nIdEstadoLibro'], array(6))):?>
					<button class="acc" rel="a_<?php echo $m['nIdLineaPedidoConcurso'];?>"><?php echo $this->lang->line('Agotado');?></button>
					<?php elseif (in_array($m['nIdEstadoLibro'], array(13, 14, 12, 15))):?>
					<button class="acc" rel="d_<?php echo $m['nIdLineaPedidoConcurso'];?>"><?php echo $this->lang->line('Descatalogado');?></button>
					<?php elseif (in_array($m['nIdEstadoLibro'], array(7,8))):?>					
					<button class="acc" rel="r_<?php echo $m['nIdLineaPedidoConcurso'];?>"><?php echo $this->lang->line('En reimpresión');?></button>
				<?php endif;?>
					<button class="acc" rel="m_<?php echo $m['nIdLineaPedidoConcurso'];?>"><?php echo $this->lang->line('Ignorar');?></button>
				</td>
				</tr>
				<?php $odd = !$odd;?>
			<?php endforeach;?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="5" scope="row" align="right">
						<span id="contador"><?php echo count($datos);?></span>
					<?php echo $this->lang->line('registros');?>
					</td>
				</tr>
			</tfoot>
		</table>
<?php endif; ?>

<?php echo js_asset('jQuery/jquery.min.js');?>
<?php echo js_asset('jQuery/jquery-ui-1.9.2.custom.min.js');?>
<script type="text/javascript">
	$(function() {
		var contador = <?php echo count($datos);?>;
		jQuery('.acc').bind('click', function(item) {
			var v = item.currentTarget.attributes.getNamedItem('rel').value;
			var st = '';
			v = v.split('_');
			switch(v[0]) {
				case 'a':
					st = 'concursos/pedidoconcursolinea/agotado';
					break;
				case 'd':
					st = 'concursos/pedidoconcursolinea/descatalogado';
					break;
				case 'r':
					st = 'concursos/pedidoconcursolinea/reimpresion';
					break;
				case 'm':
					st = 'concursos/concurso/estado_visto';
					break;
			}
			if (st!= '') {
				parent.Ext.app.callRemote({
					url: parent.site_url(st),
					params: {
						id: v[1],
					},
					fnok: function()
					{
						--contador;
						$('#contador').html(contador);
						jQuery('#t_' + v[1]).fadeOut('slow');
					}
				});
			}
			return;
		});
	});
</script>
