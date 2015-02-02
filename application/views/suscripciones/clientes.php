<?php $this->load->library('HtmlFile');?>
<table class="sortable-onload-shpw-0 rowstyle-alt colstyle-alt no-arrow"
summary="<?php echo $cTitulo;?>">
	<caption>
		<strong><?php echo $cTitulo;?></strong>
		<br />
		<?php echo $this->lang->line('Id');?>
		:<?php echo $nIdSuscripcion;?><br />
	</caption>
	<thead>
		<tr>
			<th class="sortable-date-dmy"><?php echo $this->lang->line('Fecha');?></th>
			<th class="sortable-numeric"><?php echo $this->lang->line('Antiguo');?></th>
			<th class="sortable-numeric"><?php echo $this->lang->line('Nuevo');?></th>
			<th class="sortable"><?php echo $this->lang->line('Usuario');?></th>
		</tr>
	</thead>
	<tbody>
		<?php $odd = FALSE;?>
		<?php foreach($clientes as $m):
		?>
		<tr <?php if ($odd):?> class="alt" <?php endif;?>>
			<td><?php echo format_datetime($m['dCambio']);?></td>
			<td align="left"><span style="color: blue;"><?php echo format_name($m['cNombre1'], $m['cApellido1'], $m['cEmpresa1']);?></span></td>
			<td align="left"><span style="color: green;"><?php echo format_name($m['cNombre2'], $m['cApellido2'], $m['cEmpresa2']);?></span></td>
			<td><?php echo $m['cCUser'];?></td>
		</tr>
		<?php $odd = !$odd;?>
		<?php endforeach;?>
	</tbody>
</table>
