<?php $this->load->helper('extjs');?>
<table class="sortable-onload-shpw-0 rowstyle-alt colstyle-alt no-arrow"
	summary="<?php echo $title;?>">
	<caption><?php echo $title;?><br />
	<strong><?php echo $articulo['cTitulo'];?></strong> <br />
	<?php echo $this->lang->line('Id');?> :<?php echo $articulo['id'];?><br />
	<?php if (isset($fecha1)):?> <?php echo $this->lang->line('Desde');?> :<?php echo format_date($fecha1);?><br />
	<?php endif;?> <?php if (isset($fecha2)):?> <?php echo $this->lang->line('Hasta');?>
	:<?php echo format_date($fecha2);?><br />
	<?php endif;?></caption>
	<thead>
		<tr>
			<th colspan="2" class="sortable-date-dmy" scope="col"><?php echo $this->lang->line('Fecha');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Id');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Cliente');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Id Cliente');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('nIdSeccion');?></th>
			<th class="sortable-currency" scope="col"><?php echo $this->lang->line('fPrecio');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('fDescuento');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('nCantidad');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Estado');?></th>
		</tr>
	</thead>
	<tbody>
	<?php $odd = FALSE;?>
	<?php $entradas = $salidas = 0; ?>
	<?php foreach($docs as $m):?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td><?php echo format_date($m['dFecha']);?></td>
			<td><?php echo format_time($m['dFecha']);?></td>
			<td><?php echo format_enlace_documentos($m);?></td>
			<td><?php
			if (isset($m['nIdPv'])||isset($m['nIdCl'])) {
				echo format_enlace_cmd(format_name($m['cNombre'], $m['cApellido'], $m['cEmpresa']), site_url((isset($m['nIdPv']))? ('proveedores/proveedor/index/' . $m['nIdPv']):('clientes/cliente/index/' . $m['nIdCl'])));
			}
			else
			{
				echo '&nbsp';
			}
			?></td>
			<td><?php //echo extjs_command('mailing/mailing/index/ ' . $m['nIdMailing'], $m['nIdMailing']);?>
			<?php echo format_name($m['cNombre'], $m['cApellido'], $m['cEmpresa']);?></td>
			<td><?php echo (isset($m['cSeccion']))?$m['cSeccion']:'&nbsp;';?></td>
			<td align="right"><?php echo (isset($m['fPrecio']))?format_price($m['fPrecio']):'&nbsp;';?></td>
			<td align="right"><?php echo (isset($m['fDescuento']))?format_percent($m['fDescuento']):'&nbsp;';?></td>
			<td align="right"><?php echo (isset($m['fPrecio']))?format_number($m['nCantidad']):'&nbsp;';?></td>
			<td><?php echo (isset($m['cEstado']))?$this->lang->line($m['cEstado']):'&nbsp;';?></td>
		</tr>
		<?php $odd = !$odd;?>
		<?php endforeach;?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="10" scope="row" align="right"><?php echo count($docs);?>
			<?php echo $this->lang->line('registros');?></td>
		</tr>
	</tfoot>
</table>
