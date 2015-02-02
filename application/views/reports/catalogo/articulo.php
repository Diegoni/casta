<?php $this->load->helper('asset');?>
<div class="data">
<table width="100%">
	<tr>
		<th colspan="5" class="titulo">(<?php echo $nIdLibro;?>) <?php echo $cTitulo;?> <span class="tipoarticulo_<?php echo $nIdTipo;?>"><?php echo $tipo['cDescripcion'];?></span>
		</th>
	</tr>
	<tr>
		<td colspan="5"><span class="autores"><?php echo $cAutores;?></span> <span
			class="precio"> <?php echo format_price($fPVP);?> (<?php echo format_price($fPrecio);?>)
			<?php if (isset($nIdOferta)):?> <br />
		<span class="precio-oferta"><?php echo format_price($fPrecioOriginal);?></span>
		</span> <?php endif;?></td>
	</tr>
	<tr valign="top">
		<td
			width="<?php echo $this->config->item('bp.catalogo.cover.articulo');?>">
			<img src="<?php echo site_url("sys/codebar/out/{$nIdLibro}/15");?>" />
			<br/><?php echo format_cover($nIdLibro, $this->config->item('bp.catalogo.cover.articulo'));?>			
		</td>
		<td>
		<table>
			<tr>
				<th class="label" colspan="2"><?php echo $this->lang->line('report-Datos');?></th>
			</tr>
			<tr>
				<td class="label"><?php echo $this->lang->line('report-ISBN');?>:</td>
				<td class="info"><?php echo isset($cISBN)?$cISBN:'';?>&nbsp; <?php echo isset($cISBN10)?$cISBN10:'';?>&nbsp;</td>
			</tr>
			<tr>
				<td class="label"><?php echo $this->lang->line('report-EAN');?>:</td>
				<td class="info"><?php echo isset($nEAN)?$nEAN:'';?>&nbsp;</td>
			</tr>
			<tr>
				<td class="label"><?php echo $this->lang->line('report-Estado');?>:</td>
				<td class="info"><?php echo $cEstado;?></td>
			</tr>

			<tr>
				<td class="label"><?php echo $this->lang->line('report-Proveedor');?>:</td>
				<td class="info"><?php echo $cProveedor;?>&nbsp;</td>
			</tr>
			<tr>
				<td class="label"><?php echo $this->lang->line('report-Editorial');?>:</td>
				<td class="info"><?php echo $cEditorial;?>&nbsp;</td>
			</tr>
			<tr>
				<td class="label"><?php echo $this->lang->line('report-Paginas');?>:</td>
				<td class="info"><?php echo $nPag;?>&nbsp;</td>
			</tr>
			<tr>
				<td class="label"><?php echo $this->lang->line('report-Colección');?>:</td>
				<td class="info"><?php echo $cColeccion;?>&nbsp; <?php echo isset($cNColeccion)? ' # ' . $cNColeccion:'';?>
				</td>
			</tr>
			<?php if ($show_tarifas):?>
			<tr>
				<td class="label"><?php echo $this->lang->line('report-Coste');?>:</td>
				<td class="info"><?php echo format_price($fPrecioCompra);?></td>
			</tr>
			<tr>
				<td class="label"><?php echo $this->lang->line('report-Margen');?>:</td>
				<td class="info"><?php echo format_percent(format_margen($fPrecio, $fPrecioCompra));?></td>
			</tr>
			<?php endif; ?>
			<?php if (isset($nIdOferta)):?>
			<tr>
				<td class="label"><?php echo $this->lang->line('report-Oferta');?>:</td>
				<td class="info"><?php echo $oferta['cDescripcion'];?></td>
			</tr>
			<?php endif;?>
			<?php if (isset($revista['nIdPeriodo']) || isset($revista['nIdTipoPeriodoRevista']) || isset($revista['nIdTipoSuscripcion'])):?>
			<tr>
				<th class="label" colspan="2"><?php echo $this->lang->line('report-Datos Publicación');?></th>
			</tr>
			<tr>
				<td class="label"><?php echo $this->lang->line('report-nIdTipoPeriodoRevista');?>:</td>
				<td class="info"><?php echo isset($revista['cPeriodoSuscripcion'])?$revista['cPeriodoSuscripcion']:'';?>&nbsp;</td>
			</tr>
			<tr>
				<td class="label"><?php echo $this->lang->line('report-nIdPeriodo');?>:</td>
				<td class="info"><?php echo isset($revista['cPeriodoSuscripcion'])?$revista['cPeriodoSuscripcion']:'';?>&nbsp;</td>
			</tr>
			<tr>
				<td class="label"><?php echo $this->lang->line('report-nIdTipoPeriodoRevista');?>:</td>
				<td class="info"><?php echo isset($revista['cTipoPeriodoRevista'])?$revista['cTipoPeriodoRevista']:'';?>&nbsp;</td>
			</tr>
			<tr>
				<td class="label"><?php echo $this->lang->line('report-nIdTipoSuscripcion');?>:</td>
				<td class="info"><?php echo isset($revista['cTipoSuscripcionRevista'])?$revista['cTipoSuscripcionRevista']:'';?>&nbsp;</td>
			</tr>
			<tr>
				<td class="label"><?php echo $this->lang->line('report-nEjemplares');?>:</td>
				<td class="info"><?php echo isset($revista['nEjemplares'])?$revista['nEjemplares']:'';?>&nbsp;</td>
			</tr>
			<tr>
				<td class="label"><?php echo $this->lang->line('report-bRenovable');?>:</td>
				<td class="info"><?php echo $this->lang->line(($revista['bRenovable'] == 1)?'report-RENOVABLE':'report-NO RENOVABLE');?></td>
			</tr>
			<?php endif; ?>
		</table>
		</td>
		<?php if ($show_tarifas):?>
		<td>
		<table>
			<tr>
				<th class="label" colspan="3"><?php echo $this->lang->line('report-Tarifas');?></th>
			</tr>
			<?php foreach ($tarifas as $t):?>
			<tr>
				<td class="label"><?php echo $t['cDescripcion'];?>:</td>
				<td class="info"><?php echo format_price($t['fPrecio']);?></td>
				<td class="info"><?php echo format_price(format_add_iva($t['fPrecio'], $fIVA));?></td>
			</tr>
			<?php endforeach; ?>
		</table>
		</td>
		<?php endif; ?>
	</tr>
</table>
		<?php if ($show_ventas):?>
<table>
	<tr>
		<td>
		<table>
			<tr>
				<th class="label" colspan="2"><?php echo $this->lang->line('report-Ventas');?></th>
			</tr>

			<tr>
				<td class="label"><?php echo $this->lang->line('report-Semana');?></td>
				<td class="info"><?php echo $t_semana;?></td>
			</tr>
			<tr>
				<td class="label"><?php echo $this->lang->line('report-Mes');?></td>
				<td class="info"><?php echo $t_mes;?></td>
			</tr>
			<tr>
				<td class="label"><?php echo $this->lang->line('report-3 Meses');?></td>
				<td class="info"><?php echo $t_mes3;?></td>
			</tr>
			<tr>
				<td class="label"><?php echo $this->lang->line('report-6 Meses');?></td>
				<td class="info"><?php echo $t_mes6;?></td>
			</tr>
			<tr>
				<td class="label"><?php echo $this->lang->line('report-12 Meses');?></td>
				<td class="info"><?php echo $t_mes12;?></td>
			</tr>
			<tr>
				<td class="label"><?php echo $this->lang->line('report-24 Meses');?></td>
				<td class="info"><?php echo $t_mes24;?></td>
			</tr>
		</table>
		</td>
		<td>
		<table>
			<tr>
				<th class="label" colspan="2"><?php echo $this->lang->line('report-Últimos documentos');?></th>
			</tr>
			<?php if (isset($ult_docs_general['entrada'])):?>
			<tr>
				<td class="label"><?php echo $this->lang->line('report-Entrada');?></td>
				<td class="info"><?php echo isset($ult_docs_general['entrada']['dCreacion'])?format_date($ult_docs_general['entrada']['dCreacion']):'&nbsp;';?>
				<?php echo isset($ult_docs_general['entrada']['dFecha'])?
				('(' .$this->lang->line('report-F.Alb.') . format_date($ult_docs_general['entrada']['dFecha']) .')'):'&nbsp;';?><br />
				<?php echo $ult_docs_general['entrada']['nIdAlbaran'];?> - <?php echo format_name($ult_docs_general['entrada']['cNombre']);?>
				</td>
			</tr>
			<?php endif; ?>
			<?php if (isset($ult_docs_general['salida'])):?>
			<tr>
				<td class="label"><?php echo $this->lang->line('report-Salida');?></td>
				<td class="info"><?php echo isset($ult_docs_general['salida'])?format_date($ult_docs_general['salida']):'&nbsp;';?></td>
			</tr>
			<?php endif; ?>
			<?php if (isset($ult_docs_general['devolucion'])):?>
			<tr>
				<td class="label"><?php echo $this->lang->line('report-Devolución');?></td>
				<td class="info"><?php echo isset($ult_docs_general['devolucion'])?format_date($ult_docs_general['devolucion']):'&nbsp;';?></td>
			</tr>
			<?php endif; ?>
			<?php if (isset($ult_docs_general['pendiente'])):?>
			<tr>
				<td class="label"><?php echo $this->lang->line('report-A Recibir');?></td>
				<td class="info"><?php echo isset($ult_docs_general['pendiente'])?format_date($ult_docs_general['pendiente']):'&nbsp;';?></td>
			</tr>
			<?php endif; ?>
			<?php if (isset($ult_docs_general['apedir'])):?>
			<tr>
				<td class="label"><?php echo $this->lang->line('report-A Pedir');?></td>
				<td class="info"><?php echo isset($ult_docs_general['apedir'])?format_date($ult_docs_general['apedir']):'&nbsp;';?></td>
			</tr>
			<?php endif; ?>
			<?php if (isset($fPrecioProveedor)):?>
			<tr>
				<td class="label"><?php echo $this->lang->line('report-Últ. Precio Prv.');?></td>
				<td class="info"><?php echo format_price($fPrecioProveedor, FALSE). ' ' . $divisa['cSimbolo']. ' (' . format_date($dFechaPrecioProveedor) .')';?></td>
			</tr>
			<?php endif; ?>
		</table>
		</td>
	</tr>
</table>
<?php endif;?> <?php if ($show_notas && (count($notas)>0)):?> <br />
<table>
	<tr>
		<th colspan="10" class="seccion"><?php echo $this->lang->line('report-Comentarios');?>
		</th>
	</tr>
	<tr>
		<th><?php echo $this->lang->line('report-Tipo');?></th>
		<th><?php echo $this->lang->line('report-Comentario');?></th>
		<th><?php echo $this->lang->line('report-Fecha');?></th>
		<th><?php echo $this->lang->line('report-Usuario');?></th>
	</tr>

	<?php foreach ($notas as $nota):?>
	<tr>
		<td><?php echo $nota['cTipo'];?></td>
		<td><strong><?php echo $nota['tObservacion'];?></strong></td>
		<td><?php echo format_date($nota['dCreacion']);?></td>
		<td><?php echo $nota['cCUser'];?></td>
	</tr>

	<?php endforeach;?>
</table>
<?php endif;?> <?php if (($show_secciones && isset($secciones))):?> <br />
<table>
	<tr>
		<th colspan="11" class="seccion"><?php echo $this->lang->line('report-Secciones');?>
		</th>
	</tr>
	<tr>
		<th><?php echo $this->lang->line('report-Sección');?></th>
		<th><?php echo $this->lang->line('report-Disponible');?></th>
		<th><?php echo $this->lang->line('report-Firme');?></th>
		<th><?php echo $this->lang->line('report-Depósito');?></th>
		<th><?php echo $this->lang->line('report-Recibir');?></th>
		<th><?php echo $this->lang->line('report-A Pedir');?></th>
		<th><?php echo $this->lang->line('report-A Servir');?></th>
		<th><?php echo $this->lang->line('report-Reservado');?></th>
		<th><?php echo $this->lang->line('report-A Devolver');?></th>
		<th><?php echo $this->lang->line('report-Mínimo');?></th>
		<th><?php echo $this->lang->line('Máximo');?></th>
	</tr>

	<?php foreach ($secciones as $seccion):?>
	<tr>
		<td class="label"><?php echo $seccion['cNombre'];?> (<?php echo $seccion['nIdSeccion'];?>)</td>
		<td class="number"><?php echo $seccion['nStockDisponible'];?></td>
		<td class="number"><?php echo $seccion['nStockFirme'];?></td>
		<td class="number"><?php echo $seccion['nStockDeposito'];?></td>
		<td class="number"><?php echo $seccion['nStockRecibir'];?></td>
		<td class="number"><?php echo $seccion['nStockAPedir'];?></td>
		<td class="number"><?php echo $seccion['nStockServir'];?></td>
		<td class="number"><?php echo $seccion['nStockReservado'];?></td>
		<td class="number"><?php echo $seccion['nStockADevolver'];?></td>
		<td class="number"><?php echo $seccion['nStockMinimo'];?></td>
			<td class="number"><?php echo $seccion['nStockMaximo'];?></td>
	</tr>

	<?php endforeach;?>
</table>
<?php endif;?> <?php if (($show_materias && isset($materias))):?> <br />
<table>
	<tr>
		<th colspan="10" class="seccion"><?php echo $this->lang->line('report-Materias');?>
		</th>
	</tr>
	<tr>
		<th><?php echo $this->lang->line('report-Id');?></th>
		<th><?php echo $this->lang->line('report-Código');?></th>
		<th><?php echo $this->lang->line('report-Materia');?></th>
	</tr>
	<?php foreach ($materias as $seccion):?>
	<tr>
		<td width="1" class="label"><?php echo $seccion['nIdMateria'];?></td>
		<td class="text"><?php echo $seccion['cCodMateria'];?></td>
		<td class="text"><?php echo $seccion['cNombre'];?></td>
	</tr>

	<?php endforeach;?>
</table>
<?php endif; ?> <?php if ($show_proveedores && isset($proveedores_all)):?>
<br />

<table>
	<tr>
		<th colspan="10" class="seccion"><?php echo $this->lang->line('report-Proveedores');?>
		</th>
	</tr>
	<tr>
		<th><?php echo $this->lang->line('report-Id');?></th>
		<th><?php echo $this->lang->line('report-Proveedor');?></th>
		<th><?php echo $this->lang->line('report-Origen');?></th>
		<th><?php echo $this->lang->line('report-Descuento');?></th>
		<th><?php echo $this->lang->line('report-Días');?></th>
	</tr>
	<?php foreach ($proveedores_all as $seccion):?>
	<tr>
		<td width="1" class="label"><?php echo $seccion['nIdProveedor'];?></td>
		<td class="text"><?php if ($seccion['default']):?> <strong>* <?php endif;?><?php echo $seccion['text'];?>
		<?php if ($seccion['default']):?> </strong> <?php endif;?></td>
		<td class="text"><?php echo $this->lang->line('report-origen_proveedor_' .$seccion['origen']);?></td>
		<td width="1" class="number"><?php echo format_percent($seccion['fDescuento']);?></td>
		<td width="1" class="number"><?php echo $seccion['nDiasEnvio'];?></td>
	</tr>

	<?php endforeach;?>
</table>
<?php endif; ?> <?php if (($show_pedidos) && (isset($pedidos_proveedor) || isset($pedidos_cliente))):?>
<br />
<table>
<?php if (isset($pedidos_proveedor) && count($pedidos_proveedor) > 0):?>
	<tr>
		<th colspan="10" class="seccion"><?php echo $this->lang->line('report-Pedidos proveedor pendientes');?>
		</th>
	</tr>
	<tr>
		<th><?php echo $this->lang->line('report-Id');?></th>
		<th><?php echo $this->lang->line('report-Fecha');?></th>
		<th><?php echo $this->lang->line('report-Estado');?></th>
		<th><?php echo $this->lang->line('report-Proveedor');?></th>
		<th><?php echo $this->lang->line('report-Sección');?></th>
		<th><?php echo $this->lang->line('report-Cantidad');?></th>
		<th><?php echo $this->lang->line('report-Importe');?></th>
		<th><?php echo $this->lang->line('report-Descuento');?></th>
	</tr>
	<?php foreach ($pedidos_proveedor as $seccion):?>
	<tr>
		<td width="1" class="label"><?php echo $seccion['id'];?></td>
		<td class="text"><?php echo format_date((isset($seccion['dFecha']) && $seccion['dFecha']!='')?$seccion['dFecha']:$seccion['dCreacion']);?></td>
		<td class="text"><?php echo $seccion['cEstado'];?></td>
		<td class="text"><?php echo $seccion['nIdClPv'];?> - <?php echo format_name($seccion['cNombre'], $seccion['cApellido'], $seccion['cEmpresa']);?></td>
		<td class="text"><?php echo $seccion['cSeccion'];?></td>
		<td class="text"><?php echo $seccion['nCantidad'];?></td>
		<td class="text"><?php echo format_price($seccion['fPrecio']);?></td>
		<td class="text"><?php echo format_percent($seccion['fDescuento']);?></td>
	</tr>

	<?php endforeach;?>
	<?php endif; ?>

	<?php if (isset($pedidos_cliente) && count($pedidos_cliente) > 0):?>
	<tr>
		<th colspan="10" class="seccion"><?php echo $this->lang->line('report-Pedidos cliente pendientes');?>
		</th>
	</tr>
	<tr>
		<th><?php echo $this->lang->line('report-Id');?></th>
		<th><?php echo $this->lang->line('report-Fecha');?></th>
		<th><?php echo $this->lang->line('report-Estado');?></th>
		<th><?php echo $this->lang->line('report-Cliente');?></th>
		<th><?php echo $this->lang->line('report-Sección');?></th>
		<th><?php echo $this->lang->line('report-Cantidad');?></th>
		<th><?php echo $this->lang->line('report-Importe');?></th>
		<th><?php echo $this->lang->line('report-Descuento');?></th>
	</tr>
	<?php foreach ($pedidos_cliente as $seccion):?>
	<tr>
		<td width="1" class="label"><?php echo $seccion['id'];?></td>
		<td class="text"><?php echo format_date($seccion['dFecha']);?></td>
		<td class="text"><?php echo $seccion['cEstado'];?></td>
		<td class="text"><?php echo $seccion['nIdClPv'];?> - <?php echo format_name($seccion['cNombre'], $seccion['cApellido'], $seccion['cEmpresa']);?></td>
		<td class="text"><?php echo $seccion['cSeccion'];?></td>
		<td class="text"><?php echo $seccion['nCantidad'];?></td>
		<td class="text"><?php echo format_price($seccion['fPrecio']);?></td>
		<td class="text"><?php echo format_percent($seccion['fDescuento']);?></td>
	</tr>

	<?php endforeach;?>
	<?php endif; ?>
</table>
<?php endif; ?> <?php if ($show_ubicaciones && (isset($ubicaciones) && (count($ubicaciones) > 0))):?>
<br />
<table>
	<tr>
		<th colspan="10" class="seccion"><?php echo $this->lang->line('report-Ubicaciones');?>
		</th>
	</tr>
	<tr>
		<th><?php echo $this->lang->line('report-Id');?></th>
		<th><?php echo $this->lang->line('report-Ubicación');?></th>
		<th><?php echo $this->lang->line('report-Fecha');?></th>
	</tr>
	<?php foreach ($ubicaciones as $seccion):?>
	<tr>
		<td width="1" class="label"><?php echo $seccion['nIdUbicacion'];?></td>
		<td class="text"><?php echo $seccion['cDescripcion'];?></td>
		<td class="text"><?php echo format_date($seccion['dAct']);?></td>
	</tr>
	<?php endforeach;?>
</table>
<?php endif; ?> <?php if ($show_sinopsis && isset($sinopsis['tSinopsis'])):?>
<br />
<table class="header">
	<tr>
		<th class="seccion"><?php echo $this->lang->line('report-Sinopsis');?></th>
	</tr>
	<tr>
		<td><?php echo str_replace("\n", '<br/>', $sinopsis['tSinopsis']);?></td>
	</tr>
</table>
<?php endif; ?></div>
