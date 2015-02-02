<?php $this->load->helper('extjs');?>
<?php $this->load->helper('asset');?>
<?php echo css_asset('icons.css', 'main');?>
<?php echo css_asset('jquery-ui-1.9.2.custom.css');?>
<?php if (count($editoriales) > 0):?>

<table 
	summary="<?php echo $this->lang->line('Editoriales');?>">
	<caption>
		<?php echo $this->lang->line('Editoriales');?>
	</caption>
	<thead>
		<tr>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Concurso');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Ct');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Editorial');?></th>
			<th>#</th>
		</tr>
	</thead>
	<tbody>
		<?php $count = 0; ?>
		<?php foreach($editoriales as $editorial):?>
			<tr id="recibido_<?php echo $count;?>">
				<td>
					<span style="color: green;">
					<strong  id="recibido_1_<?php echo $count;?>"><?php echo $editorial['cEditorial'];?></strong>
					</span>
					<span id="recibido_4_<?php echo $count;?>"></span>
				</td>
				<td>
					<?php echo $editorial['nContador'];?>
				</td>
				<td>
				<div class="ui-widget">
				    <input class="auto" rel="<?php echo $count;?>"/>
				</div>					
				<span id="recibido_2_<?php echo $count;?>" style="visibility:hidden;position:absolute;top:0;right:0;">
				</span>
				<span id="recibido_3_<?php echo $count;?>" style="visibility:hidden;position:absolute;top:0;right:0;">				
				</span>
				</td>
				<td align="center">
					<button class="acc" rel="<?php echo $count;?>"><?php echo $this->lang->line('asignar');?></button>
				</td>
			</tr>
		<?php ++$count; ?>
		<?php endforeach;?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="4" scope="row" align="right"><span id="contador"><?php echo count($editoriales);?></span>
			<?php echo $this->lang->line('registros');?></td>
		</tr>
	</tfoot>
</table>
<?php endif; ?>

<?php echo js_asset('jQuery/jquery.min.js');?>
<?php echo js_asset('jQuery/jquery-ui-1.9.2.custom.min.js');?>
<script type="text/javascript">
	$(function() {
		var contador = <?php echo count($editoriales);?>;
		jQuery('.acc').bind('click', function(item) {
			var v = item.currentTarget.attributes.getNamedItem('rel').value;
			var v1 = jQuery('#recibido_1_' + v).html();
			var v2 = jQuery('#recibido_2_' + v).html();
			var v3 = jQuery('#recibido_3_' + v).html();
			parent.Ext.app.callRemote({
				url: "<?php echo site_url('concursos/concurso/asignar_editorial');?>",
				params: {
					malo: v1,
					ed: parseInt(v2),
					pv: parseInt(v3)
				},
				fnok: function()
				{
					--contador;
					$('#contador').html(contador);
					jQuery('#recibido_' + v).fadeOut('slow');
				}
			});
			return;
		});
 		var cache = {};
        jQuery( ".auto" ).autocomplete({
            minLength: 4,
			select: function( event, ui ) {
				var v = event.target.attributes.getNamedItem('rel').value;
				jQuery('#recibido_2_' + v).html(ui.item.id);
				if (ui.item.name != '') {
					jQuery('#recibido_3_' + v).html(ui.item.proveedor);
					jQuery('#recibido_4_' + v).html('<br/>' + ("<?php echo $this->lang->line('proveedor-name');?>").replace('%s', ui.item.name));
				} else {
					jQuery('#recibido_3_' + v).html('');
					jQuery('#recibido_4_' + v).html('<br/>' + "<?php echo $this->lang->line('proveedor-name-no');?>");
				}
			},
            source: function( request, response ) {
                var term = request.term;
                if ( term in cache ) {
                    response( cache[ term ] );
                    return;
                }
 
                $.getJSON( "<?php echo site_url('concursos/concurso/search_editorial');?>", request, function( data, status, xhr ) {
                    cache[ term ] = data;
                    response( data );
                });
            }
        });
	});
</script>
