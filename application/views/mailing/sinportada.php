<?php $this->load->library('HtmlFile'); ?>
<?php echo get_instance()->htmlfile->orientation(ORIENTATION_LANDSCAPE); ?>
<script language="javascript">
            var fn_sinportadas = function(res, id){
                parent.Ext.app.callRemote({
                    url: parent.site_url('catalogo/articulo/set_cover'),
                    params: {
                        url: res.url,
                        id: id
                    },
                    fnok: function(){
                        // Refresca el elemento
                        /*if (el != null) {
                            try {
                                el.src = site_url('catalogo/articulo/cover/' + id + '/' + el.width + '?' + Ext.app.createId());
                            } 
                            catch (e) {
                                console.dir(e);
                            }
                        }*/
                    }
                });
            }
</script>

<table width="100%"
	class="sortable-onload-shpw-0 rowstyle-alt colstyle-alt">
	<caption><strong><?php echo $this->lang->line('Artículos sin portada en boletín'); ?></strong></caption>
	<thead>
		<tr>
			<th class="sortable-date-dmy" width="1%"><?php echo $this->lang->line('Id');?></th>
			<th class="sortable" width="1%"><?php echo $this->lang->line('Autores');?></th>
			<th class="sortable" width="1%"><?php echo $this->lang->line('Título');?></th>
			<th class="sortable" width="1%"><?php echo $this->lang->line('ISBN');?></th>
			<th class="sortable" width="90%"><?php echo $this->lang->line('EAN');?></th>
		</tr>
	</thead>
	<tbody>
	<?php $odd = FALSE;?>
	<?php foreach($articulos as $m):?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td nowrap="nowrap" width="1%"><?php echo format_enlace_cmd($m['nIdLibro'], site_url('catalogo/articulo/index/' . $m['nIdLibro']));?>
			</td>
			<td nowrap="nowrap" width="1%"><?php echo format_enlace_cmd($m['cAutores'], site_url('catalogo/articulo/index/' . $m['nIdLibro']));?>
			</td>
			<td nowrap="nowrap" width="90%"><?php
			$titulo = str_replace('\'', '\\\'', $m['cTitulo']);
			echo format_enlace_js($m['cTitulo'], "parent.searchPicture('{$titulo}', function(res) {fn_sinportadas(res, {$m['nIdLibro']})})");?>
			</td>
			<td nowrap="nowrap" width="1%"><?php
			echo format_enlace_js($m['cISBN'], "parent.searchPicture('{$m['cISBN']}', function(res) {fn_sinportadas(res, {$m['nIdLibro']})})");?>
			</td>
			<td nowrap="nowrap" width="1%"><?php
			echo format_enlace_js($m['nEAN'], "parent.searchPicture('{$m['nEAN']}', function(res) {fn_sinportadas(res, {$m['nIdLibro']})})");?>
			</td>
		</tr>
		<?php $odd = !$odd;?>
		<?php endforeach;?>
	</tbody>
</table>

