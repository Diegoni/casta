<?php $this->load->helper('extjs');?>
<?php $this->load->helper('asset');?>
<?php echo css_asset('icons.css', 'main');?>
<?php echo css_asset('jquery-ui-1.9.2.custom.css');?>
<?php if (count($libros) > 0):?>

<table 
	summary="<?php echo $this->lang->line('Editoriales');?>">
	<caption>
		<?php echo $this->lang->line('Editoriales');?>
	</caption>
	<thead>
		<tr>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Bueno');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Malo');?></th>
			<th>#</th>
		</tr>
	</thead>
	<tbody>
		<?php $count = 0; ?>
		<?php foreach($libros as $libro):?>
			<tr id="recibido_<?php echo $count;?>">
				<td>
					
					<strong><?php echo $libro['cTituloBueno'];?></strong><br/>
						<span style="color: grey;"><?php echo $libro['cAutoresBueno'];?>
					</span>
				</td>
				<td>
				<strong><?php echo $libro['cTituloMalo'];?></strong><br/>
						<span style="color: grey;"><?php echo $libro['cAutoresMalo'];?></span>
				<span id="recibido_2_<?php echo $count;?>" style="visibility:hidden;position:absolute;top:0;right:0;"><?php echo $libro['nIdBueno'];?>
				</span>
				<span id="recibido_3_<?php echo $count;?>" style="visibility:hidden;position:absolute;top:0;right:0;"><?php echo $libro['nIdMalo'];?>
				</span>
				</td>
				<td align="center">
					<button class="acc" rel="<?php echo $count;?>"><?php echo $this->lang->line('unificar');?></button>
				</td>
			</tr>
		<?php ++$count; ?>
		<?php endforeach;?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="4" scope="row" align="right"><?php echo count($libros);?>
			<?php echo $this->lang->line('registros');?></td>
		</tr>
	</tfoot>
</table>
<?php endif; ?>

<?php echo js_asset('jQuery/jquery.min.js');?>
<?php echo js_asset('jQuery/jquery-ui-1.9.2.custom.min.js');?>
<script type="text/javascript">
	$(function() {
		jQuery('.acc').bind('click', function(item) {
			var v = item.currentTarget.attributes.getNamedItem('rel').value;
			var v2 = jQuery('#recibido_2_' + v).html();
			var v3 = jQuery('#recibido_3_' + v).html();
			parent.Ext.app.callRemote({
				url: "<?php echo site_url('catalogo/articulo/unificar');?>",
				params: {
					id1: parseInt(v2),
					id2: parseInt(v3)
				},
				fnok: function()
				{
					jQuery('#recibido_' + v).fadeOut('slow');
				}
			});
			return;
		});
	});
</script>
