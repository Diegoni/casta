<?php $this->load->library('HtmlFile'); ?>
<?php echo get_instance()->htmlfile->orientation(ORIENTATION_LANDSCAPE); ?>
<?php $this->load->helper('extjs');?>
<table class="sortable-onload-shpw-0 rowstyle-alt colstyle-alt no-arrow"
	summary="<?php echo $titulo;?>">
	<caption><strong><?php echo $item;?></strong> <br />
	<?php echo $this->lang->line('Id');?> :<?php echo $id;?><br />
	<?php if (isset($fecha1)):?> <?php echo $this->lang->line('Desde');?> :<?php echo format_date($fecha1);?><br />
	<?php endif;?> <?php if (isset($fecha2)):?> <?php echo $this->lang->line('Hasta');?>
	:<?php echo format_date($fecha2);?><br />
	<?php endif;?></caption>
	<thead>
		<tr>
			<th colspan="2" class="sortable-date-dmy" scope="col"><?php echo $this->lang->line('Fecha');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Tipo');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('nIdSeccion');?></th>
			<th class="sortable" scope="col"><?php echo ($clpv)?$this->lang->line('Cl/Pv'):$this->lang->line('cTitulo');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Id');?></th>
			<th class="sortable-currency" scope="col"><?php echo $this->lang->line('P.');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Dto');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Ct');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('E');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('S');?></th>
		</tr>
	</thead>
	<tbody>
	<?php $odd = FALSE;?>
	<?php $entradas = $salidas = 0; ?>
	<?php foreach($docs as $m):?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td><?php echo format_date($m['dFecha']);?></td>
			<td><?php echo $m['cCUser'];?><br />
			<?php echo format_time($m['dFecha']);?></td>
			<td><?php echo $this->lang->line('doc_' . $m['tipo']);?><?php if (isset($m['cDescripcion'])):?>
			(<?php echo $m['cDescripcion'];?>)<?php endif;?></td>
			<td><?php echo (isset($m['cSeccion']))?$m['cSeccion']:'&nbsp;';?></td>
			<td><?php
			if ($clpv)
			{
				if (isset($m['nIdPv'])||isset($m['nIdCl'])) {
					echo format_enlace_cmd(format_name($m['cNombre'], $m['cApellido'], $m['cEmpresa']), site_url((isset($m['nIdPv']))? ('proveedores/proveedor/index/' . $m['nIdPv']):('clientes/cliente/index/' . $m['nIdCl'])));
				}
				else
				{
					echo '&nbsp';
				}
			}
			else
			{
				if (isset($m['cTitulo'])||isset($m['cTitulo']))
				{
					echo format_enlace_cmd($m['cTitulo'], site_url('catalogo/articulo/index/' . $m['nIdLibro']));
				}
				else
				{
					echo '&nbsp';
				}
			}
			?></td>
			<td><?php echo format_enlace_documentos($m);?></td>
			<td align="right"><?php echo (isset($m['fPrecio']))?format_price($m['fPrecio']):'&nbsp;';?></td>
			<td align="right"><?php echo (isset($m['fDescuento']))?format_percent($m['fDescuento']):'&nbsp;';?></td>
			<td align="right"><?php echo (!isset($m['ES']))?format_number($m['nCantidad']):'&nbsp;';?></td>
			<td align="right"><?php echo (isset($m['ES']) && ($m['ES'] == 1))?format_number($m['nCantidad']):'&nbsp;';?></td>
			<td align="right"><?php echo (isset($m['ES']) && ($m['ES'] == 0))?format_number($m['nCantidad']):'&nbsp;';?></td>
		</tr>
		<?php $odd = !$odd;?>
		<?php if ((isset($m['ES']) && ($m['ES'] == 1))) $entradas+=$m['nCantidad'];?>
		<?php if ((isset($m['ES']) && ($m['ES'] == 0))) $salidas+=$m['nCantidad'];?>
		<?php endforeach;?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="9" scope="row" align="right">&nbsp;</td>
			<td align="right"><?php echo format_number($entradas);?></td>
			<td align="right"><?php echo format_number($salidas);?></td>
		</tr>
		<tr>
			<td colspan="11" scope="row" align="right"><?php echo count($docs);?>
			<?php echo $this->lang->line('registros');?></td>
		</tr>
	</tfoot>
</table>
