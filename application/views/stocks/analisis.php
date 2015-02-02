<?php $this->load->helper('extjs');?>
<div class="data">
<table width="100%">
	<tr>
		<th colspan="5" class="titulo">(<?php echo $articulo['nIdLibro'];?>) <?php echo $articulo['cTitulo'];?>
		</th>
	</tr>
	<tr>
		<td colspan="5"><span class="autores"><?php echo $articulo['cAutores'];?></span>

		</td>
	</tr>
	<tr valign="top">
		<td
			width="<?php echo $this->config->item('bp.catalogo.cover.articulo');?>"><?php echo format_lightbox(format_cover($articulo['nIdLibro'], $this->config->item('bp.catalogo.cover.articulo'), 'portada'), format_url_cover($articulo['nIdLibro']));?></td>
		<td>
		<table>
			<tr>
				<th class="label" colspan="4"><?php echo $this->lang->line('Datos');?></th>
			</tr>
			<tr>
				<td class="label"><?php echo $this->lang->line('ISBN');?>:</td>
				<td class="info"><?php echo isset($articulo['cISBN'])?$articulo['cISBN']:'';?>&nbsp;
				<?php echo isset($articulo['cISBN10'])?$articulo['cISBN10']:'';?>&nbsp;</td>
				<td class="label"><?php echo $this->lang->line('fPrecio');?>:</td>
				<td class="info"><?php echo format_price($articulo['fPrecio']);?></td>
			</tr>
			<tr>
				<td class="label"><?php echo $this->lang->line('EAN');?>:</td>
				<td class="info"><?php echo isset($articulo['nEAN'])?$articulo['nEAN']:'';?>&nbsp;</td>
				<td class="label"><?php echo $this->lang->line('Coste');?>:</td>
				<td class="info"><?php echo format_price($articulo['fPrecioCompra']);?></td>
			</tr>
			<tr>
				<td class="label"><?php echo $this->lang->line('Proveedor');?>:</td>
				<td class="info"><?php echo $articulo['cProveedor'];?>&nbsp;</td>
				<td class="label"><?php echo $this->lang->line('Margen');?>:</td>
				<td class="info"><?php echo format_percent(format_margen($articulo['fPrecio'], $articulo['fPrecioCompra']));?></td>
			</tr>
			<tr>
				<td class="label"><?php echo $this->lang->line('Editorial');?>:</td>
				<td class="info"><?php echo $articulo['cEditorial'];?>&nbsp;</td>
				<td class="label"><?php echo $this->lang->line('fPVP');?>:</td>
				<td class="info"><?php echo format_price($articulo['fPVP']);?></td>
			</tr>
		</table>
		</td>
	</tr>
</table>
				<?php if (isset($articulo['ubicaciones']) && (count($articulo['ubicaciones']) > 0)):?>
<br />
<table>
	<tr>
		<th colspan="10" class="seccion"><?php echo $this->lang->line('Ubicaciones');?>
		</th>
	</tr>
	<tr>
		<th><?php echo $this->lang->line('Id');?></th>
		<th><?php echo $this->lang->line('Ubicación');?></th>
		<th><?php echo $this->lang->line('Fecha');?></th>
	</tr>
	<?php foreach ($articulo['ubicaciones'] as $seccion):?>
	<tr>
		<td width="1" class="label"><?php echo $seccion['nIdUbicacion'];?></td>
		<td class="text"><?php echo $seccion['cDescripcion'];?></td>
		<td class="text"><?php echo format_date($seccion['dAct']);?></td>
	</tr>
	<?php endforeach;?>
</table>
<?php endif; ?> <?php if (isset($contado)):?> <br />
<table>
	<tr>
		<th colspan="10" class="seccion"><?php echo $this->lang->line('Contado inventario');?>
		</th>
	</tr>
	<tr>
		<th scope="col"><?php echo $this->lang->line('Sección');?></th>
		<th scope="col"><?php echo $this->lang->line('Tipo');?></th>
		<th scope="col"><?php echo $this->lang->line('Cantidad');?></th>
	</tr>

	<?php foreach ($contado as $m):?>
	<tr>
		<td><?php echo $m['cNombre'];?></td>
		<td><?php echo $m['cDescripcion'];?></td>
		<td align="right"><?php echo format_number($m['nCantidad']);?></td>
	</tr>
	<?php endforeach;?>
</table>
<?php endif;?> <?php if (count($ajustesmas) > 0):?> <br />
<table>
	<tr>
		<th colspan="4" class="seccion"><?php echo $this->lang->line('Ajustado Positivo');?>
		</th>
	</tr>
	<tr>
		<tr>
			<th><?php echo $this->lang->line('Fecha');?></th>
			<th><?php echo $this->lang->line('Motivo');?></th>
			<th><?php echo $this->lang->line('F.');?></th>
			<th><?php echo $this->lang->line('D.');?></th>
		</tr>
	</tr>

	<?php foreach ($ajustesmas as $m):?>
	<tr>
		<td width="1%"><?php echo format_date($m['dCreacion']);?></td>
		<td nowrap="nowrap" width="35%"><?php echo $m['cMotivo'];?></td>
		<td width="1%" align="right"><?php echo (isset($m['nCantidadFirme']))?format_number($m['nCantidadFirme']):'&nbsp;';?></td>
		<td width="1%" align="right"><?php echo (isset($m['nCantidadDeposito']))?format_number($m['nCantidadDeposito']):'&nbsp;';?></td>
	</tr>
	<?php endforeach;?>
</table>
<?php endif;?> <?php if (count($ajustesmenos) > 0):?> <br />
<table>
	<tr>
		<th colspan="4" class="seccion"><?php echo $this->lang->line('Ajustado Negativo');?>
		</th>
	</tr>
	<tr>
		<tr>
			<th><?php echo $this->lang->line('Fecha');?></th>
			<th><?php echo $this->lang->line('Motivo');?></th>
			<th><?php echo $this->lang->line('F.');?></th>
			<th><?php echo $this->lang->line('D.');?></th>
		</tr>
	</tr>

	<?php foreach ($ajustesmenos as $m):?>
	<tr>
		<td width="1%"><?php echo format_date($m['dCreacion']);?></td>
		<td nowrap="nowrap" width="35%"><?php echo $m['cMotivo'];?></td>
		<td width="1%" align="right"><?php echo (isset($m['nCantidadFirme']))?format_number($m['nCantidadFirme']):'&nbsp;';?></td>
		<td width="1%" align="right"><?php echo (isset($m['nCantidadDeposito']))?format_number($m['nCantidadDeposito']):'&nbsp;';?></td>
	</tr>
	<?php endforeach;?>
</table>
	<?php endif;?> <br />
<table>
	<tr>
		<th class="seccion" colspan="11"><?php echo sprintf($this->lang->line('Stock-retrocedido-fechas'), $fechainventario, $fecharetroceso);?></th>
	</tr>
	<tr>
		<td class="label"><?php echo $this->lang->line('Total Firme');?>:</td>
		<td class="label"><?php echo $this->lang->line('Firme 1');?>:</td>
		<td class="label"><?php echo $this->lang->line('Firme 2');?>:</td>
		<td class="label"><?php echo $this->lang->line('Firme 3');?>:</td>
		<td class="label"><?php echo $this->lang->line('Firme 4');?>:</td>
		<td class="label"><?php echo $this->lang->line('Depósito');?>:</td>
		<td class="label"><?php echo $this->lang->line('Entradas 1');?>:</td>
		<td class="label"><?php echo $this->lang->line('Entradas 2');?>:</td>
		<td class="label"><?php echo $this->lang->line('Entradas 3');?>:</td>
		<td class="label"><?php echo $this->lang->line('Entradas 4');?>:</td>
	</tr>
	<tr>
		<td align="right" class="info"><?php echo isset($retrocedido['nTotalFirme'])?$retrocedido['nTotalFirme']:'';?>&nbsp;</td>
		<td align="right" class="info"><?php echo isset($retrocedido['nFirme1'])?$retrocedido['nFirme1']:'';?>&nbsp;</td>
		<td align="right" class="info"><?php echo isset($retrocedido['nFirme2'])?$retrocedido['nFirme2']:'';?>&nbsp;</td>
		<td align="right" class="info"><?php echo isset($retrocedido['nFirme3'])?$retrocedido['nFirme3']:'';?>&nbsp;</td>
		<td align="right" class="info"><?php echo isset($retrocedido['nFirme4'])?$retrocedido['nFirme4']:'';?>&nbsp;</td>
		<td align="right" class="info"><?php echo isset($retrocedido['nDeposito'])?$retrocedido['nDeposito']:'';?>&nbsp;</td>
		<td align="right" class="info"><?php echo isset($retrocedido['nEntradas1'])?$retrocedido['nEntradas1']:'';?>&nbsp;</td>
		<td align="right" class="info"><?php echo isset($retrocedido['nEntradas2'])?$retrocedido['nEntradas2']:'';?>&nbsp;</td>
		<td align="right" class="info"><?php echo isset($retrocedido['nEntradas3'])?$retrocedido['nEntradas3']:'';?>&nbsp;</td>
		<td align="right" class="info"><?php echo isset($retrocedido['nEntradas4'])?$retrocedido['nEntradas4']:'';?>&nbsp;</td>
	</tr>
</table>
	<?php if (isset($docs_retroceso)):?><br/>
<table>
	<tr>
		<th class="label" colspan="10"><?php echo sprintf($this->lang->line('Documentos retroceso'), $fecharetroceso, $fechainventario);?></th>
	</tr>
	<tr>
		<th><?php echo $this->lang->line('Fecha');?></th>
		<th><?php echo $this->lang->line('Tipo');?></th>
		<th><?php echo $this->lang->line('nIdSeccion');?></th>
		<th><?php echo $this->lang->line('Cl/Pv');?></th>
		<th><?php echo $this->lang->line('Id');?></th>
		<th><?php echo $this->lang->line('P.');?></th>
		<th><?php echo $this->lang->line('Dto');?></th>
		<th><?php echo $this->lang->line('Ct');?></th>
		<th><?php echo $this->lang->line('E');?></th>
		<th><?php echo $this->lang->line('S');?></th>
	</tr>
	<?php $entradas = $salidas = 0; ?>
	<?php foreach($docs_retroceso as $m):?>
	<tr>
		<td><?php echo format_date($m['dFecha']);?></td>
		<td><?php echo $this->lang->line('doc_' . $m['tipo']);?><?php if (isset($m['cDescripcion'])):?>
		(<?php echo $m['cDescripcion'];?>)<?php endif;?></td>
		<td><?php echo (isset($m['cSeccion']))?$m['cSeccion']:'&nbsp;';?></td>
		<td><?php if (isset($m['nIdPv'])||isset($m['nIdCl'])):?> <?php echo format_enlace_cmd(format_name($m['cNombre'], $m['cApellido'], $m['cEmpresa']), site_url((isset($m['nIdPv']))? ('proveedores/proveedor/index/' . $m['nIdPv']):('clientes/cliente/index/' . $m['nIdCl']))); ?>
		<?php else:?>&nbsp;<?php endif;?></td>
		<td><?php echo format_enlace_documentos($m);?></td>
		<td align="right"><?php echo (isset($m['fPrecio']))?format_price($m['fPrecio']):'&nbsp;';?></td>
		<td align="right"><?php echo (isset($m['fDescuento']))?format_percent($m['fDescuento']):'&nbsp;';?></td>
		<td align="right"><?php echo (!isset($m['ES']))?format_number($m['nCantidad']):'&nbsp;';?></td>
		<td align="right"><?php echo (isset($m['ES']) && ($m['ES'] == 1))?format_number($m['nCantidad']):'&nbsp;';?></td>
		<td align="right"><?php echo (isset($m['ES']) && ($m['ES'] == 0))?format_number($m['nCantidad']):'&nbsp;';?></td>
	</tr>
	<?php if ((isset($m['ES']) && ($m['ES'] == 1))) $entradas+=$m['nCantidad'];?>
	<?php if ((isset($m['ES']) && ($m['ES'] == 0))) $salidas+=$m['nCantidad'];?>
	<?php endforeach;?>
	<tr>
		<td colspan="8" scope="row" align="right">&nbsp;</td>
		<td align="right"><?php echo format_number($entradas);?></td>
		<td align="right"><?php echo format_number($salidas);?></td>
	</tr>
	<tr>
		<td colspan="10" scope="row" align="right"><?php echo count($docs_retroceso);?>
		<?php echo $this->lang->line('registros');?></td>
	</tr>
</table>
		<?php endif; ?>
<br/><table>
	<tbody>
		<tr>
			<th colspan="12" class="seccion"><?php echo sprintf($this->lang->line('Stock actual'), $ahora);?>
			</th>
		</tr>
		<tr>
			<th><?php echo $this->lang->line('Sección');?></th>
			<th><?php echo $this->lang->line('Firme');?></th>
			<th><?php echo $this->lang->line('Depósito');?></th>
			<th><?php echo $this->lang->line('Reservado');?></th>
			<th><?php echo $this->lang->line('A Devolver');?></th>
		</tr>
		<?php if (count($articulo['secciones'])>0):?>
		<?php foreach ($articulo['secciones'] as $seccion):?>
		<tr id="sec_<?php echo $seccion['id'];?>">
			<td class="label"><?php echo $seccion['cNombre'];?> (<?php echo $seccion['nIdSeccion'];?>)</td>
			<td class="number"><?php echo $seccion['nStockFirme'];?></td>
			<td class="number"><?php echo $seccion['nStockDeposito'];?></td>
			<td class="number"><?php echo $seccion['nStockReservado'];?></td>
			<td class="number"><?php echo format_enlace_cmd($seccion['nStockADevolver'], site_url('catalogo/articulo/devoluciones/' . $articulo['nIdLibro']));?></td>
		</tr>
		<?php endforeach;?>
		<?php endif; ?>
	</tbody>
</table>
	<?php if (isset($docs)):?><br/>
<table>
	<tr>
		<th class="label" colspan="10"><?php echo sprintf($this->lang->line('Documentos retroceso'),  $fechainventario, $ahora);?></th>
	</tr>
	<tr>
		<th><?php echo $this->lang->line('Fecha');?></th>
		<th><?php echo $this->lang->line('Tipo');?></th>
		<th><?php echo $this->lang->line('nIdSeccion');?></th>
		<th><?php echo $this->lang->line('Cl/Pv');?></th>
		<th><?php echo $this->lang->line('Id');?></th>
		<th><?php echo $this->lang->line('P.');?></th>
		<th><?php echo $this->lang->line('Dto');?></th>
		<th><?php echo $this->lang->line('Ct');?></th>
		<th><?php echo $this->lang->line('E');?></th>
		<th><?php echo $this->lang->line('S');?></th>
	</tr>
	<?php $entradas = $salidas = 0; ?>
	<?php foreach($docs as $m):?>
	<tr>
		<td><?php echo format_date($m['dFecha']);?></td>
		<td><?php echo $this->lang->line('doc_' . $m['tipo']);?><?php if (isset($m['cDescripcion'])):?>
		(<?php echo $m['cDescripcion'];?>)<?php endif;?></td>
		<td><?php echo (isset($m['cSeccion']))?$m['cSeccion']:'&nbsp;';?></td>
		<td><?php if (isset($m['nIdPv'])||isset($m['nIdCl'])):?> <?php echo format_enlace_cmd(format_name($m['cNombre'], $m['cApellido'], $m['cEmpresa']), site_url((isset($m['nIdPv']))? ('proveedores/proveedor/index/' . $m['nIdPv']):('clientes/cliente/index/' . $m['nIdCl']))); ?>
		<?php else:?>&nbsp;<?php endif;?></td>
		<td><?php echo format_enlace_documentos($m);?></td>
		<td align="right"><?php echo (isset($m['fPrecio']))?format_price($m['fPrecio']):'&nbsp;';?></td>
		<td align="right"><?php echo (isset($m['fDescuento']))?format_percent($m['fDescuento']):'&nbsp;';?></td>
		<td align="right"><?php echo (!isset($m['ES']))?format_number($m['nCantidad']):'&nbsp;';?></td>
		<td align="right"><?php echo (isset($m['ES']) && ($m['ES'] == 1))?format_number($m['nCantidad']):'&nbsp;';?></td>
		<td align="right"><?php echo (isset($m['ES']) && ($m['ES'] == 0))?format_number($m['nCantidad']):'&nbsp;';?></td>
	</tr>
	<?php if ((isset($m['ES']) && ($m['ES'] == 1))) $entradas+=$m['nCantidad'];?>
	<?php if ((isset($m['ES']) && ($m['ES'] == 0))) $salidas+=$m['nCantidad'];?>
	<?php endforeach;?>
	<tr>
		<td colspan="8" scope="row" align="right">&nbsp;</td>
		<td align="right"><?php echo format_number($entradas);?></td>
		<td align="right"><?php echo format_number($salidas);?></td>
	</tr>
	<tr>
		<td colspan="10" scope="row" align="right"><?php echo count($docs_retroceso);?>
		<?php echo $this->lang->line('registros');?></td>
	</tr>
</table>
		<?php endif; ?>
		
		</div>
