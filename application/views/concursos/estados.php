<?php foreach($estados as $m):?>
<h1><?php echo $m['libro']['cTitulo']; ?></h1>
<table class="sortable-onload-shpw-0 rowstyle-alt colstyle-alt" align="left">
	<thead>
		<tr><th colspan="3"><?php echo $this->lang->line('Cambios estado');?></th></tr>
	</thead>
	<tbody>
		<?php foreach($m['estados'] as $v):?>
		<tr>
			<td class="alt" align="left"><?php echo format_datetime($v['dCreacion']);?></td>
			<td align="left"><strong><?php echo $v['cDescripcion'];?></strong></td>
			<td align="left"><?php echo $v['cCUser'];?></td>
		</tr>
		<?php endforeach;?>
	</tbody>
</table>
<?php endforeach;?>
