<?php $fecha1 = str_replace('/', '-', $fecha1);?>
<?php $fecha2 = str_replace('/', '-', $fecha2);?>
<table
	summary="<?php echo $this->lang->line('report-ventas-exentas-iva');?> <?php echo $fecha1; ?> &lt;-&gt; <?php echo $fecha2; ?>">
	<caption><?php echo $this->lang->line('report-ventas-exentas-iva');?> <?php echo $fecha1; ?>
	&lt;-&gt; <?php echo $fecha2; ?></caption>
	<thead>
		<tr>
			<th scope="col"><?php echo $this->lang->line('Concepto');?></th>
			<th scope="col"><?php echo $this->lang->line('Valor');?></th>
		</tr>
	</thead>
	<tbody>
	<?php $odd = FALSE;?>
	<?php foreach($valores as $k => $v):?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td><?php if (isset($v['nIdGrupoIva'])):?> <a
				href="javascript:parent.Ext.app.execCmd({url: '<?php echo site_url('oltp/oltp/ventas_sin_iva/'.urlencode($fecha1).'/'.urlencode($fecha2).'/'.$v['nIdGrupoIva']); ?>'});">
				<?php echo $this->lang->line($k);?></a> <?php else:?> <?php echo $this->lang->line($k);?>
				<?php endif;?></td>
			<td align="right"><?php echo format_price($v['base']);?></td>
		</tr>
		<?php $odd = !$odd;?>
		<?php endforeach;?>
	</tbody>
</table>