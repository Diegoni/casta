<?php $this->load->helper('extjs');?>
<table class="sortable-onload-shpw-0 rowstyle-alt colstyle-alt no-arrow"
summary="<?php echo $title;?>">
	<caption>
		<strong><?php echo $title;?></strong>
	</caption>
	<thead>
		<tr>			
			<th class="sortable" scope="col"><?php echo $this->lang->line('Artículo');?></th>
			<th class="sortable-currency" scope="col"><?php echo $this->lang->line('Local');?></th>
			<?php foreach ($motores as $key => $value):?>
				<th class="sortable-currency" scope="col"><?php echo $key;?> <img width="16" src="<?php echo $value;?>" \></th>
			<?php endforeach; ?>
		</tr>
	</thead>
	<tbody>
		<?php $odd = FALSE;?>
		<?php foreach($titulos as $m):
		?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td><?php if (isset($m['libro'])):?>
				<?php echo format_enlace_cmd($m['libro']['nIdLibro'], site_url('catalogo/articulo/index/' . $m['libro']['nIdLibro']));?>
				<?php echo $m['libro']['cTitulo'];?><br/>
				<?php echo $m['libro']['cISBN'];?> | <?php echo $m['libro']['cAutores'];?>
			<?php endif; ?>
			</td>
			<td align="right" style="color:blue;"><?php echo (isset($m['libro']['fPVP']))?format_price($m['libro']['fPVP']):'&nbsp;';?></td>
			<?php foreach ($motores as $key => $value):?>
				<td align="right">
					<?php if (isset($m['precios'][$key]['price'])):?>
						<span class="<?php echo ($m['precios'][$key]['price']<$m['libro']['fPVP'])?'cell-precio-down':'cell-precio-up';?>">
						<?php echo format_price((float)$m['precios'][$key]['price']);?>
					</span>
					<?php else: ?>
						&nbsp;
					<?php endif; ?>
				</td>
			<?php endforeach; ?>
		</tr>
		<?php $odd = !$odd;?>
		<?php endforeach;?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="9" scope="row"><?php echo count($titulos);?> <?php echo $this->lang->line('registros encontrados');?></td>
		</tr>
	</tfoot>
</table>
