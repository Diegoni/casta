<div class="container">
<div class="row">
	<div class="col-md-2">
		<div class="panel panel-primary">
  			<div class="panel-heading">
  				<i class="icon-tagalt-pricealt"></i> Reservas
  			</div>
  			<div class="panel-body">
    			<ul class="nav nav-pills nav-stacked">
	            	<li><a  href='<?php echo site_url('admin/reserva/reservas_abm')?>'>Reservas</a></li>
	            	<li><a  href='<?php echo site_url('admin/reserva/vuelos_abm')?>'>Vuelos</a></li>
	            	<li><a  href='<?php echo site_url('admin/reserva/disponibilidades_abm')?>'>Cierre de ventas</a></li>
					<li><a  href='<?php echo site_url('admin/reserva/estados_reserva')?>'>Estados reserva</a></li>
            	</ul>
  			</div>
		</div>
	</div>

	<div class="col-md-10">
		<div class="panel panel-primary">
  			<div class="panel-heading">
  				<i class="icon-tagalt-pricealt"></i> Reservas
  			</div>
  			<div class="panel-body">
  				<?php if($mensaje!=""){?>
  				<div class="alert alert-success alert-dismissible" role="alert">
				  <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				  <?php echo $mensaje ?>
				</div>
				<?php } ?>
  				<?php if($nuevas==0){ ?>
  				<?php 	foreach ($reserva_habitacion as $reserva) {
							$id_huesped=$reserva->id_huesped;
							$entrada=date("d/m/Y", strtotime($reserva->entrada));					  
							$salida=date("d/m/Y", strtotime($reserva->salida));
							$fecha_alta=date("d/m/Y H:i:s", strtotime($reserva->fecha_alta));
							$adultos=$reserva->adultos;
							$menores=$reserva->menores;
							$total=$reserva->total;
							$id_estado=$reserva->id_estado_reserva;
							$id_nota=$reserva->id_nota;
						} 
						?>
  				<form action="" method="post" class="form-horizontal">
  					<link href="<?php echo base_url().'librerias/ui/jquery-ui.css'?>" rel="stylesheet" media="screen">
    				<div class="form-group even" id="huesped_field_box">
						<div class="col-sm-2 control-label" id="huesped_display_as_box">
						Huesped<span class="required">*</span>  :
						</div>
						<div class="col-sm-10" id="adultos_input_box">
							<select name="id_huesped" class="chosen-select chzn-done form-control" data-placeholder="Seleccionar Huesped">
								<?php foreach ($huespedes as $huesped) { ?>
									<?php if($id_huesped==$huesped->id_huesped){ ?>
										<option value="<?php echo $huesped->id_huesped ?>" selected>
											<?php echo $huesped->apellido." ".$huesped->nombre ?>
										</option>
									<?php }else{ ?>
										<option value="<?php echo $huesped->id_huesped ?>">
											<?php echo $huesped->apellido." ".$huesped->nombre ?>
										</option>
									<?php } ?>	
								<?php } ?>
							</select>				
						</div>
					</div>
					
					<div class="form-group even" id="entrada_field_box">
						<div class="col-sm-2 control-label" id="entrada_display_as_box">
						Entrada<span class="required">*</span>  :
						</div>
						<div class="col-sm-10" id="entrada_input_box">
							<input id="entrada" name="entrada" type="text" value="<?php echo $entrada ?>" maxlength="10" class="form-control">		
						</div>
					</div>
					
					<div class="form-group even" id="salida_field_box">
						<div class="col-sm-2 control-label" id="salida_display_as_box">
						Salida<span class="required">*</span>  :
						</div>
						<div class="col-sm-10" id="salida_input_box">
							<input id="salida" name="salida" type="text" value="<?php echo $salida ?>" maxlength="10" class="form-control">		
						</div>
					</div>
					
					<div class="form-group even" id="adultos_field_box">
						<div class="col-sm-2 control-label" id="adultos_display_as_box">
						Adultos<span class="required">*</span>  :
						</div>
						<div class="col-sm-10" id="adultos_input_box">
							<input id="field-adultos" name="adultos" type="text" value="<?php echo $adultos?>" class="numeric form-control" maxlength="11">		
						</div>
					</div>
					
					<div class="form-group even" id="menores_field_box">
						<div class="col-sm-2 control-label" id="menores_display_as_box">
						Menores<span class="required">*</span>  :
						</div>
						<div class="col-sm-10" id="menores_input_box">
							<input id="field-menores" name="menores" type="text" value="<?php echo $menores?>" class="numeric form-control" maxlength="11">		
						</div>
					</div>
					
					<div class="form-group even" id="total_field_box">
						<div class="col-sm-2 control-label" id="total_display_as_box">
						Total<span class="required">*</span>  :
						</div>
						<div class="col-sm-10" id="total_input_box">
							<input id="field-total" name="total" type="text" value="<?php echo $total?>" class="numeric form-control" maxlength="11">		
						</div>
					</div>
					
					<div class="form-group even" id="alta_field_box">
						<div class="col-sm-2 control-label" id="alta_display_as_box">
						Fecha alta<span class="required">*</span>  :
						</div>
						<div class="col-sm-10" id="total_input_box">
							<input id="field-alta" name="fecha_alta" type="text" value="<?php echo $fecha_alta?>" class="numeric form-control" maxlength="11" readonly>		
						</div>
					</div>
	
					<div class="form-group even" id="huesped_field_box">
						<div class="col-sm-2 control-label" id="huesped_display_as_box">
						Habitaciones<span class="required">*</span>  :
						</div>
						<div class="col-sm-10" id="adultos_input_box">
							<select id="field-id_huesped" name="id_habitaciones[]" class="chosen-select chzn-done form-control" data-placeholder="Seleccionar habitaciones"  multiple="">
								<?php foreach ($habitaciones as $habitacion) { ?>
									<?php 
									$bandera=0;
									foreach ($reservas as $reserva) {
										if($reserva->id_habitacion==$habitacion->id_habitacion){
											$bandera=1;
											$cantidad=$reserva->cantidad;
										}
									}
									if($bandera==1){
									?>
										<option value="<?php echo $habitacion->id_habitacion ?>" selected><?php echo $cantidad." - ".$habitacion->habitacion." - ".$habitacion->hotel ?></option>
									<?php }else{ ?>
										<option value="<?php echo $habitacion->id_habitacion ?>"><?php echo $habitacion->habitacion." - ".$habitacion->hotel ?></option>
									<?php } ?>
								<?php } ?>
							</select>				
						</div>
					</div>
					
					
					<div class="form-group even" id="nota_field_box">
						<div class="col-sm-2 control-label" id="nota_display_as_box">
						Nota<span class="required">*</span>  :
						</div>
						<div class="col-sm-10" id="total_input_box">
							<?php 
							if($id_nota!=0){
								$notas=$this->notas_model->getNota($id_nota);
								foreach ($notas as $nota) {
									$value=$nota->nota;
								}
							}else{
								$value="";
							}	
							?>
							<textarea id="field-nota" name="nota" class="form-control"><?php echo $value ?></textarea>	
							<input type="hidden" value="<?php echo $id_nota?>" name="id_nota">	
						</div>
					</div>
					
					<div class="form-group even" id="huesped_field_box">
						<div class="col-sm-2 control-label" id="huesped_display_as_box">
						Estado<span class="required">*</span>  :
						</div>
						<div class="col-sm-10" id="adultos_input_box">
							<select id="field-id_huesped" name="id_estado_reserva" class="chosen-select chzn-done form-control" data-placeholder="Seleccionar Huesped">
								<?php foreach ($estados as $estado) { ?>
									<?php if($id_estado==$estado->id_estado_reserva){ ?>
										<option value=<?php echo $estado->id_estado_reserva ?> selected><?php echo $estado->estado_reserva ?></option>
									<?php }else{ ?>
										<option value=<?php echo $estado->id_estado_reserva ?> ><?php echo $estado->estado_reserva ?></option>
									<?php } ?>	
								<?php } ?>
							</select>				
						</div>
					</div>
					
					<div class="form-group even" id="alta_field_box">
						<div class="col-sm-2 control-label" id="alta_display_as_box">
						</div>
						<div class="col-sm-10" id="total_input_box">
							<button type="submit" name="aceptar" value="1" class="btn btn-default">Aceptar</button>
							<button type="submit" name="reenviar_correo" value="1" onclick="return confirm('Esta seguro de reenviar el correo?');" class="btn btn-default">Reenviar correo</button>		
							<a href="http://localhost/Hotel_web/index.php/es/admin/reserva/reservas_abm" class="btn btn-default">Reservas</a>
						</div>
					</div>

    			</form>
    			<?php }else{ ?>
    			Cantidad de habitaciones
    			<form action="" method="post" class="form-horizontal">
    				
    				<div class="form-group even" id="habitacion_field_box">
						<div class="col-sm-2 control-label" id="alta_display_as_box">
							<b>Habitación - Hotel</b>
						</div>
						<div class="col-sm-10" id="alta_display_as_box">
							<b>Cantidad</b>		
						</div>
					</div>
					
    				<?php foreach ($reservas as $habitacion) { ?>
					<div class="form-group even" id="habitacion_field_box">
						<div class="col-sm-2 control-label" id="alta_display_as_box">
						<?php echo $habitacion->habitacion." ".$habitacion->hotel ?><span class="required">*</span>  :
						</div>
						<div class="col-sm-10" id="total_input_box">
							<input id="field-alta" name="id_habitacion<?php echo $habitacion->id_habitacion ?>" type="text" value="<?php echo $habitacion->cantidad ?>" class="numeric form-control" maxlength="3">		
						</div>
					</div>
					<?php }?>
					<div class="form-group even" id="alta_field_box">
						<div class="col-sm-2 control-label" id="alta_display_as_box">
						</div>
						<div class="col-sm-10" id="total_input_box">
							<button type="submit" name="cantidad" value="1" class="btn btn-default">Aceptar</button>		
						</div>
					</div>
    			</form>
    			<?php }?>
  			</div>
		</div>
    </div>
</div> 

 <script src="<?php echo base_url().'librerias/chosen/chosen.jquery.js'?>" type="text/javascript"></script>
 <script src="<?php echo base_url().'librerias/chosen/prism.js'?>" type="text/javascript" charset="utf-8"></script>
  <script type="text/javascript">
    var config = {
      '.chosen-select'           : {},
      '.chosen-select-deselect'  : {allow_single_deselect:true},
      '.chosen-select-no-single' : {disable_search_threshold:10},
      '.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
      '.chosen-select-width'     : {width:"95%"}
    }
    for (var selector in config) {
      $(selector).chosen(config[selector]);
    }
  </script>   







