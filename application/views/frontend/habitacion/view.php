<div class="row">
	<div class="col-md-8 col-md-offset-2">
		<div class="panel panel-hotel">
			<div class="panel-heading"><?php echo $texto['habitacion']?></div>
		  	<div class="panel-body">
			<?php foreach ($habitaciones as $habitacion) { 
				
					if($traducciones){
						foreach ($traducciones as $key => $value) {
							if($key=='traduccion_descripcion'.$habitacion->id_habitacion){
								$habitacion->descripcion	= $value;
							}
								
							if($key=='traduccion_titulo'.$habitacion->id_habitacion){
								$habitacion->habitacion		= $value;
							}
						}	
					}
			?> 
			<div class="panel">
				<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
					<?php	$imagenes_habitacion=$this->imagenes_habitacion_model->getImagenes($habitacion->id_habitacion); 
					if($imagenes_habitacion){
					?>
					<div class="carousel-inner">
						<?php 
						$i=0;
						foreach ($imagenes_habitacion as $imagenes) { ?>
						<a href="#" class="item <?php if($i==0){echo 'active';}?>" class="thumbnail">
							<center>
								<img alt="slide" src="<?php echo base_url().'assets/uploads/habitaciones/'.$imagenes->imagen;?>" width="600" height="350">
							</center>
							<?php if($imagenes->descripcion!=""){ ?>
								<div class="carousel-caption">
									<p><?php echo $imagenes->descripcion;?></p>
								</div>
							<?php } ?>							
						</a>
						<?php $i=$i+1?>
						<?php } ?>
					</div>
					<a class="left carousel-control" href="#carousel-example-generic" data-slide="prev">
						<span class="glyphicon glyphicon-chevron-left"></span>
					</a>
					<a class="right carousel-control" href="#carousel-example-generic" data-slide="next">
						<span class="glyphicon glyphicon-chevron-right"></span>
					</a>
					<?php } ?>
				</div>
			</div>
			<div class="panel">
				<div class="badger-left badger-hotel" data-badger="<?php echo $texto['descripcion']?>">
				<div class="descripcion"><?php echo $habitacion->descripcion;?></div>
				</div>
				
				<div class="badger-left badger-hotel" data-badger="<?php echo $texto['servicios']?>">
				<div class="servicios">
					<ul class="list-unstyled">
					<?php if($servicios){ ?>
					<?php foreach ($servicios as $servicio) {
						if($t_servicios){
							foreach ($t_servicios as $key => $value) {
								if($key=='traduccion_titulo'.$servicio->id_servicio){
									$servicio->servicio	= $value;
								}
							}	
						}
						?>
						<li class="lista-servicios">
							<!--<img src='<?php echo base_url().'assets/uploads/servicios/'.$servicio->icono?>' class="icono-servicios">-->
							<i class="fa fa-check"></i>
							<?php echo $servicio->servicio?>
						</li>
					<?php } ?>	
					<?php } ?>
					</ul>
					
				</div>
				</div>
				
				<div class="badger-left badger-hotel" data-badger="<?php echo $texto['condiciones']?>">
					<dl class="dl-horizontal">
					  	<dt><i class='fa fa-user'></i> <?php echo $texto['adultos']?>: </dt>
					  	<dd><?php echo $habitacion->adultos;?></dd>
					  	<dt><i class='fa fa-child'></i> <?php echo $texto['menores']?>: </dt>
					  	<dd><?php echo $habitacion->menores;?></dd>
					</dl>
					<dl class="dl-horizontal">
						<dt><i class="fa fa-sign-in"></i> <?php echo $texto['entrada']?>: </dt>
						<dd><?php echo date("H:i",strtotime($habitacion->entrada));?> Hs</dd>
						<dt><i class="fa fa-sign-out"></i> <?php echo $texto['salida']?>: </dt>
						<dd><?php echo date("H:i",strtotime($habitacion->salida));?> Hs</dd>
					</dl>	
				</div>
				
				<!--				
				<div class="badger-left badger-hotel" data-badger="<?php echo $texto['como_llegar']?>">
				<?php 
  					$telefono=array();
					$direccion=array();
					
					foreach ($hoteles as $hotel) {
						if (!(in_array($hotel->calle." - ".$hotel->provincia, $direccion))) {
							$direccion[]=$hotel->hotel.", ".$hotel->calle.", ".$hotel->provincia;
						}	
					} 
					
					foreach ($direccion as $key => $value) {
						$direccion_final=$value;
					}
				?>
				
				<script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=false&languaje=sp"></script>
				<script type="text/javascript" src="<?php echo base_url().'librerias/main/js/google_maps.js'?>"></script>

  				<div id="map_canvas" style="float:left;width:100%;height:60%; margin-bottom: 15px;"></div>
				<form class="form-horizontal" role="form">
					
					<div class="form-group">
    					<label for="inputEmail3" class="col-sm-2 control-label">
    						<?php echo $texto['destino'];?>:
    					</label>
    					<div class="col-sm-10">
							<input type="text" id="end" class="form-control" value="<?php echo $direccion_final ?>" readonly>
						</div>
  					</div>
			
					<div class="form-group">
    					<label for="inputEmail3" class="col-sm-2 control-label">
    						<?php echo $texto['desde'];?>:
    					</label>
    					<div class="col-sm-10">
							<input type="text" class="form-control" id="start" value="">
						</div>
  					</div>
  					
  					<div class="form-group">
    					<label for="inputEmail3" class="col-sm-2 control-label">
    						<?php echo $texto['puntos_intermedios'];?>:
    					</label>
    					<div class="col-sm-10">
							<input type="text" id="waypoints1" class="form-control">
							<div class="separador"></div>
							<input type="text" id="waypoints2" class="form-control">
							<div class="separador"></div>
							<input type="text" id="waypoints3" class="form-control">
						</div>
  					</div>
  			
  					<div class="form-group">
    					<label for="inputEmail3" class="col-sm-2 control-label">
    						<?php echo $texto['vehiculo_consumo'];?>
    					</label>
    					<div class="col-sm-10">
    						<div class="input-group">
      							<input type="text" class="form-control" id="consumo" value="8">
      							<div class="input-group-addon"><?php echo $texto['litros_kilometros'];?></div>
							</div>
						</div>
  					</div>
  			
  					<div class="form-group">
    					<label for="inputEmail3" class="col-sm-2 control-label">
    						<?php echo $texto['precio_combustible'];?>
    					</label>
    					<div class="col-sm-10">
							<input type="text" class="form-control" id="combustible" value="9">
						</div>
  					</div>
  			
  					<div class="form-group">
    					<label for="inputEmail3" class="col-sm-2 control-label">
    					</label>
    					<div class="col-sm-10">
							<a onclick="calcRoute(); document.getElementById('como_llegar').disabled=false;" class="btn btn-default show_hide2"><?php echo $texto['ruta']?></a>
						</div>
  					</div>
  			
  				<div class="slidingDiv2">
	  				<div class="form-group">
		    			<label for="inputEmail3" class="col-sm-2 control-label">
		    			</label>
		    			<div class="col-sm-10">
							<div id="directions_panel" class="alert alert-success"></div>
						</div>
	  				</div>
  			
	    		
		    		<div class="form-group">
	    				<label for="inputEmail3" class="col-sm-2 control-label">
	    				</label>
	    				<div class="col-sm-10">
							<a id="a" class='show_hide btn btn-default' title='<?php echo $texto['leer_mas']?>'><?php echo $texto['como_llegar']?></a>
						</div>
	  				</div>
		    		<div class='slidingDiv'>
						<div id="directions-panel"></div>
					</div>
				</div>
				</form>
				</div>
				-->
				<div class="col-md-12">
					<center>
						<a href="javascript:window.history.back();" type="submit" class="btn btn-hotel boton-redondo" title="<?php echo $texto['volver']?>" rel="tooltip">
							<span class="icon-chevron-left"></span>
						</a>
					</center>
				</div>
				<!--
				<div class="col-md-6">
					<?php echo form_open('reserva/datos');?>
					<input type="hidden" name="entrada" value="<?php echo $this->input->post('entrada') ?>">
					<input type="hidden" name="salida"  value="<?php echo $this->input->post('salida') ?>">
					<input type="hidden" name="adultos" value="<?php echo $this->input->post('adultos') ?>">
					<input type="hidden" name="menores" value="<?php echo $this->input->post('menores') ?>">
					<input type="hidden" name="habitacion" value="<?php echo $this->input->post('id') ?>">
					<button type="submit" class="btn btn-hotel btn-lg btn-block">Seleccionar</button>	 	
					<?php echo form_close(); ?>      
				</div>
				-->
				
				
			</div>
			<?php } ?>
			</div>
		</div>
	</div>
</div>							
