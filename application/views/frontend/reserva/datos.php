	<div class="col-md-8 col-md-offset-2">
		<div class="panel panel-hotel">
			<div class="panel-heading"><?php echo $texto['datos_personales']?></div>
		  	<div class="panel-body">
		  		<form method="post" class="form-horizontal register" role="form" accept-charset="utf-8" action="<?php echo base_url().'index.php/'.$texto['url_idioma'].'/reserva/pago'?>" />
		  			
		  			<!--------------------------------------------------------------------------------
		  			----------------------------------------------------------------------------------
		  							Datos Personales
		  			----------------------------------------------------------------------------------
		  			--------------------------------------------------------------------------------->	
		  			
		  			<h3>
						<span class="label label-danger">1</span> <?php echo $texto['datos'] ?>
					</h3>
					<hr />
  					<div class="form-group">
    					<label for="nombre" class="col-sm-2 control-label"><?php echo $texto['nombre']?></label>
    					<div class="col-sm-10">
    						<div class="input-group">
								<input type="text" class="form-control" name="nombre" id="validate-text" placeholder="<?php echo $texto['ingrese']?> <?php echo $texto['nombre']?>" maxlength="64" autofocus required>
								<span class="input-group-addon danger"><span class="glyphicon glyphicon-remove"></span></span>
							</div>
    					</div>
  					</div>
  					
  					<div class="form-group">
    					<label for="apellido" class="col-sm-2 control-label"><?php echo $texto['apellido']?></label>
    					<div class="col-sm-10">
    						<div class="input-group">
								<input type="text" class="form-control" name="apellido" id="validate-text" placeholder="<?php echo $texto['ingrese']?> <?php echo $texto['apellido']?>" maxlength="64" required>
								<span class="input-group-addon danger"><span class="glyphicon glyphicon-remove"></span></span>
							</div>
    					</div>
  					</div>
  					
  					<div class="form-group">
    					<label for="email" class="col-sm-2 control-label"><?php echo $texto['email']?></label>
    					<div class="col-sm-10">
    						<div class="input-group" data-validate="email">
								<input type="text" class="form-control" name="email" id="validate-email" placeholder="<?php echo $texto['ingrese']?> <?php echo $texto['email']?>" maxlength="64" required>
								<span class="input-group-addon danger"><span class="glyphicon glyphicon-remove"></span></span>
							</div>
      					</div>
  					</div>
  					  					  					
  					<div class="form-group">
    					<label for="telefono" class="col-sm-2 control-label"><?php echo $texto['telefono']?></label>
    					<div class="col-sm-10">
    						<div class="input-group" data-validate="phone">
								<input type="text" class="form-control" name="telefono" id="validate-phone" placeholder="<?php echo $texto['ejemplo']?> 261-4223355" maxlength="32" required>
								<span class="input-group-addon danger"><span class="glyphicon glyphicon-remove"></span></span>
							</div>
    					</div>
  					</div>
  					
  					
  					
  					<!--------------------------------------------------------------------------------
		  			----------------------------------------------------------------------------------
		  							Tarjeta
		  			----------------------------------------------------------------------------------
		  			--------------------------------------------------------------------------------->	
  					
  					<h3>
						<span class="label label-danger">2</span> <?php echo $texto['tarjeta']?>
					</h3>
  					<hr />
  					
  					<div class="form-group">
            			<label for="tipo_tarjeta" class="col-sm-2 control-label"><?php echo $texto['tipo_tarjeta']?></label>
						<div class="col-sm-10">
							<div class="input-group">
		                        <select class="form-control" name="tipo_tarjeta" id="tipo_tajeta"required>
		                            <option value=""><?php echo $texto['seleccione_tarjeta']?></option>
		                            <?php foreach ($tipos_tarjeta as $tipo_tarjeta) { ?>
										<option value="<?php echo $tipo_tarjeta->id_tipo_tarjeta; ?>"><?php echo $tipo_tarjeta->tipo_tarjeta; ?></option>	
									<?php } ?>
		                        </select>
								<span class="input-group-addon danger"><span class="glyphicon glyphicon-remove"></span></span>
							</div>
						</div>
					</div>
					
					<div class="form-group">
    					<label for="tarjeta" class="col-sm-2 control-label"><?php echo $texto['tarjeta']?></label>
    					<div class="col-sm-10">
    						<div class="input-group" data-validate="tarjeta">
								<input type="text" class="form-control" name="tarjeta" id="tarjeta" placeholder="<?php echo $texto['ejemplo']?> 1234-5678-9012-3456" maxlength="20" autocomplete="off" required>
								<span class="input-group-addon danger"><span class="glyphicon glyphicon-remove"></span></span>
							</div>
    					</div>
  					</div>
  					
  					<div class="form-group">
    					<label for="pin" class="col-sm-2 control-label"><?php echo $texto['pin'] ?></label>
    					<div class="col-sm-10">
    						<div class="input-group" data-validate="pin">
								<input type="password" class="form-control" name="pin" id="pin" placeholder="<?php echo $texto['ingrese']?> <?php echo $texto['pin'] ?>" maxlength="5" autocomplete="off" data-toggle="popover" 
        						data-content="<?php echo $texto['ayuda_pin']?>" required>
								<span class="input-group-addon danger"><span class="glyphicon glyphicon-remove"></span></span>
							</div>
    					</div>
  					</div>
  					<script>
					$('[data-toggle="popover"]').popover({
					    trigger: 'select',
					        'placement': 'top',
					        delay: { "show": 500, "hide": 100 }
					});
					</script>
  					
  					<div class="form-group">
    					<label for="tarjeta" class="col-sm-2 control-label"><?php echo $texto['vencimiento'] ?></label>
    					<div class="col-sm-10">
    						<div class="input-group">
    							<input type="text" class="form-control" name="vencimiento" id="vencimiento" placeholder="<?php echo $texto['ingrese']?> <?php echo $texto['vencimiento'] ?>" onkeypress="return false" autocomplete="off" required>
								<span class="input-group-addon danger"><span class="glyphicon glyphicon-remove"></span></span>
							</div>
    					</div>
  					</div>
  					
  				
  					
  					<!--------------------------------------------------------------------------------
		  			----------------------------------------------------------------------------------
		  							Vuelo
		  			----------------------------------------------------------------------------------
		  			--------------------------------------------------------------------------------->	
		  			
  					<h3>
						<span class="label label-danger">3</span> <?php echo $texto['vuelo'] ?>
					</h3>
  					<hr />
  					
  					<div class="form-group">
    					<label for="tarjeta" class="col-sm-2 control-label"><?php echo $texto['nro_de_vuelo']?></label>
    					<div class="col-sm-10">
							<input type="text" class="form-control" name="nro_de_vuelo" id="nro_de_vuelo" placeholder="<?php echo $texto['ingrese']?> <?php echo $texto['nro_de_vuelo']?>" maxlength="32">
    					</div>
  					</div>
  					
  					<div class="form-group">
    					<label for="tarjeta" class="col-sm-2 control-label"><?php echo $texto['horario_llegada']?></label>
    					<div class="col-sm-10">
    						<input type="text" class="form-control" name="horario_llegada" id="horario" onkeypress="return false" placeholder="<?php echo $texto['ingrese']?> <?php echo $texto['horario_llegada']?>" autocomplete="off">
    					</div>
  					</div>
  					
  					<div class="form-group">
    					<label for="tarjeta" class="col-sm-2 control-label"><?php echo $texto['aerolinea'] ?></label>
    					<div class="col-sm-10">
							<select class="form-control" name="aerolinea" id="aerolinea" placeholder="<?php echo $texto['aerolinea'] ?>">
								<option value=""></option>
								<?php foreach ($aerolineas as $aerolinea) { ?>
									<option value="<?php echo $aerolinea->id_aerolinea?>"><?php echo $aerolinea->aerolinea ?></option>
								<?php } ?>
							</select>
    					</div>
  					</div>
  					
  					<div class="form-group slidingDiv">
  						<label for="nota" class="col-sm-2 control-label"><?php echo $texto['nota']?></label>
    					<div class="col-sm-10">
      						<textarea class="form-control" name="nota" rows="3" placeholder="<?php echo $texto['ingrese']?> <?php echo $texto['nota']?>"></textarea>
    					</div>
  					</div>
  					
  					<div class="form-group">
  						<label for="nota" class="col-sm-2 control-label"></label>
    					<div class="col-sm-10">
      						<a class="btn btn-default show_hide">
								<?php echo $texto['agregar_nota']?>
							</a>
    					</div>
  					</div>
  					
  					<div class="form-group">
					    <div class="col-sm-offset-2 col-sm-10">
					      <div class="input-group">
					          <input type="checkbox" required> <?php echo $texto['acepto_condiciones']?> 
					          <a href="#" class="btn btn-default btn-xs" data-toggle="modal" data-target="#terminos"><?php echo $texto['ver_condiciones']?> </a> 
					      </div>
					    </div>
					</div>
  					  					
  					<div class="form-group">
  						<input type="hidden" name="entrada" value="<?php echo $this->input->post('entrada') ?>">
						<input type="hidden" name="salida"  value="<?php echo $this->input->post('salida') ?>">
						<input type="hidden" name="adultos" value="<?php echo $this->input->post('adultos') ?>">
						<input type="hidden" name="menores" value="<?php echo $this->input->post('menores') ?>">					
						<input type="hidden" name="hotel" value="<?php echo $this->input->post('hotel') ?>">
						<?php 
						foreach ($habitaciones as $clave => $valor) { ?>
	    					<input type="hidden" name="habitacion<?php echo $clave;?>" value="<?php echo $valor?>">
	    					<input type="hidden" name="precio<?php echo $clave;?>" value="<?php echo $this->input->post('precio'.$clave) ?>">
						<?php } ?>
						<center>
    					<div class="col-sm-offset-2 col-sm-5">
							<button type="submit" class="btn btn-hotel boton-redondo">
								<span class="icon-ok font-big"></span>
							</button>
							
    					</div>
    					<div class="col-sm-5">
    						<a href="javascript:window.history.back();" type="submit" class="btn btn-hotel boton-redondo" title="<?php echo $texto['volver']?>" rel="tooltip">
								<span class="icon-chevron-left"></span>
							</a>
      					</div>
      					</center>
    					
  					</div>  					
				</form>
		    </div>
		</div>
		
		<!-- ver como lo vamos a trabajar -->
		<!--
		<div class="panel panel-default">
			<div class="panel-heading">Huesped registrado</div>
		  	<div class="panel-body">
				<form class="form-horizontal" role="form">
  					<div class="form-group">
    					<label for="usuario" class="col-sm-2 control-label">Ususario</label>
    					<div class="col-sm-10">
      						<div class="input-group">
								<input type="text" class="form-control" name="nombre" id="validate-text" placeholder="Ingrese nombre" required>
								<span class="input-group-addon danger"><span class="glyphicon glyphicon-remove"></span></span>
							</div>
    					</div>
    				</div>
  					
  					
  					<div class="form-group">
    					<label for="pass" class="col-sm-2 control-label">Pass</label>
    					<div class="col-sm-10">
      					<div class="input-group">
								<input type="password" class="form-control" name="pass" id="validate-text" placeholder="Ingrese pass" required>
								<span class="input-group-addon danger"><span class="glyphicon glyphicon-remove"></span></span>
							</div>
    					</div>
  					</div>
  					
  					<div class="form-group">
    					<div class="col-sm-offset-2 col-sm-10">
      						<button type="submit" class="btn btn-default">Sign in</button>
    					</div>
  					</div>
				</form>
			</div>
		</div>
		-->
	</div>
</div>

<div class="modal fade" id="terminos" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  	<div class="modal-dialog">
    	<div class="modal-content">
      		<div class="modal-header">
        		<h4 class="modal-title" id="myModalLabel"><?php echo $texto['terminos_condiciones']?></h4>
      		</div>
      		<div class="modal-body">
      			<?php foreach ($terminos as $termino) { ?>
					  <?php echo $termino->termino; ?>
				<?php } ?>
      		</div>
      		<div class="modal-footer">
        		<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $texto['cerrar']?></button>
      		</div>
      		</form>
    	</div>
  	</div>
</div>	