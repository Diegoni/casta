<?php $this->load->helper('extjs');?>
<?php $this->load->helper('asset');?>
<div class="details-panel">
	<table width="100%">
		<tr valign="top">
			<td  valign="top">
			<table width="100%">
				<tr>
					<td class="label"><?php echo $this->lang->line('nIdSuscripcion')
					?>:</td>
					<td class="info"><?php echo format_enlace_cmd($nIdSuscripcion, site_url('suscripciones/suscripcion/index/' . $nIdSuscripcion));?>
					<?php if (($nEntradas < $nFacturas)):
					?>
					[<span style="color: red;"><b><?php echo $this->lang->line('ANTICIPADA');?></b> <?php echo format_enlace_cmd($nIdUltimaFactura, site_url('ventas/factura/index/' . $nIdUltimaFactura));?></span>]<?php endif;?>
					<?php if (($bNoFacturable)):
					?>
					[<span style="color: green;"><b><?php echo $this->lang->line('bNoFacturable');?></b></span>]<?php endif;?>					
					</td>
				</tr>
				<tr>
					<td class="label"><?php echo $this->lang->line('nIdCliente')
					?>:</td>
					<td class="info"><?php echo format_enlace_cmd(format_name($cliente['cNombre'], $cliente['cApellido'], $cliente['cEmpresa']), site_url('clientes/cliente/index/' . $nIdCliente));?></td>
				</tr>
				<tr>
					<td class="label"><?php echo $this->lang->line('Condiciones')
					?>:</td>
					<td class="info"><b><?php echo $cliente['cTipoTarifa'];?></b><?php
					if (count($cliente['descuentos']) > 0)
					{
						$dtos = array();
						foreach ($cliente['descuentos'] as $dto)
						{
							$dtos[] = $dto['cDescripcion'] . ' - ' . format_percent($dto['fValor']);
						}
						echo ' - ' . $this->lang->line('fDescuento') . ': ' . implode(', ', $dtos);
					}
					?></td>
				</tr>
				<tr>
					<td class="label"><?php echo $this->lang->line('Revista')
					?>:</td>
					<td class="info"><?php echo format_enlace_cmd($cTitulo, site_url('catalogo/articulo/index/' . $nIdLibro));?></td>
				</tr>
				<tr>
					<td class="label"><?php echo $this->lang->line('nIdProveedor')
					?>:</td>
					<td class="info"><?php echo format_enlace_cmd(format_name($proveedor['cNombre'], $proveedor['cApellido'], $proveedor['cEmpresa']), site_url('proveedores/proveedor/index/' . $nIdProveedor));?></td>
				</tr>
				<?php if (!empty($presupuesto)): ?>
					<tr>
						<td class="label"><?php echo $this->lang->line('Presupuesto')
						?>:</td>
						<td class="info"><?php echo format_enlace_cmd($presupuesto['id'], site_url('ventas/pedidocliente/index/' . $presupuesto['id']));?></td>
					</tr>
					<tr>
						<td class="label"><?php echo $this->lang->line('Precio')
						?>:</td>
						<td class="info"><?php echo format_price($presupuesto['fPVP']);?></td>
					</tr>
					<tr>
						<td class="label"><?php echo $this->lang->line('Descuento')
						?>:</td>
						<td class="info"><?php echo format_percent($presupuesto['fDescuento']);?></td>
					</tr>
				<?php endif; ?>

			</table></td>
			<td>
			<table>
				<?php if (isset($facturas)):
				?>
				<tr  valign="top">
					<td>
					<table>
						<tr class="label">
							<th colspan="15" class="seccion"><?php echo $this->lang->line('Facturas');?></th>
						</tr>
						<tr class="label">
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
						<?php $i = 0;?>
						<?php foreach ($facturas as $seccion):
						?>
						<tr>
							<td class="label"><?php echo format_enlace_cmd($seccion['nIdAlbaran'], site_url('ventas/albaransalida/index/' . $seccion['nIdAlbaran']));?></td>
							<td class="info"><?php echo format_date($seccion['dCreacion']);?></td>
							<td class="info"><?php $factura = isset($seccion['cFactura']) ? ($seccion['cFactura'] . '-' . $seccion['cNumeroSerie']) : $seccion['nIdFactura'];?>
							<?php echo format_enlace_cmd($factura, site_url('ventas/factura/index/' . $seccion['nIdFactura']));?></td>
							<td class="info"><?php echo format_date($seccion['dFecha']);?></td>
							<td class="info"><?php echo $seccion['cRefInterna'];?></td>
							<td class="number"><?php echo $seccion['nCantidad'];?></td>
							<td class="number"><?php echo format_price($seccion['fPrecio']);?></td>
							<td class="number"><?php echo format_percent($seccion['fDescuento']);?></td>
							<td class="number"><?php echo format_price($seccion['fTotal']);?></td>
						</tr>
						<?php endforeach;?>
					</table></td>
				</tr>
				<?php endif;?>
			</table></td>
		</tr>
	</table>
	<?php if (isset($pedidosproveedor)):
	?>
	<table>
		<tr class="label">
			<th colspan="15" class="seccion"><?php echo $this->lang->line('Pedidos Proveedor');?></th>
		</tr>
		<tr class="label">
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
			<td class="label"><?php echo format_enlace_cmd($seccion['nIdPedido'], site_url('compras/pedidoproveedor/index/' . $seccion['nIdPedido']));?></td>
			<td class="info"><?php echo format_date($seccion['dCreacion']);?></td>
			<td class="info"><?php echo format_date($seccion['dFechaEntrega']);?></td>
			<td class="info"><?php echo $this->lang->line($seccion['cEstadoPedido']);?></td>
			<td class="info"><?php echo $this->lang->line($seccion['cEstadoLinea']);?></td>
			<td class="info"><?php echo $seccion['cNumeroAlbaran'];?></td>
			<td class="info"><?php echo format_date($seccion['dFecha']);?></td>
			<td class="info"><?php echo format_enlace_cmd($seccion['nIdAlbaran'], site_url('compras/albaranentrada/index/' . $seccion['nIdAlbaran']));?></td>
			<td class="info"><?php echo format_date($seccion['dCreacionAlbaran']);?></td>
			<td class="info"><?php echo $seccion['cRefInterna'];?></td>
			<td class="number"><?php echo $seccion['nCantidad'];?></td>
			<td class="number"><?php echo format_price($seccion['fPrecio']);?></td>
			<td class="number"><?php echo format_percent($seccion['fDescuento']);?></td>
			<td class="number"><?php echo format_price($seccion['fTotal']);?></td>
			<td class="number"><?php echo format_price($seccion['Cargos']);?></td>
		</tr>
		<?php endforeach;?>
	</table>
	<?php endif;?>
</div>
