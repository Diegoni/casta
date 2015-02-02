<?php $this->load->helper('asset');?>
<table class="sortable-onload-shpw-0 rowstyle-alt colstyle-alt no-arrow"
summary="<?php echo $title;?>">
	<caption>
		<strong><?php echo $title;?></strong>
	</caption>
	<thead>
		<tr>			
			<th class="sortable" scope="col"><?php echo $this->lang->line('#');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Artículo');?></th>
			<th class="sortable-currency" scope="col"><?php echo $this->lang->line('Precio');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Url');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('#');?></th>
		</tr>
	</thead>
	<tbody>
		<?php $m = array_pop($titulos); ?>
		<tr>
			<td><?php echo $this->lang->line('Local');?></td>
			<td>
				<?php echo format_enlace_cmd($m['libro']['nIdLibro'], site_url('catalogo/articulo/index/' . $m['libro']['nIdLibro']));?>
				<?php echo $m['libro']['cTitulo'];?><br/>
				<?php echo $m['libro']['cISBN'];?> | <?php echo $m['libro']['cAutores'];?>
			</td>
			<td align="right" style="color:blue;"><?php echo (isset($m['libro']['fPVP']))?format_price($m['libro']['fPVP']):'&nbsp;';?></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<?php $odd = FALSE;?>
		<?php foreach ($m['precios'] as $key => $value):?>

		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td nowrap="nowrap"><?php echo $key;?>&nbsp;<img width="16" src="<?php echo $value['icon'];?>" \></td>
			<td><?php echo isset($value['title'])?$value['title']:'&nbsp;';?></td>
			<td align="right">
					<?php if (isset($value['price'])):?>
						<span class="<?php echo ($value['price'] < $m['libro']['fPVP'])?'cell-precio-down':(($value['price'] > $m['libro']['fPVP'])?'cell-precio-up':'cell-precio-idem');?>">
						<?php echo format_price((float)$value['price']);?>
					</span>
					<?php else: ?>
						&nbsp;
					<?php endif; ?>
			</td>
			<td><?php if (isset($value['url'])): ?>
				<a class="cmd-link" src="<?php echo $value['url'];?>"><?php echo $this->lang->line('Página Web');?></a>
			<?php else: ?>
				&nbsp;
			<?php endif; ?>
			</td>
			<td>
				<small>(<?php echo round($value['time'],4);?>s)</small>
			</td>
		</tr>
		<?php $odd = !$odd;?>
		<?php endforeach;?>
	</tbody>
</table>

<?php echo js_asset('jQuery/jquery.min.js');?>
<script type="text/javascript">
	$(function() {
		jQuery('.cmd-link').bind('click', function(e, item) {
			console.dir(item);
			console.log($(item).src);
				parent.Ext.app.addTabJSONHTMLFILE({
						html_file : item.src,
						icon : 'iconoWebTab',
						title : '<?php echo $title;?>'
					});
			});
			return;
		});
</script>
