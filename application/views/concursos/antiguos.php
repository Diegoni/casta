<?php $this->load->library('HtmlFile'); ?>
<?php echo get_instance()->htmlfile->orientation(ORIENTATION_LANDSCAPE); ?>
<h1><?php echo $this->lang->line('Libros a recoger de tienda');?> - 
	<strong><?php echo $concurso['cDescripcion'];?></strong>
<?php foreach($datos as $secc => $data):?>
	<div style='page-break-after: always;'>
		<h2><?php echo $secc;?> - <?php echo count($data);?>
			<?php echo $this->lang->line('registros');?></h2>
		<table class="sortable-onload-shpw-0 rowstyle-alt colstyle-alt">
			<thead>
				<tr>
					<th align="left"><?php echo $this->lang->line('Días');?></th>
					<th align="left"><?php echo $this->lang->line('Id');?></th>
					<th align="left"><?php echo $this->lang->line('cAutores');?></th>
					<th align="left"><?php echo $this->lang->line('cTitulo');?></th>
					<th align="left"><?php echo $this->lang->line('Stock');?></th>
					<th align="left"><?php echo $this->lang->line('Materias');?></th>
					<th align="left"><?php echo $this->lang->line('Ubicación');?></th>
					<th align="left"><?php echo $this->lang->line('dUltimaVenta');?></th>
				</tr>
			</thead>
			<tbody>
			<?php $odd = FALSE; ?>
			<?php foreach($data as $m):?>
				<tr <?php if ($odd):?> class="alt" <?php endif;?>>
					<?php $dias = isset($m['dUltimaVenta'])?datediff('d', $m['dUltimaVenta'], time(), TRUE):-1; ?>
					<?php #var_dump($dias); die(); ?>
					<td align="left"><?php if ($dias > 0):?>
						<?php echo $dias;?>
					<?php else: ?>
						<?php echo $this->lang->line('NUNCA');?>
					<?php endif; ?>
					</td>
					<td align="left">
						<?php echo format_enlace_cmd($m['nIdLibro'], site_url('catalogo/articulo/index/' . $m['nIdLibro']));?>
					</td>
					<td align="left"><?php echo $m['cAutores'];?></td>
					<td align="left">
						<?php if (($m['nStockFirme']+$m['nStockDeposito'] >1) || ($dias == -1) || ($dias > 180)) :?>
							<strong><?php echo $m['cTitulo'];?></strong>
						<?php else: ?>
							<?php echo $m['cTitulo'];?>
						<?php endif; ?>
					</td>
					<td align="left"><?php echo $m['nStockFirme']+$m['nStockDeposito'];?></td>
					<td align="left"><?php echo $m['materias'];?></td>
					<td align="left"><?php echo $m['ubicaciones'];?></td>
					<td align="left"><?php echo isset($m['dUltimaVenta'])?format_date($m['dUltimaVenta']):'';?></td>
				</tr>
				<?php $odd = !$odd;?>
			<?php endforeach;?>
			</tbody>
		</table>
	</div>
<?php endforeach;?>

