<table class="fons2" cellspacing="0" cellpadding="5" width="100%"
	border="0">
	<thead>
		<tr>
			<th><?php echo $this->lang->line('report-nIdLibro');?></th>
			<th><?php echo $this->lang->line('report-cAutores');?></th>
			<th><?php echo $this->lang->line('report-cTitulo');?></th>
			<th><?php echo $this->lang->line('report-Colección');?></th>
			<th><?php echo $this->lang->line('report-Editorial');?></th>
			<th><?php echo $this->lang->line('report-Año');?></th>
			<th><?php echo $this->lang->line('report-fPrecio');?></th>
			<th><?php echo $this->lang->line('report-Idioma');?></th>
		</tr>
	</thead>
	<tbody>

	<?php foreach($libros as $libro):?>
		<tr>
			<td><?php echo $libro["nIdLibro"];?></td>
			<td><?php echo isset($libro["cAutores"])?$libro["cAutores"]:'&nbsp;';?></td>
			<td><?php echo isset($libro["cTitulo"])?$libro["cTitulo"]:'&nbsp;';?></td>
			<td><?php echo isset($libro["coleccion"]['cNombre'])?$libro["coleccion"]['cNombre']:'&nbsp;';?></td>
			<td><?php echo isset($libro["editorial"]['cNombre'])?$libro["editorial"]['cNombre']:'&nbsp;';?></td>
			<td><?php echo isset($libro["dEdicion"])?date('Y',$libro["dEdicion"]):'&nbsp;'?></td>
			<td align="right"><?php echo isset($libro["fPVP"])?format_price($libro["fPVP"]):'&nbsp;';?></td>
			<td><?php echo isset($libro["idioma"]['cNombre'])?$libro["idioma"]['cNombre']:'&nbsp;';?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
