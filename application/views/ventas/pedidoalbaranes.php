<table summary="<?php echo $this->lang->line('Pedidos cliente');?>">
	<caption>
		<?php echo $this->lang->line('Pedidos cliente');?> <?php echo $id;?>
	</caption>
	<thead>
		<tr>
			<th><?php echo $this->lang->line('nIdAlbaran');?></th>
			<th><?php echo $this->lang->line('nIdPedido');?></th>
			<th><?php echo $this->lang->line('Cliente');?></th>
			<th><?php echo $this->lang->line('cTitulo');?></th>
			<th><?php echo $this->lang->line('cSeccion');?></th>
			<th><?php echo $this->lang->line('nCantidad');?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($pedidos as $alb):
		?>
		<tr>
			<td><?php echo format_enlace_cmd($alb['nIdAlbaran'], site_url('ventas/albaransalida/index/' . $alb['nIdAlbaran']));?></td>
			<td><?php echo format_enlace_cmd($alb['nIdPedido'], site_url('ventas/pedidocliente/index/' . $alb['nIdPedido']));?></td>
			<td><?php echo format_enlace_cmd($alb['nIdCliente'], site_url('clientes/cliente/index/' . $alb['nIdCliente']));?> <?php echo format_name($alb['cEmpresa'], $alb['cNombre'], $alb['cApellido']);?></td>
			<td><?php echo format_enlace_cmd($alb['nIdLibro'], site_url('catalogo/articulo/index/' . $alb['nIdLibro']));?>- <?php echo $alb['cTitulo'];?></td>
			<td><?php echo $alb['cSeccion'];?></td>
			<td align="right"><?php echo format_number($alb['nCantidad']);?></td>
		</tr>
		<?php endforeach;?>
	</tbody>
</table>
