<!------------------------------------------------------------------------
--------------------------------------------------------------------------
					Modal reserva
--------------------------------------------------------------------------
------------------------------------------------------------------------->	
<?php echo form_open('admin/reserva/actualizar_nuevas'); ?>
<div class="modal fade" id="modal_reservas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel"><span class="icon-tagalt-pricealt"></span> Nuevas reservas</h4>
      </div>
      <div class="modal-body">
      	<table class="table table-hover">
      		<thead>
      			<tr>
	      			<th>ID</th>
	      			<th>Habitación</th>
	      			<th>Huesped</th>
	      			<th>Entrada</th>
	      			<th>Salida</th>
	      			<th>Estado</th>
      			</tr>
      		</thead>
      		<tbody>
      			<?php foreach ($reservas as $reserva) { ?>
			  	<tr>
			  		<td><?php echo $reserva->id_reserva ?></td>
			  		<td><?php $habitaciones=$this->reserva_habitacion_model->getReserva($reserva->id_reserva); ?>
			  			<?php if($habitaciones){ ?>
			  			<?php foreach($habitaciones as $habitacion){ ?>
			  				<a href=<?php echo base_url().'index.php/admin/habitacion/habitaciones_abm/read/'.$habitacion->id_habitacion; ?> target="_blank">
			  					<?php echo $habitacion->cantidad ?> - 
			  					<?php echo $habitacion->habitacion ?><br>
			  				</a>	
			  			<?php }} ?>
			  		</td>
					<td><a href=<?php echo base_url().'index.php/admin/huesped/huespedes_abm/edit/'.$reserva->id_huesped; ?> target="_blank"> 
			  		 		<?php echo $reserva->apellido ?> <?php echo $reserva->nombre ?>
			  		 	</a>
			  		</td>			  			
			  		<td><?php echo date("d-m-Y", strtotime($reserva->entrada)); ?></td>
			  		<td><?php echo date("d-m-Y", strtotime($reserva->salida));  ?></td>
			  		<td><select name="estado<?php echo $reserva->id_reserva ?>">
			  				<?php foreach ($estados_reserva as $estado) { ?>
			  				<?php if($estado->id_estado_reserva==2){ ?>
			  					<option value="<?php echo $estado->id_estado_reserva?>" selected><?php echo $estado->estado_reserva;?></option>	
			  				<?php }else{ ?>
			  					<option value="<?php echo $estado->id_estado_reserva?>"><?php echo $estado->estado_reserva;?></option>
			  				<?php } ?>
							<?php } ?>
			  			</select>
			  		</td>
			  		<input type="hidden" name="id_reserva<?php echo $reserva->id_reserva ?>" value="<?php echo $reserva->id_reserva ?>">
			  	</tr>
		 		<?php }?>
      		</tbody>
      	</table>
      	
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <button type="submit" class="btn btn-primary">Guardar cambios</button>
      </div>
    </div>
  </div>
</div>
<?php echo form_close(); ?>


<!------------------------------------------------------------------------
--------------------------------------------------------------------------
					Modal mensaje
--------------------------------------------------------------------------
------------------------------------------------------------------------->	

<?php echo form_open('admin/mensaje/actualizar_nuevos'); ?>
<div class="modal fade" id="modal_mensajes" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel"><span class="icon-emailalt"></span> Nuevos mensajes</h4>
      </div>
      <div class="modal-body">
      	<table class="table table-hover">
      		<thead>
      			<tr>
	      			<th>Mensaje</th>
	      			<th>De</th>
	      			<!--<th>Tipo</th>-->
	      			<th>Fecha</th>
	      			<th>Estado</th>
      			</tr>
      		</thead>
      		<tbody>
      			<?php foreach ($mensajes as $mensaje) { ?>
			  	<tr>
			  		<td><?php echo $mensaje->mensaje ?></td>
			  		<td><?php echo $mensaje->emisor ?> 
			  			<!--
			  			<a title="responder" href=<?php echo base_url().'index.php/admin/mensaje/mensajes_abm/add'?> target="_blank">
			  				<span class="icon-emailforward"></span>
			  			</a>-->
			  		</td>
			  		<!---<td><?php echo $mensaje->id_tipo_mensaje ?></td>-->
			  		<td><?php echo date("d-m-Y", strtotime($mensaje->fecha_envio));  ?></td>
			  		<td><select name="estado<?php echo $mensaje->id_mensaje; ?>">
			  				<?php foreach ($estados_mensaje as $estado) { ?>
			  				<?php if($estado->id_estado_mensaje==2){ ?>
			  					<option value="<?php echo $estado->id_estado_mensaje?>" selected><?php echo $estado->estado_mensaje;?></option>	
			  				<?php }else{ ?>
			  					<option value="<?php echo $estado->id_estado_mensaje?>"><?php echo $estado->estado_mensaje;?></option>
			  				<?php } ?>
							<?php } ?>
			  			</select>
			  		</td>
			  		<input type="hidden" name="id_mensaje<?php echo $mensaje->id_mensaje; ?>" value="<?php echo $mensaje->id_mensaje; ?>">
			  	</tr>
		 		<?php }?>
      		</tbody>
      	</table>
      	
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <button type="submit" class="btn btn-primary">Guardar cambios</button>
      </div>
    </div>
  </div>
</div>
<?php echo form_close(); ?>
