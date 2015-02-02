<?php $this->load->helper('extjs');?>
<?php $this->load->helper('asset');?>
<?php echo css_asset('icons.css', 'main');?>
<?php echo css_asset('jquery-ui-1.9.2.custom.css');?>
<h1><?php echo $this->lang->line('Obras concurso');?>
	<strong><?php echo $concurso['cDescripcion'];?></strong>-
<span id="contador"><?php echo count($datos);?></span>
			<?php echo $this->lang->line('registros');?></h1>

<?php if (count($datos) > 0):?>
	<?php foreach($datos as $m):?>
		<table class="sortable-onload-shpw-0 rowstyle-alt colstyle-alt" id="t_<?php echo $m['nIdLineaPedidoConcurso'];?>">
			</caption>
			<thead>
				<tr><th colspan="2"><?php echo $m['cTitulo']; ?></th></tr>
			</thead>
			<tbody>
				<tr><th colspan="2"><?php echo $this->lang->line('Asignado');?></th></tr>
				<tr>
					<th align="left"><?php echo $this->lang->line('Id');?></th>
					<td align="left">
					<?php echo format_enlace_cmd($m['nIdLibro'], site_url('catalogo/articulo/index/' . $m['nIdLibro']));?>
					</td>
				</tr>
				<tr class="alt">
					<th align="left"><?php echo $this->lang->line('cTitulo');?></th>
					<td align="left" style="color:green;"><?php echo $m['cTitulo2']; ?></td>
				</tr>
				<tr>
					<th align="left"><?php echo $this->lang->line('cAutores');?></th>
					<td align="left"><?php echo $m['cAutores2']; ?></td>
				</tr>
				<tr class="alt">
					<th align="left"><?php echo $this->lang->line('cISBN');?></th>
					<td align="left"><?php echo $m['cISBN2']; ?></td>
				</tr>
				<tr>
					<th align="left"><?php echo $this->lang->line('cProveedor');?></th>
					<td align="left" style="color:orange;"><?php echo $m['cProveedor']; ?></td>
				</tr>
				<tr><th colspan="2"><?php echo $this->lang->line('Original');?></th></tr>
				<?php if (isset($m['cElxurro'])):?>
					<?php $xurro = unserialize($m['cElxurro']); ?>
					<?php $odd = FALSE; ?>
					<?php foreach($xurro as $r => $v):?>
						<tr <?php if ($odd):?> class="alt" <?php endif;?>>
							<th align="left"><?php echo $r;?></th>
							<td align="left"><?php echo $v;?></td>
						</tr>
						<?php $odd = !$odd;?>
					<?php endforeach;?>
				<?php else: ?>
					<tr>
						<td colspan="2"><?php echo $this->lang->line('NO HAY Pedido Original');?></td>
					</tr>
				<?php endif; ?>
			</tbody>
	<tfoot>
		<tr>
			<td colspan="2" scope="row" align="right">
				<button class="acc" rel="<?php echo $m['nIdLineaPedidoConcurso'];?>"><?php echo $this->lang->line('Marcar');?></button>
			</td>
		</tr>
	</tfoot>
		</table>
	<?php endforeach;?>

<?php endif; ?>

<?php echo js_asset('jQuery/jquery.min.js');?>
<?php echo js_asset('jQuery/jquery-ui-1.9.2.custom.min.js');?>
<script type="text/javascript">
	$(function() {
		var contador = <?php echo count($datos);?>;
		jQuery('.acc').bind('click', function(item) {
			var v = item.currentTarget.attributes.getNamedItem('rel').value;
			parent.Ext.app.callRemote({
				url: "<?php echo site_url('concursos/concurso/obra_vista');?>",
				params: {
					id: v,
				},
				fnok: function()
				{
					--contador;
					$('#contador').html(contador);
					jQuery('#t_' + v).fadeOut('slow');
				}
			});
			return;
		});
	});
</script>
