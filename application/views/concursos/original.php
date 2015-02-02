<?php $this->load->helper('extjs');?>
	<?php foreach($libros as $m):?>
<table class="sortable-onload-shpw-0 rowstyle-alt colstyle-alt"
	summary="<?php echo $this->lang->line('Pedido Original');?>">
	<caption><?php echo $this->lang->line('Pedido Original');?><br />
	</caption>
	<thead>
		<tr><th colspan="2"><?php echo $m['cTitulo']; ?></th></tr>
	</thead>
	<tbody>
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
</table>
	<?php endforeach;?>
