<?php $this->load->library('HtmlFile'); ?>
<?php echo get_instance()->htmlfile->orientation(ORIENTATION_LANDSCAPE); ?>
<?php foreach($secciones as $sec):?>
<?php if (isset($sec['seccion']) && is_array($sec['seccion'])):?>
<table width="100%">
	<caption><strong><?php echo $titulo; ?></strong></caption>
	<thead>
		<tr>
			<th colspan="7"><?php echo $sec['seccion']['cNombre'];?></th>
		</tr>
		<tr>
			<th><?php echo $this->lang->line('Id');?></th>
			<th width="100%"><?php echo $this->lang->line('Título');?></th>
			<th><?php echo $this->lang->line('Stock');?></th>
			<th><?php echo $this->lang->line('fPrecio');?></th>
			<th><?php echo $this->lang->line('fTotal');?></th>
			<th><?php echo $this->lang->line('Ubicación');?></th>
		</tr>
	</thead>
	<tbody>
	<?php $odd = FALSE;?>
	<?php $entradas = $salidas = 0; ?>
	<?php foreach($sec['lineas'] as $m):?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td><?php echo $m['nIdLibro'];?></td>
			<td><?php echo $m['cTitulo'];?><br/><?php echo $m['cISBN'];?></td>
			<td align="right"><?php echo format_number($m['nStockFirme'] + $m['nStockDeposito']); ?></td>
			<td align="right"><?php echo (isset($m['fPrecio']))?format_price($m['fPrecio']):'&nbsp;';?></td>
			<td align="right"><?php echo (isset($m['fPrecio']))?format_price($m['fPrecio'] * ($m['nStockFirme'] + $m['nStockDeposito'])):'&nbsp;';?></td>
			<td><?php
			$ar = array();
			foreach($m['ubicacion'] as $u)
			{				
				$ar[] = $u['cDescripcion'];
			}
			?><?php echo implode(', ', $ar);?></td>
		</tr>
		<?php $odd = !$odd;?>
		<?php endforeach;?>
	</tbody>
</table>
<div class="page-break"></div>
		<?php endif; ?>
		<?php endforeach; ?>
