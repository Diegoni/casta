<?php $this->load->helper('asset');?>
<?php $this->load->model('suscripciones/m_tiporeclamacion'); ?>
<?php echo js_asset('jQuery/jquery.min.js');?>
<?php echo js_asset('jQuery/jquery.hoverIntent.js');?>
<div class="data">
	<table width="100%">
		<tr>
			<th colspan="4" class="titulo">(<?php echo format_enlace_cmd($articulo['nIdLibro'], site_url('catalogo/articulo/index/' . $articulo['nIdLibro']))
			?>)<?php echo $articulo['cTitulo'];?>
			<?php if ($revista['nIdTipoSuscripcion'] == 5):
			?>
			<span class="obra"><?php echo $this->lang->line('OBRA');?></span><?php endif;?>			
			<?php if (isset($avisos)):?>
			<span class="aviso_renovacion">
			<?php echo $avisos[0]['cCampana'];?> - <?php echo $this->lang->line(isset($avisos[0]['nIdAvisoRenovacion']) ? (isset($avisos[0]['dGestionada']) ? ($avisos[0]['bAceptada'] ? 'ACEPTADA' : 'RECHAZADA') : 'SIN GESTIONAR') : 'NO HAY AVISO');?>
			</span><?php endif;?>
			<?php if ($bNoFacturable == 0):
			?>
			<?php if ($nEntradas > $nFacturas):
			?>
			<span class="est_sus_pend"> <?php echo $this->lang->line('PENDIENTE FACTURAR');?></span><?php endif;?>
			<?php if ($nEntradas < $nFacturas):
			?>
			<span class="est_sus_ant"> <?php echo $this->lang->line('ANTICIPADA');?>
				<?php echo format_enlace_cmd($nIdUltimaFactura, site_url('ventas/factura/index/' . $nIdUltimaFactura));?></span><?php endif;?>
			<?php endif;?></th>
		</tr>
		<tr>
			<td colspan="3"><?php
			$data[] = format_enlace_cmd($cliente['nIdCliente'], site_url('clientes/cliente/index/' . $cliente['nIdCliente']));
			$data[] = format_name($cliente['cNombre'], $cliente['cApellido'], $cliente['cEmpresa']);
			if (!empty($cliente['cNIF']))
				$data[] = $cliente['cNIF'];
			if (!empty($cliente['cTipoTarifa']))
				$data[] = '<strong>' . $cliente['cTipoTarifa'] . '</strong>';
			echo implode('<br/>', $data);
			?></td>
			<td colspan="1"><span class="precio"><?php echo format_price(isset($fPVP) ? $fPVP : 0);?>
				(<?php echo format_price($fPrecio);?>)<?php if ($fDescuento>0):
				?>
				<br/>
				<?php echo format_percent($fDescuento);?><?php endif;?></span></td>
		</tr>
		<tr>
			<td colspan="4">
			<div class="est_sus_<?php echo $bActiva;?>">
				<?php echo $this->lang->line(($bActiva) ? 'ACTIVA' : 'CANCELADA');?>
			</div></td>
		</tr>
		<tr valign="top">
			<td
			width="<?php echo $this->config->item('bp.catalogo.cover.articulo');?>"><?php echo format_cover($articulo['nIdLibro'], $this->config->item('bp.catalogo.cover.articulo'));?></td>
			<td>
			<table>
				<tr>
					<th class="label" colspan="2"><?php echo $this->lang->line('Datos');?></th>
				</tr>
				<tr>
					<td class="label"><?php echo $this->lang->line('Ejemplares');?>:</td>
					<td class="info"><?php echo isset($nEjemplares) ? $nEjemplares : '';?></td>
				</tr>
				<tr>
					<td class="label"><?php echo $this->lang->line('Duración');?>:</td>
					<td class="info"><?php echo isset($nDuracion) ? $nDuracion : '';?> <?php echo $this->lang->line('Años');?></td>
				</tr>
				<tr>
					<td class="label"><?php echo $this->lang->line('Creación');?>:</td>
					<td class="info"><?php echo isset($dCreacion) ? format_date($dCreacion) : '';?></td>
				</tr>
				<tr>
					<td class="label"><?php echo $this->lang->line('Primer pedido');?>:</td>
					<td class="info"><?php echo isset($dPrimerInicio) ? format_date($dPrimerInicio) : '';?></td>
				</tr>
				<tr>
					<td class="label"><?php echo $this->lang->line('Años suscritos');?>:</td>
					<td class="info"><?php echo (isset($nYears)?$nYears:0);?></td>
				</tr>
				<tr>
					<td class="label"><?php echo $this->lang->line('Actual periodo');?>:</td>
					<td class="info"><?php echo 'por hacer';?></td>
				</tr>
				<tr>
					<td class="label"><?php echo $this->lang->line('Renovación');?>:</td>
					<td class="info"><?php echo isset($dRenovacion) ? format_date($dRenovacion) : '';?></td>
				</tr>
				<tr>
					<td class="label"><?php echo $this->lang->line('Tipo de envío');?>:</td>
					<td class="info"><?php echo isset($cTipoEnvio) ? $cTipoEnvio : '';?></td>
				</tr>
			</table></td>
			<td>
			<table>
				<tr>
					<th class="label" colspan="2"><?php echo $this->lang->line('Referencias');?></th>
				</tr>
				<tr>
					<td class="label"><?php echo $this->lang->line('Proveedor');?></td>
					<td class="info"><?php echo $cRefProveedor;?></td>
				</tr>
				<tr>
					<td class="label"><?php echo $this->lang->line('Cliente');?></td>
					<td class="info"><?php echo $cRefCliente;?></td>
				</tr>
			</table><?php if (isset($proveedor)):
			?>
			<?php $data = format_name($proveedor['cNombre'], $proveedor['cApellido'], $proveedor['cEmpresa']);?>
			<?php echo format_enlace_cmd($proveedor['nIdProveedor'], site_url('proveedores/proveedor/index/' . $proveedor['nIdProveedor']));?>
			<br/>
			<?php echo $data;?>
			<?php endif;?></td>
			<td>
			<table>
				<tr>
					<th class="label" colspan="2"><?php echo $this->lang->line('Precios');?></th>
				</tr>
				<tr>
					<td class="label"><?php echo $this->lang->line('Precio de compra');?></td>
					<td align="right" class="info"><?php echo isset($fPrecioCompra) ? format_price($fPrecioCompra) : '&nbsp;';?></td>
				</tr>
				<tr>
					<td class="label"><?php echo $this->lang->line('Precio de la revista');?></td>
					<td align="right" class="info"><?php echo isset($fPrecioRevista) ? format_price($fPrecioRevista) : '&nbsp;';?></td>
				</tr>
				<tr>
					<td class="label"><?php echo $this->lang->line('PVP de la revista');?></td>
					<td align="right" class="info"><?php echo isset($fPVPRevista) ? format_price($fPVPRevista) : '&nbsp;';?></td>
				</tr>
				<tr>
					<td class="label"><?php echo $this->lang->line('Precio de la suscripción');?></td>
					<td align="right" class="info"><?php echo isset($fPrecio) ? format_price($fPrecio) : '&nbsp;';?></td>
				</tr>
				<tr>
					<td class="label"><?php echo $this->lang->line('PVP de la suscripción');?></td>
					<td align="right" class="info"><?php echo isset($fPVP) ? format_price($fPVP) : '&nbsp;';?></td>
				</tr>
			</table></td>
		</tr>
	</table>
	<br/>
	<table>
		<tr>
			<th colspan="2" class="seccion"><?php echo $this->lang->line('Direcciones');?></th>
		</tr>
		<tr>
			<th width="50%"><?php echo $this->lang->line('nIdDireccionEnvio');?>-<?php echo $this->lang->line($cTipoEnvio);?></th>
			<th width="50%"><?php echo $this->lang->line('nIdDireccionFactura');?></th>
		</tr>
		<tr>
			<td><?php echo isset($direccionenvio) ? format_address_print($direccionenvio) : $this->lang->line('SIN DIRECCIÓN');?></td>
			<td><?php echo isset($direccionfactura) ? format_address_print($direccionfactura) : $this->lang->line('SIN DIRECCIÓN');?></td>
		</tr>
	</table>
	<?php if (isset($pedidosproveedor)):
	?>
	<br />
	<table>
		<tr>
			<th colspan="17" class="seccion"><?php echo $this->lang->line('Pedidos Proveedor');?></th>
		</tr>
		<tr>
			<th><?php echo $this->lang->line('Id');?></th>
			<th><?php echo $this->lang->line('dCreacion');?></th>
			<th><?php echo $this->lang->line('dEntrega');?></th>
			<th><?php echo $this->lang->line('Estado Pedido');?></th>
			<th><?php echo $this->lang->line('Estado Línea');?></th>
			<th><?php echo $this->lang->line('Fact.Prv.');?></th>
			<th><?php echo $this->lang->line('dFecha');?></th>
			<th><?php echo $this->lang->line('Alb. Entr.');?></th>
			<th><?php echo $this->lang->line('dCreacion');?></th>
			<th><?php echo $this->lang->line('Año/Vol');?></th>
			<th><?php echo $this->lang->line('Cant.');?></th>
			<th><?php echo $this->lang->line('fPrecio');?></th>
			<th><?php echo $this->lang->line('fIVA');?></th>
			<th><?php echo $this->lang->line('Dto.');?></th>
			<th><?php echo $this->lang->line('fBase');?></th>
			<th><?php echo $this->lang->line('fTotal');?></th>
			<th><?php echo $this->lang->line('Cargos');?></th>
		</tr>
		<?php foreach ($pedidosproveedor as $seccion):
		?>
		<tr>
			<td class="label"><?php echo format_enlace_cmd($seccion['nIdPedido'], site_url('compras/pedidoproveedor/index/' . $seccion['nIdPedido']));?></td>
			<td><?php echo format_date($seccion['dCreacion']);?></td>
			<td><?php echo format_date($seccion['dFechaEntrega']);?></td>
			<td><?php echo $this->lang->line($seccion['cEstadoPedido']);?></td>
			<td><?php echo $this->lang->line($seccion['cEstadoLinea']);?></td>
			<td><?php echo $seccion['cNumeroAlbaran'];?></td>
			<td><?php echo format_date($seccion['dFecha']);?></td>
			<td><?php if ($seccion['nIdAlbaran']>0):?><?php echo format_enlace_cmd($seccion['nIdAlbaran'], site_url('compras/albaranentrada/index/' . $seccion['nIdAlbaran']));?><?php endif; ?></td>
			<td><?php echo format_date($seccion['dCreacionAlbaran']);?></td>
			<td><?php echo $seccion['cRefInterna'];?></td>
			<td class="number"><?php echo $seccion['nCantidad'];?></td>
			<td class="number"><?php echo format_price($seccion['fPrecio']);?></td>
			<td class="number"><?php echo format_percent($seccion['fIVA']);?></td>
			<td class="number"><?php echo format_percent($seccion['fDescuento']);?></td>
			<td class="number"><?php echo format_price($seccion['fBase']);?></td>
			<td class="number"><?php echo format_price($seccion['fTotal']);?></td>
			<td class="number"><?php echo format_price($seccion['Cargos']);?></td>
		</tr>
		<?php endforeach;?>
	</table>
	<?php endif;?>

	<?php if (isset($facturas)):
	?>
	<br />
	<table>
		<tr>
			<th colspan="11" class="seccion"><?php echo $this->lang->line('Facturas');?></th>
		</tr>
		<tr>
			<th><?php echo $this->lang->line('Albarán');?></th>
			<th><?php echo $this->lang->line('dCreacion');?></th>
			<th><?php echo $this->lang->line('Factura');?></th>
			<th><?php echo $this->lang->line('Fec.Fac.');?></th>
			<th><?php echo $this->lang->line('Año/Vol');?></th>
			<th><?php echo $this->lang->line('Cant.');?></th>
			<th><?php echo $this->lang->line('fPrecio');?></th>
			<th><?php echo $this->lang->line('fIVA');?></th>
			<th><?php echo $this->lang->line('Dto.');?></th>
			<th><?php echo $this->lang->line('fBase');?></th>
			<th><?php echo $this->lang->line('fTotal');?></th>
		</tr>
		<?php foreach ($facturas as $seccion):
		?>
		<tr>
			<td class="label"><?php echo format_enlace_cmd($seccion['nIdAlbaran'], site_url('ventas/albaransalida/index/' . $seccion['nIdAlbaran']));?></td>
			<td><?php echo format_date($seccion['dCreacion']);?></td>
			<td><?php $factura = isset($seccion['cFactura']) ? ($seccion['cFactura'] . '-' . $seccion['cNumeroSerie']) : $seccion['nIdFactura'];?>
			<?php echo(!empty($factura) ? format_enlace_cmd($factura, site_url('ventas/factura/index/' . $seccion['nIdFactura'])) : '');?></td>
			<td><?php echo format_date($seccion['dFecha']);?></td>
			<td><?php echo $seccion['cRefInterna'];?></td>
			<td class="number"><?php echo $seccion['nCantidad'];?></td>
			<td class="number"><?php echo format_price($seccion['fPrecio']);?></td>
			<td class="number"><?php echo format_percent($seccion['fIVA']);?></td>
			<td class="number"><?php echo format_percent($seccion['fDescuento']);?></td>
			<td class="number"><?php echo format_price($seccion['fBase']);?></td>
			<td class="number"><?php echo format_price($seccion['fTotal']);?></td>
		</tr>
		<?php endforeach;?>
	</table>
	<?php endif;?>
	<?php if (isset($presupuestos) && count($presupuestos) > 0):?>
	<br />
	<table>
	<tr>
		<th colspan="10" class="seccion"><?php echo $this->lang->line('Presupuestos');?>
		</th>
	</tr>
	<tr>
		<th><?php echo $this->lang->line('Id');?></th>
		<th><?php echo $this->lang->line('Fecha');?></th>
		<th><?php echo $this->lang->line('Estado');?></th>
		<th><?php echo $this->lang->line('Cliente');?></th>
		<th><?php echo $this->lang->line('Sección');?></th>
		<th><?php echo $this->lang->line('Referencia');?></th>
		<th><?php echo $this->lang->line('Cantidad');?></th>
		<th><?php echo $this->lang->line('Importe');?></th>
		<th><?php echo $this->lang->line('Descuento');?></th>
	</tr>
		<?php foreach ($presupuestos as $seccion):?>
		<tr>
			<td width="1" class="label"><?php echo format_enlace_cmd($seccion['id'], site_url('ventas/pedidocliente/index/' . $seccion['id']));?></td>
			<td class="text"><?php echo format_date($seccion['dFecha']);?></td>
			<td class="text"><?php echo $seccion['cEstado'];?></td>
			<td class="text"><?php echo format_enlace_cmd($seccion['nIdCl'], site_url('clientes/cliente/index/' . $seccion['nIdCl']));?>
			- <?php echo format_name($seccion['cNombre'], $seccion['cApellido'], $seccion['cEmpresa']);?></td>
			<td class="text"><?php echo $seccion['cSeccion'];?></td>
			<td><?php echo $seccion['cRefInterna'];?></td>
			<td class="text"><?php echo $seccion['nCantidad'];?></td>
			<td class="text"><?php echo format_price($seccion['fPrecio']);?></td>
			<td class="text"><?php echo format_percent($seccion['fDescuento']);?></td>
		</tr>
		<?php endforeach;?>
	</table>
	<?php endif; ?>

	<?php if (isset($reclamaciones)):
	?>
	<br />
	<table>
		<tr>
			<th colspan="15" class="seccion"><?php echo $this->lang->line('Reclamaciones');?></th>
		</tr>
		<tr>
			<th><?php echo $this->lang->line('Id');?></th>
			<th><?php echo $this->lang->line('dCreacion');?></th>
			<th><?php echo $this->lang->line('cDescripcion');?></th>
			<th><?php echo $this->lang->line('Destino');?></th>
			<th><?php echo $this->lang->line('Cliente');?></th>
			<th><?php echo $this->lang->line('Proveedor');?></th>
			<th><?php echo $this->lang->line('Envio');?></th>
			<th><?php echo $this->lang->line('cCUser');?></th>
			<th><?php echo $this->lang->line('cAUser');?></th>
			<th><?php echo $this->lang->line('dAct');?></th>
		</tr>
		<?php foreach ($reclamaciones as $seccion):
		?>
		<tr>
			<td class="label"><?php echo format_enlace_cmd($seccion['nIdReclamacion'], site_url('suscripciones/reclamacion/index/' . $seccion['nIdReclamacion']));?></td>
			<td><?php echo format_date($seccion['dCreacion']);?></td>
			<td><strong><?php echo $seccion['cTipoReclamacion'];?></strong>
				<?php if ($seccion['bCancelada']):?>
				<br/><div style='color:red'><?php echo $this->lang->line('CANCELADA'); ?></div>
				<?php endif; ?>
			</td>
			<td><div style='color:green'><?php echo $this->lang->line($seccion['nIdDestino'] == 1 ? 'Proveedor' : 'Cliente');?></div></td>
			<td><?php echo format_enlace_cmd($seccion['cCliente'], site_url('clientes/cliente/index/' . $seccion['nIdCliente']));?></td>
			<td><?php echo format_enlace_cmd($seccion['cProveedor'], site_url('proveedores/proveedor/index/' . $seccion['nIdProveedor']));?></td>
			<td><?php if (isset($seccion['dEnvio'])):?><?php echo format_date($seccion['dEnvio']);?><br/><?php endif; ?>
			<?php echo format_enlace_cmd('['. $this->lang->line(isset($seccion['dEnvio'])?'Reenviar':'Enviar') .']', site_url('suscripciones/reclamacion/send/' . $seccion['nIdReclamacion']));?> 
			<?php if (!isset($seccion['dEnvio'])):?><?php echo format_enlace_cmd('['. $this->lang->line('Marcar Enviada') .']', site_url('suscripciones/reclamacion/enviada/' . $seccion['nIdReclamacion']));?> <?php endif; ?>
			<?php if (!$seccion['bCancelada']):?> 
			<?php if (($seccion['nIdTipoReclamacion'] == TIPORECLAMACION_RECLAMACIONCLIENTE) && (!isset($seccion['nIdReclamacionAsociada']))):?> 
			<?php echo format_enlace_cmd('['. $this->lang->line('Respuesta') .']', site_url('suscripciones/suscripcion/respuesta/' . $seccion['nIdReclamacion']));?>
			<?php endif; ?>
			<?php echo format_enlace_cmd('['. $this->lang->line('Cancelar') .']', site_url('suscripciones/reclamacion/cancelar/' . $seccion['nIdReclamacion']));?>
			<?php endif;?>
		</td>
			<td><?php echo $seccion['cCUser'];?></td>
			<td><?php echo $seccion['cAUser'];?></td>
			<td><?php echo format_date($seccion['dAct']);?></td>
		</tr>
		<?php endforeach;?>
	</table>
	<?php endif;?>

	<?php if (isset($avisos)):
	?>
	<br />
	<table>
		<tr>
			<th colspan="15" class="seccion"><?php echo $this->lang->line('Avisos renovación');?></th>
		</tr>
		<tr>
			<th><?php echo $this->lang->line('Campaña');?></th>
			<th><?php echo $this->lang->line('Estado');?></th>
			<th><?php echo $this->lang->line('Enviada');?></th>
			<th><?php echo $this->lang->line('Gestionada');?></th>
			<th><?php echo $this->lang->line('F. Cliente');?></th>
			<th><?php echo $this->lang->line('Contacto');?></th>
			<th><?php echo $this->lang->line('Medio');?></th>
			<th>&nbsp;</th>
		</tr>
		<?php foreach ($avisos as $seccion):
		?>
		<tr>
			<td class="label"><?php echo $seccion['cCampana'];?></td>
			<td <?php if (!isset($seccion['nIdAvisoRenovacion'])):?>colspan="7"<?php endif;?>><?php echo $this->lang->line(isset($seccion['nIdAvisoRenovacion']) ? (isset($seccion['dGestionada']) ? ($seccion['bAceptada'] ? 'ACEPTADA' : 'RECHAZADA') : 'SIN GESTIONAR') : 'NO HAY AVISO');?></td>
			<?php if (isset($seccion['nIdAvisoRenovacion'])):
			?>
			<td><?php echo format_date($seccion['dEnviada']);?></td>
			<td><?php echo format_date($seccion['dGestionada']);?></td>
			<td><?php echo format_date($seccion['dFecha']);?></td>
			<td><?php echo $seccion['cPersona'];?></td>
			<td><?php echo $this->lang->line($seccion['cMedio']);?></td>
			<?php if (!$seccion['dGestionada']):
			?>
			<td><?php echo format_enlace_cmd($this->lang->line('Renovar'), site_url('suscripciones/avisorenovacion/aceptar/' . $seccion['nIdAvisoRenovacion']), null, $cmpid);?>| <?php echo format_enlace_cmd($this->lang->line('Cancelar'), site_url('suscripciones/avisorenovacion/cancelar/' . $seccion['nIdAvisoRenovacion']), null, $cmpid);?></td>
			<?php else:?>
			<td>&nbsp;</td>
			<?php endif;?>

			<?php endif;?>
		</tr>
		<?php endforeach;?>
	</table>
	<?php endif;?>
</div>
