<?php $this->load->helper('extjs');?>
<?php $this->load->library('HtmlFile'); ?>
<?php echo get_instance()->htmlfile->orientation(ORIENTATION_LANDSCAPE); ?>
<div style='page-break-after: always;'>
<table class="sortable-onload-shpw-0 rowstyle-alt colstyle-alt no-arrow"
summary="<?php echo $this->lang->line('a-entregar-totales');?>">
	<caption>
		<?php echo $this->lang->line('a-entregar-totales');?>
	</caption>
	<thead>
		<tr>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Sección');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Cant.');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Imp.1');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Imp.2');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Imp.3');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Imp.4');?></th>
		</tr>
	</thead>
	<tbody>
		<?php $odd = FALSE;?>
		<?php $firme1 = $firme2 = $firme3 = $firme4 = $cantidad = 0;?>
		<?php foreach($total as $m):
		?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td><?php echo($m['cNombre']);?></td>
			<td align="right"><?php echo (isset($m['cantidad']))?format_number($m['cantidad']):'&nbsp;';
			?></td>
			<td align="right"><?php echo (isset($m['firme1']))?format_price($m['firme1']):'&nbsp;';
			?></td>
			<td align="right"><?php echo (isset($m['firme2']))?format_price($m['firme2']):'&nbsp;';
			?></td>
			<td align="right"><?php echo (isset($m['firme3']))?format_price($m['firme3']):'&nbsp;';
			?></td>
			<td align="right"><?php echo (isset($m['firme4']))?format_price($m['firme4']):'&nbsp;';
			?></td>
		</tr>
		<?php $odd = !$odd;?>
		<?php $firme1 += $m['firme1'];
			$firme2 += $m['firme2'];
			$firme3 += $m['firme3'];
			$firme4 += $m['firme4'];
			$cantidad += $m['cantidad'];
			$codigos[$m['nIdSeccion']] = $m['cNombre'];
		?>
		<?php endforeach;?>
	</tbody>
	<tfoot>
		<tr>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td>&nbsp;</td>
			<td align="right"><?php echo (isset($cantidad))?format_number($cantidad):'&nbsp;';?></td>
			<td align="right"><?php echo (isset($firme1))?format_price($firme1):'&nbsp;';?></td>
			<td align="right"><?php echo (isset($firme2))?format_price($firme2):'&nbsp;';?></td>
			<td align="right"><?php echo (isset($firme3))?format_price($firme3):'&nbsp;';?></td>
			<td align="right"><?php echo (isset($firme4))?format_price($firme4):'&nbsp;';?></td>
		</tr>
		</tr>
	</tfoot>
</table>
</div>
<?php if (isset($devoluciones)):?>
<div style='page-break-after: always;'>
<table class="sortable-onload-shpw-0 rowstyle-alt colstyle-alt no-arrow"
summary="<?php echo $this->lang->line('a-entregar-devoluciones');?>">
	<caption>
		<?php echo $this->lang->line('a-entregar-devoluciones');?>
	</caption>
	<thead>
		<tr>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('nIdDevolucion');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Proveedor');?></th>
			<th class="sortable-date-dmy" scope="col"><?php echo $this->lang->line('dCierre');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Cantidad');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Imp.1');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Imp.2');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Imp.3');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Imp.4');?></th>
		</tr>
	</thead>
	<tbody>
		<?php $odd = FALSE;?>
		<?php $cantidad = $firme1 = $firme2 = $firme3 = $firme4 = 0;?>
		<?php $tcantidad = $tfirme1 = $tfirme2 = $tfirme3 = $tfirme4 = 0;?>
		<?php foreach($devoluciones as $k => $m):
		?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td><?php echo format_enlace_cmd($k, site_url('compras/devolucion/index/' . $k));?></td>
			<td><?php echo format_name($m['cNombre'], $m['cApellido'], $m['cEmpresa']);?></td>
			<td><?php echo format_date($m['dCierre']);?></td>
			<td align="right"><?php echo format_number($m['cantidad']);?></td>
			<td align="right"><?php echo (isset($m['firme1']))?format_price($m['firme1']):'&nbsp;';
			?></td>
			<td align="right"><?php echo (isset($m['firme2']))?format_price($m['firme2']):'&nbsp;';
			?></td>
			<td align="right"><?php echo (isset($m['firme3']))?format_price($m['firme3']):'&nbsp;';
			?></td>
			<td align="right"><?php echo (isset($m['firme4']))?format_price($m['firme4']):'&nbsp;';
			?></td>
		</tr>
		<?php $odd = !$odd;?>
		<?php
			$cantidad += $m['cantidad']; 
			$firme1 += $m['firme1'];
			$firme2 += $m['firme2'];
			$firme3 += $m['firme3'];
			$firme4 += $m['firme4'];
		?>
		<?php endforeach;?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="3">&nbsp;</td>
			<td align="right"><?php echo format_number($cantidad);?></td>
			<td align="right"><?php echo (isset($firme1))?format_price($firme1):'&nbsp;';?></td>
			<td align="right"><?php echo (isset($firme2))?format_price($firme2):'&nbsp;';?></td>
			<td align="right"><?php echo (isset($firme3))?format_price($firme3):'&nbsp;';?></td>
			<td align="right"><?php echo (isset($firme4))?format_price($firme4):'&nbsp;';?></td>
		</tr>
	</tfoot>
</table>
</div>
<?php endif; ?>
<?php if (isset($lineas)):?>
	<div style='page-break-after: always;'>
<table class="sortable-onload-shpw-0 rowstyle-alt colstyle-alt no-arrow"
summary="<?php echo $this->lang->line('a-entregar-lineas');?>">
	<caption>
		<?php echo $this->lang->line('a-entregar-lineas');?>
	</caption>
	<thead>
		<tr>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Sección');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('ISBN');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Título');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('nIdDevolucion');?></th>
			<th class="sortable-date-dmy" scope="col"><?php echo $this->lang->line('dCierre');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('fCoste');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Cantidad');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Imp.1');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Imp.2');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Imp.3');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Imp.4');?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($lineas as $k => $grupo):?>
		<tr >
			<td colspan="11"><?php echo $codigos[$k];?></td>
		</tr>
		<?php $odd = FALSE;?>
		<?php $cantidad = $firme1 = $firme2 = $firme3 = $firme4 = 0;?>
		<?php $tcantidad = $tfirme1 = $tfirme2 = $tfirme3 = $tfirme4 = 0;?>
		<?php foreach($grupo as $m):
		?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td><?php echo format_name($m['cNombre'], $m['cApellido'], $m['cEmpresa']);?></td>
			<td><?php echo($m['cISBN']);?></td>
			<td><?php echo format_enlace_cmd($m['cTitulo'], site_url('catalogo/articulo/index/' . $m['nIdLibro']));?></td>
			<td><?php echo format_enlace_cmd($m['nIdDevolucion'], site_url('compras/devolucion/index/' . $m['nIdDevolucion']));?></td>
			<td><?php echo format_date($m['dCierre']);?></td>
			<td align="right"><?php echo (isset($m['fCoste']))?format_price($m['fCoste']):'&nbsp;';
			?></td>
			<td align="right"><?php echo format_number($m['nCantidad']);?></td>
			<td align="right"><?php echo (isset($m['firme1']))?format_price($m['firme1']):'&nbsp;';
			?></td>
			<td align="right"><?php echo (isset($m['firme2']))?format_price($m['firme2']):'&nbsp;';
			?></td>
			<td align="right"><?php echo (isset($m['firme3']))?format_price($m['firme3']):'&nbsp;';
			?></td>
			<td align="right"><?php echo (isset($m['firme4']))?format_price($m['firme4']):'&nbsp;';
			?></td>
		</tr>
		<?php $odd = !$odd;?>
		<?php
			$cantidad += $m['nCantidad']; 
			$firme1 += $m['firme1'];
			$firme2 += $m['firme2'];
			$firme3 += $m['firme3'];
			$firme4 += $m['firme4'];
			$tcantidad += $m['nCantidad']; 
			$tfirme1 += $m['firme1'];
			$tfirme2 += $m['firme2'];
			$tfirme3 += $m['firme3'];
			$tfirme4 += $m['firme4'];
		?>
		<?php endforeach;?>
		<tr>
			<td colspan="6">&nbsp;</td>
			<td align="right"><?php echo format_number($cantidad);?></td>
			<td align="right"><?php echo (isset($firme1))?format_price($firme1):'&nbsp;';?></td>
			<td align="right"><?php echo (isset($firme2))?format_price($firme2):'&nbsp;';?></td>
			<td align="right"><?php echo (isset($firme3))?format_price($firme3):'&nbsp;';?></td>
			<td align="right"><?php echo (isset($firme4))?format_price($firme4):'&nbsp;';?></td>
		</tr>
		<?php endforeach;?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="6">&nbsp;</td>
			<td align="right"><?php echo format_number($tcantidad);?></td>
			<td align="right"><?php echo (isset($tfirme1))?format_price($tfirme1):'&nbsp;';?></td>
			<td align="right"><?php echo (isset($tfirme2))?format_price($tfirme2):'&nbsp;';?></td>
			<td align="right"><?php echo (isset($tfirme3))?format_price($tfirme3):'&nbsp;';?></td>
			<td align="right"><?php echo (isset($tfirme4))?format_price($tfirme4):'&nbsp;';?></td>
		</tr>
	</tfoot>
</table>
</div>
<?php endif; ?>
