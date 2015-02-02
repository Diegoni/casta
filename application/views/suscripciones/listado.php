<table
	class="sortable-onload-0 rowstyle-alt colstyle-alt"
	summary="<?php echo $this->lang->line('Suscripciones');?>">
	<caption><?php echo $this->lang->line('Suscripciones');?></caption>
	<thead>
		<tr>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Id');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Revista');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Tipo'); ?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('Precio');?></th>
			<th class="sortable-numeric" scope="col"><?php echo $this->lang->line('PVP');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('cRefCliente');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('cRefProveedor');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Renovación'); ?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Estado'); ?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Cliente');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Proveedor');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('nIdDireccionEnvio');?></th>
		</tr>
	</thead>
	<tbody>
	<?php $odd = FALSE;?>
	<?php foreach($suscripciones as $k => $m):?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td scope="row"><?php echo format_enlace_cmd($m['nIdSuscripcion'], site_url('suscripciones/suscripcion/index/' . $m['nIdSuscripcion']));?></td>
			<td scope="row"><?php echo format_enlace_cmd($m['articulo']['cTitulo'], site_url('catalogo/articulo/index/' . $m['nIdRevista']));?></td>
			<td scope="row"><?php echo $m['cTipoSuscripcion']; ?></td>
			<td scope="row" align="right"><?php echo format_price($m['fPrecio']);?></td>
			<td scope="row" align="right"><?php echo format_price(isset($m['fPVP'])?$m['fPVP']:0);?></td>
			<td scope="row"><?php echo $m['cRefCliente'];?></td>
			<td scope="row"><?php echo $m['cRefProveedor'];?></td>
			<td scope="row" align="right"><?php echo format_date($m['dRenovacion']);?></td>
			<td scope="row"><?php echo !$m['bActiva']? ('<span style="color:red;">'. $this->lang->line('Cancelada') . '</span>'):('<span style="color:green;">'.  $this->lang->line('Activa') . '</span>'); ?>
			<?php if (isset($m['avisos'])):?>
			<span style="aviso_renovacion">
			<?php echo $avisos[0]['cCampana'];?> - <?php echo $this->lang->line(isset($avisos[0]['nIdAvisoRenovacion']) ? (isset($avisos[0]['dGestionada']) ? ($avisos[0]['bAceptada'] ? 'ACEPTADA' : 'RECHAZADA') : 'SIN GESTIONAR') : 'NO HAY AVISO');?>
			</span><?php endif;?>
			<?php if ($m['bNoFacturable'] == 0):
			?>
			<?php if ($m['nEntradas'] > $m['nFacturas']):
			?>
			<span class="est_sus_pend"> <?php echo $this->lang->line('PENDIENTE FACTURAR');?></span><?php endif;?>
			<?php if ($m['nEntradas'] < $m['nFacturas']):
			?>
			<span class="est_sus_ant"> <?php echo $this->lang->line('ANTICIPADA');?>
				<?php echo format_enlace_cmd($m['nIdUltimaFactura'], site_url('ventas/factura/index/' . $m['nIdUltimaFactura']));?></span><?php endif;?>
			<?php endif;?></th>
				
				
			</td>
			<td scope="row"><?php echo format_enlace_cmd(format_name($m['cliente']['cNombre'], $m['cliente']['cApellido'], $m['cliente']['cEmpresa']), site_url('clientes/cliente/index/' . $m['nIdCliente']));?></td>
			<td scope="row"><?php echo isset($m['proveedor'])?format_enlace_cmd(format_name($m['proveedor']['cNombre'], $m['proveedor']['cApellido'], $m['proveedor']['cEmpresa']), site_url('proveedores/proveedor/index/' . $m['nIdCliente'])):'';?></td>
			<td scope="row"><?php echo isset($m['direccionenvio']) ? format_address_print($m['direccionenvio']) : $this->lang->line('SIN DIRECCIÓN');?></td>
		</tr>
		<?php $odd = !$odd;?>
		<?php endforeach;?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="12"><?php echo count($suscripciones);?> <?php echo $this->lang->line('registros');?></td>
		</tr>
	</tfoot>
</table>
