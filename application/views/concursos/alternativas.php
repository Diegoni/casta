<?php $this->load->helper('extjs');?>
<?php $this->load->helper('asset');?>
<h3><?php echo $sin ;?> 
			<?php echo $this->lang->line('sin-asignar');?></h3>

<h3><?php echo $sin_cdu ;?> 
			<?php echo $this->lang->line('sin-cdu');?></h3>

<?php if (count($libros) > 0):?>

<table 
	summary="<?php echo $this->lang->line('Alternativas');?>">
	<caption>
		<?php echo $this->lang->line('Alternativas');?>
	</caption>
	<thead>
		<tr>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Concurso');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Alternativa');?></th>
		</tr>
	</thead>
	<tbody>
		<?php $count = 0; ?>
		<?php foreach($libros as $libro):?>
		<?php $alt = $libro['alt']; ?>
			<tr id="recibido_<?php echo $count;?>">
				<td>
					<span style="color: blue;"><?php echo $libro['cBiblioteca'];?></span><br/>
					<strong><?php echo $libro['cTitulo'];?></strong><br/>
						<span style="color: grey;"><?php echo $libro['cAutores'];?>					
					</span>
					<?php echo ($libro['cEditorial1a']); ?> | 
					<?php echo ($libro['cEditorial']); ?> | 
					<span style="color: green;"><?php echo format_price($libro['fPrecio']); ?></span>
				</td>
				<td>
					<span style="color: blue;"><?php echo $alt['cBiblioteca'];?></span><br/>
					<strong><?php echo $alt['cTitulo'];?></strong><br/>
							<span style="color: grey;"><?php echo $alt['cAutores'];?></span>
					<span id="recibido_2_<?php echo $count;?>" style="visibility:hidden;position:absolute;top:0;right:0;"><?php echo $libro['nIdBueno'];?>
					</span>
					<span id="recibido_3_<?php echo $count;?>" style="visibility:hidden;position:absolute;top:0;right:0;"><?php echo $libro['nIdMalo'];?>
					</span>
					<?php echo ($alt['cEditorial1a']); ?> | 
					<?php echo ($alt['cEditorial']); ?> | 
					<span style="color: green;"><?php echo format_price($alt['fPrecio2']); ?></span>
				</td>
			</tr>
		<?php ++$count; ?>
		<?php endforeach;?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="2" scope="row" align="right"><?php echo count($libros);?>
			<?php echo $this->lang->line('registros');?></td>
		</tr>
	</tfoot>
</table>
<?php endif; ?>

<?php if (count($errores) > 0):?>

<table 
	summary="<?php echo $this->lang->line('Errores');?>">
	<caption>
		<?php echo $this->lang->line('Errores');?>
	</caption>
	<thead>
		<tr>
			<th class="sortable" scope="col"><?php echo $this->lang->line('cTitulo');?></th>
			<th class="sortable" scope="col"><?php echo $this->lang->line('Error');?></th>
		</tr>
	</thead>
	<tbody>
		<?php $count = 0; ?>
		<?php foreach($libros as $libro):?>
			<tr id="recibido_<?php echo $count;?>">
				<td>
					<span style="color: blue;"><?php echo $libro['cBiblioteca'];?></span><br/>
					<strong><?php echo $libro['cTitulo'];?></strong><br/>
						<span style="color: grey;"><?php echo $libro['cAutores'];?>					
					</span>
					<?php echo ($libro['cEditorial1a']); ?> | 
					<?php echo ($libro['cEditorial']); ?> | 
					<span style="color: green;"><?php echo format_price($libro['fPrecio']); ?></span>
				</td>
				<td>
					<span style="color: red;"><?php echo $libro['error']; ?></span>
				</td>
			</tr>
		<?php ++$count; ?>
		<?php endforeach;?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="2" scope="row" align="right"><?php echo count($libros);?>
			<?php echo $this->lang->line('registros');?></td>
		</tr>
	</tfoot>
</table>
<?php endif; ?>
