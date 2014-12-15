
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
				<div class="panel panel-hotel">
					<?php
					foreach ($categorias as $categoria) {
						if($t_categorias){
							foreach ($t_categorias as $key => $value) {
								if($key=='traduccion_titulo'.$categoria->id_categoria){
									$categoria->categoria = $value;
								}
							}	
						}
					?>
			  		<div class="panel-heading"><?php echo $categoria->categoria;?></div>
			  		<?php } ?>
			  		<?php
			  		if ($articulos){
			  		foreach ($articulos as $articulo) { 
			  			if($traducciones){
							foreach ($traducciones as $key => $value) {
								if($key=='traduccion_descripcion'.$articulo->id_articulo){
									$articulo->articulo	= $value;
								}
								
								if($key=='traduccion_titulo'.$articulo->id_articulo){
									$articulo->titulo	= $value;
								}
							}	
						}
			  		?>
			  		<div class="panel-body">
			  			
			  			<div class="badger-left badger-hotel" data-badger="<?php echo $articulo->titulo ?>">
							<div class="descripcion">
								<!--<blockquote>-->
						<!--<div class="panel panel-hotel">
							<div class="panel-subheading"><?php echo $articulo->titulo ?></div>-->
							<div class="panel-body">
								  <?php
					  			if($articulo->archivo_url!=""){?>
					    			<img class="img-circle img-banner" src="<?php echo base_url().'assets/uploads/articulos/'.$articulo->archivo_url?>">
					    		<?php } ?>
					    		<div class="text-banner">
					    			<?php echo $articulo->articulo; ?>
					    		</div>
								<!--</blockquote>-->
							</div>
							<?php if($articulo->id_tarifa_temporal!=0){
								$fechas=$this->tarifas_temporales_model->getFechas($articulo->id_tarifa_temporal);
								foreach ($fechas as $fecha) {
									if($fecha->entrada>date("Y/m/d")){}
										$fecha->entrada=date("Y/m/d");
									?>
									
									<p class="reservar"><?php echo $texto['reservar'] ?></p>
									<form class="form-horizontal" role="form" action="<?php echo base_url().'index.php/reserva/habitacion' ?>" method="post">
										<div class="col-md-6 col-md-offset-3">
										<div class="form-group">
											<div class="input-group">
										    	<div class="input-group-addon" onclick="document.getElementById('entrada_articulo').focus();">
										    		<span class="icon-calendarthree"></span>
												</div>
												<input type="text" name="entrada" class="form-control" id="entrada_articulo<?php echo $articulo->id_articulo?>" placeholder="<?php echo $texto['entrada']?>" autocomplete="off" required>
											</div>
  										</div>
  										</div>
  										<div class="col-md-6 col-md-offset-3">
  										<div class="form-group">
											<div class="input-group">
										    	<div class="input-group-addon" onclick="document.getElementById('salida_articulo').focus();" >
													<span class="icon-calendarthree"></span>
												</div>
												<input type="text" name="salida" class="form-control" id="salida_articulo<?php echo $articulo->id_articulo?>" placeholder="<?php echo $texto['salida']?>" autocomplete="off" required>
											</div>
										</div>
										</div>
										<div class="col-md-6 col-md-offset-3">	
										<div class="form-group">
											<select class="form-control" name="adultos">
												<?php 
												foreach ($configs as $config) {
													$max_adultos=$config->max_adultos;
													$max_menores=$config->max_menores;
												}
												$i=1;
												do{
													if($i==1){ ?>
														<option value="<?php echo $i;?>"><?php echo $i;?> <?php echo $texto['adulto']?></option>	
													<?php }else{?>
														<option value="<?php echo $i;?>" <?php if($i==2){echo "selected";};?>><?php echo $i;?> <?php echo $texto['adultos']?></option>
												<?php 
													}
												$i=$i+1;
												}while($i<=$max_adultos);?>
											</select>
											<div class="separador"></div>
								     		<select class="form-control" name="menores">
									  			<?php $i=0;
												do{
													if($i==0){ ?>
														<option value="<?php echo $i;?>"><?php echo $texto['sin_menores']?></option>
													<?php }else if($i==1){ ?>
														<option value="<?php echo $i;?>"><?php echo $i;?> <?php echo $texto['menor']?></option>	
													<?php }else{?>
														<option value="<?php echo $i;?>"><?php echo $i;?> <?php echo $texto['menores']?></option>
												<?php 
													}
							
												$i=$i+1;
												}while($i<=$max_menores);?>
											</select>
										</div>
										</div>
										
										<input name="hotel" type="hidden" value="2" />
										<div class="form-group">
    									<div class="col-md-6 col-md-offset-3">
											<center>
											<button class="btn btn-hotel boton-redondo" type="submit" title="<?php echo $texto['reservar']?>">
												<span class="icon-ok"></span>
											</button>
											</center>
										</div>
										</div>
										
										
									</form>
									
									<script>
									  $(function() {
									    $( "#entrada_articulo<?php echo $articulo->id_articulo?>" ).datepicker({
									      minDate: "<?php echo date("d/m/Y", strtotime($fecha->entrada));?>",
										  maxDate: "<?php echo date("d/m/Y", strtotime($fecha->salida));?>",
									      onClose: function( selectedDate ) {
									        $( "#salida_articulo<?php echo $articulo->id_articulo?>" ).datepicker( "option", "minDate", selectedDate );
									      }
									    });
									    $( "#salida_articulo<?php echo $articulo->id_articulo?>" ).datepicker({
									      minDate: "<?php echo date("d/m/Y", strtotime($fecha->entrada));?>",
										  maxDate: "<?php echo date("d/m/Y", strtotime($fecha->salida));?>",
									      onClose: function( selectedDate ) {
									        $( "#entrada_articulo<?php echo $articulo->id_articulo?>" ).datepicker( "option", "maxDate", selectedDate );
									      }
									    });
									  });
									</script>
								<?php
								}
							} ?>
						</div>
						</div>
						<!--<small><?php echo $texto['fecha_publicacion']; ?> : <?php echo date("d-m-Y" ,strtotime($articulo->fecha_publicacion));?></small>-->						
					</div>
					
					<?php }
					}else{
						
					}
					 ?>
					<div class="panel-body">
					<center>
						<a href="javascript:window.history.back();" type="submit" class="btn btn-hotel boton-redondo" title="<?php echo $texto['volver']?>" rel="tooltip">
							<span class="icon-chevron-left"></span>
						</a>
					</center>
					</div>
				</div>
	</div>	
	</div>



	