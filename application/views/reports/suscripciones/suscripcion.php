<?php $this->load->helper('asset');?>
<div class="data">
	<table width="100%">
		<tr>
			<th colspan="4" class="titulo">(<?php echo $articulo['nIdLibro'];
			?>)<?php echo $articulo['cTitulo'];?>
			<?php if ($revista['nIdTipoSuscripcion'] == 5):
			?>
			<span class="obra"><?php echo $this->lang->line('OBRA');?></span><?php endif;?></th>
		</tr>
		<tr>
			<td colspan="3"><?php
			$data[] = $cliente['nIdCliente'];
			$data[] = format_name($cliente['cNombre'], $cliente['cApellido'], $cliente['cEmpresa']);
			if (!empty($cliente['cNIF']))
				$data[] = $cliente['cNIF'];
			if (!empty($cliente['cTipoTarifa']))
				$data[] = '<strong>' . $cliente['cTipoTarifa'] . '</strong>';
			echo implode('<br/>', $data);
			?></td>
			<td colspan="1"><span class="precio"><?php echo format_price(isset($fPVP)?$fPVP:0);?>
				(<?php echo format_price($fPrecio);?>)</span></td>
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
					<td class="info"><?php echo $nYears;?></td>
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
			<?php echo $proveedor['nIdProveedor'];?>
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
			<th colspan="15" class="seccion"><?php echo $this->lang->line('Pedidos Proveedor');?></th>
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
			<th><?php echo $this->lang->line('Dto.');?></th>
			<th><?php echo $this->lang->line('fTotal');?></th>
			<th><?php echo $this->lang->line('Cargos');?></th>
		</tr>
		<?php foreach ($pedidosproveedor as $seccion):
		?>
		<tr>
			<td class="label"><?php echo $seccion['nIdPedido'];?></td>
			<td><?php echo format_date($seccion['dCreacion']);?></td>
			<td><?php echo format_date($seccion['dFechaEntrega']);?></td>
			<td><?php echo $this->lang->line($seccion['cEstadoPedido']);?></td>
			<td><?php echo $this->lang->line($seccion['cEstadoLinea']);?></td>
			<td><?php echo $seccion['cNumeroAlbaran'];?></td>
			<td><?php echo format_date($seccion['dFecha']);?></td>
			<td><?php echo $seccion['nIdAlbaran'];?></td>
			<td><?php echo format_date($seccion['dCreacionAlbaran']);?></td>
			<td><?php echo $seccion['cRefInterna'];?></td>
			<td class="number"><?php echo $seccion['nCantidad'];?></td>
			<td class="number"><?php echo format_price($seccion['fPrecio']);?></td>
			<td class="number"><?php echo format_percent($seccion['fDescuento']);?></td>
			<td class="number"><?php echo format_price($seccion['Total']);?></td>
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
			<th colspan="15" class="seccion"><?php echo $this->lang->line('Facturas');?></th>
		</tr>
		<tr>
			<th><?php echo $this->lang->line('Id');?></th>
			<th><?php echo $this->lang->line('dCreacion');?></th>
			<th><?php echo $this->lang->line('Factura');?></th>
			<th><?php echo $this->lang->line('Fec.Fac.');?></th>
			<th><?php echo $this->lang->line('Año/Vol');?></th>
			<th><?php echo $this->lang->line('Cant.');?></th>
			<th><?php echo $this->lang->line('fPrecio');?></th>
			<th><?php echo $this->lang->line('Dto.');?></th>
			<th><?php echo $this->lang->line('fTotal');?></th>
		</tr>
		<?php foreach ($facturas as $seccion):
		?>
		<tr>
			<td class="label"><?php echo $seccion['nIdAlbaran'];?></td>
			<td><?php echo format_date($seccion['dCreacion']);?></td>
			<td><?php $factura = isset($seccion['cFactura']) ? ($seccion['cFactura'] . '-' . $seccion['cNumeroSerie']) : $seccion['nIdFactura'];?>
			<?php echo (!empty($factura)?$factura:'');?></td>
			<td><?php echo format_date($seccion['dFecha']);?></td>
			<td><?php echo $seccion['cRefInterna'];?></td>
			<td class="number"><?php echo $seccion['nCantidad'];?></td>
			<td class="number"><?php echo format_price($seccion['fPrecio']);?></td>
			<td class="number"><?php echo format_percent($seccion['fDescuento']);?></td>
			<td class="number"><?php echo format_price($seccion['Total']);?></td>
		</tr>
		<?php endforeach;?>
	</table>
	<?php endif;?>

	<?php if (isset($reclamaciones)):
	?>
	<br />
	<table>
		<tr>
			<th colspan="15" class="seccion"><?php echo $this->lang->line('Reclamaciones');?></th>
		</tr>
		<tr>
			<th><?php echo $this->lang->line('Id');?></th>
			<th><?php echo $this->lang->line('cDescripcion');?></th>
			<th><?php echo $this->lang->line('Destino');?></th>
			<th><?php echo $this->lang->line('Cliente');?></th>
			<th><?php echo $this->lang->line('Proveedor');?></th>
			<th><?php echo $this->lang->line('Envio');?></th>
			<th><?php echo $this->lang->line('cCUser');?></th>
			<th><?php echo $this->lang->line('dCreacion');?></th>
			<th><?php echo $this->lang->line('cAUser');?></th>
			<th><?php echo $this->lang->line('dAct');?></th>
		</tr>
		<?php foreach ($reclamaciones as $seccion):
		?>
		<tr>
			<td class="label"><?php echo $seccion['nIdReclamacion'];?></td>
			<td><?php echo $seccion['cDescripcion'];?></td>
			<td><?php echo $this->lang->line($seccion['nIdDestino'] == 1 ? 'Proveedor' : 'Cliente');?></td>
			<td><?php echo$seccion['cCliente'];?></td>
			<td><?php echo$seccion['cProveedor'];?></td>
			<td><?php echo format_date($seccion['dEnvio']);?></td>
			<td><?php echo $seccion['cCUser'];?></td>
			<td><?php echo format_date($seccion['dCreacion']);?></td>
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
			<?php endif;?>
		</tr>
		<?php endforeach;?>
	</table>
	<?php endif;?>
</div>
