<div class="col-md-8 col-md-offset-2">
	<div class="panel panel-hotel">
		<!--<div class="panel-heading">Habitación</div>-->
		<div class="panel-body">
			<?php $noches=restarFechasFormulario($this->input->post('salida'),$this->input->post('entrada'));?>
			<div class="panel panel-hotel">
			<table class="table table-hover">
				<tr>
					<th><i class="fa fa-sign-in"></i> <?php echo $texto['entrada']?>: </th>
					<td><?php echo $this->input->post('entrada') ?></td>
					<th><i class="fa fa-sign-out"></i> <?php echo $texto['salida']?>: </th>
					<td><?php echo $this->input->post('salida') ?></td>
				</tr>
				<tr>
					<th><i class="fa fa-user"></i> <?php echo $texto['adultos']?>: </th>
					<td><?php echo $this->input->post('adultos') ?></td>
					<th><i class="fa fa-child"></i> <?php echo $texto['menores']?>: </th>
					<td><?php echo $this->input->post('menores') ?></td>
				</tr>
				<tr>
					<th><i class="fa fa-building"></i> <?php echo $texto['hotel']?>: </th>
					<td>
						<?php
						foreach ($hotel as $hotel2) {
							echo $hotel2->hotel;
							$id_hotel=$hotel2->id_hotel;
						} 
						?>
					</td>
					<th><i class="fa fa-moon-o"></i> <?php echo $texto['noches']?>: </th>
					<td><?php echo $noches;?></td>
				</tr>
			</table>
			</div>
		</div>
	</div>
		
			
			
		<?php if($habitaciones){?>
	<div class="panel panel-hotel">
		<div class="panel-heading"><?php echo $texto['seleccione_habitacion']?></div>
			<?php echo form_open('reserva/datos');?>
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
					}?> 
				<div class="panel-body">
        			<div class="col-md-3 text-center  nombre-habitacion">
        				<h3><small> <?php echo $habitacion->habitacion; ?> </small></h3>
        				<p class="list-group-item-text"> 
							<a href="<?php echo base_url().'index.php/'.$this->uri->segment(1).'/habitacion/view/'.$habitacion->id_habitacion.'/'.$id_hotel;?>" class="btn btn-hotel boton-redondo-medium a-seleccion-habitacion" title="<?php echo $texto['leer_mas']?>" rel="tooltip">
								<span class="icon-chevron-down"></span>
							</a>
							<a href="#" class="btn btn-hotel boton-redondo-medium" title="<?php echo $texto['email']?>" rel="tooltip" data-toggle="modal" data-target="#habitacion<?php echo $habitacion->id_habitacion?>">
								<span class="icon-paperplane"></span>
							</a>
                    	</p>
        			</div>
          			<div class="media col-md-5 thumbnail">
          				<div class="caption">
							<h4><?php echo $texto['habitacion']?></h4>
							<!--<p>comentario</p>-->
							<p>
								<a href="<?php echo base_url().'index.php/'.$this->uri->segment(1).'/habitacion/galeria/'.$habitacion->id_habitacion.'/'.$id_hotel?>" class="btn btn-default" rel="tooltip" title="<?php echo $texto['ver_fotos']?>"><span class="icon-play"></span></a>
							</p>
						</div>
						<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
							<?php	$imagenes_habitacion=$this->imagenes_habitacion_model->getImagenes($habitacion->id_habitacion); ?>
							<div class="carousel-inner">
								<?php 
								$i=0;
								if($imagenes_habitacion){
								foreach ($imagenes_habitacion as $imagenes) { ?>
									<a href="#" class="item <?php if($i==0){echo 'active';}?>" class="thumbnail">
										<center>								
											<img alt="slide" src="<?php echo base_url().'assets/uploads/habitaciones/'.$imagenes->imagen;?>" width="300" height="175"> <!--style="max-width: 160px; max-height: 120px;"-->
										</center>
									</a>
									<?php $i=$i+1?>
								<?php }} ?>
							</div>
						</div>
					</div>
                	
			                	
                	<div class="col-md-4 text-center">
                		
                		<h3>
							<small> 
								<?php echo $texto['nro_habitaciones'] ?>
							</small>
						</h3>
						<?php
							$cantidad=$habitacion->cantidad;
								
							if(isset($reservas_habitacion)){
								foreach ($reservas_habitacion as $disp) {
									if($habitacion->id_habitacion==$disp->id_habitacion){
										$cantidad=$cantidad-$disp->cantidad; 
									}
								}	
							}
									
							if(isset($disponibilidades)){
								foreach ($disponibilidades as $disponibilidad) {
									if($habitacion->id_habitacion==$disponibilidad->id_habitacion){	
										$cantidad=0;
									}
								}
							}																			
						?>
						
						<?php
                		$precio=$habitacion->precio;
						$precio_con_descuento=0;
						
                		if(isset($tarifas)){
                			foreach ($tarifas as $tarifa) { 
								if($tarifa->id_habitacion==$habitacion->id_habitacion){
									$datos=array('id_tipo_tarifa'=>$tarifa->id_tipo_tarifa,
												 'valor'=>$tarifa->valor,
												 'precio'=>$habitacion->precio); 
									$precio=$this->tarifas_temporales_model->calcular_precio($datos);
									if($precio<$habitacion->precio){
										$precio_con_descuento=1;
									}
                				}							
							}	
                		} 
						?>
						
						<select name="habitacion<?php echo $habitacion->id_habitacion?>" class="form-control habitacion" onChange="validarHabitacion()">
							<?php for ($i=0; $i <= $cantidad; $i++) { ?>
								<option value="<?php echo $i;?>">
									<?php 
										if($cantidad==0){
											echo $texto['sin_disponibilidad'];
										}else{
											echo $i;
											if($i>0){
												foreach ($cambios as $cambio) {
													echo "(".$cambio->simbolo." ".number_format($precio*$i/$cambio->valor*$noches, 2, ',', ' ').")";
												}	
											}	
										}											 
									?>
								</option>
							<?php }?>
						</select>
                		
                		
                		<?php foreach ($cambios as $cambio) { ?>
						<?php if($precio_con_descuento==1){ ?>
						<del><h4><small> 
                				<?php echo $cambio->abreviatura ; ?>
                			</small>
                    			<?php echo $cambio->simbolo; ?>  
                    			<?php echo number_format($habitacion->precio/$cambio->valor*$noches, 2, ',', ' '); ?></h4></del>							
						<?php } ?>
						<?php if($precio/$cambio->valor*$noches>1000){
							echo "<h3>";
						}else{
							echo "<h2>";
						}; ?>                			
                		
                			<small> 
                				<?php echo $cambio->abreviatura ; ?>
                			</small>
                    			<?php echo $cambio->simbolo; ?>  
                    			<?php echo number_format($precio/$cambio->valor*$noches, 2, ',', ' '); ?>
                    			<input type="hidden" name="precio<?php echo $habitacion->id_habitacion ?>" value="<?php echo $precio ?>">
                    			<?php 
                    			foreach ($hoteles as $hotel) {
                    				$mostrar_moneda=$hotel->monedas;
								}
                    				
	                    			if($mostrar_moneda==1){ ?>
	                    			<a href="#" class="btn btn-hotel btn-xs" title="<?php echo $texto['monedas']?>" rel="tooltip" data-toggle="modal" data-target="#monedas">
										<i class="fa fa-usd icons-white"></i>
										<!--<img style="width: 16px" src="<?php echo base_url().'assets/uploads/moneda-01.png'?>">-->
									</a>
									<?php }
								 ?>
						<?php if($precio/$cambio->valor*$noches>1000){
							echo "</h3>";
						}else{
							echo "</h2>";
						}; ?>  
						
						<div class="row">
							<div class="col-md-1">
							</div> 
							<div class="col-md-10" style="text-align: initial;">
								<div class="stars" >
		                        	<?php echo $texto['adultos']?>: <?php 
		                        	for ($i=0; $i < $habitacion->adultos; $i++) { 
										echo "<i rel='tooltip' title='".$texto['maximo_adultos']."' class='fa fa-user'></i> ";
									}
		                        	?>  
		                    	</div>
		                    	<div class="stars" >
		                    		<?php echo $texto['menores']?>: <?php 
		                    		if( $habitacion->menores>0 ){
		                    			for ($i=0; $i < $habitacion->menores; $i++) { 
											echo "<i rel='tooltip' title='".$texto['maximo_menores']."' class='fa fa-child'></i> ";
										}	
		                    		}else{
		                    			echo $texto['sin_menores'];
		                    		}
		                    		?>
		                    		<?php } ?>
		                    	</div>
		                    </div>
		                    <div class="col-md-1">
							</div> 
						</div>
                    <!--</div>
                    <div class="col-md-3">-->
						
                	</div>	
            </div>
            <hr>
         	<?php } ?>	
         	
         	<input type="hidden" name="entrada" value="<?php echo $this->input->post('entrada') ?>">
			<input type="hidden" name="salida" value="<?php echo $this->input->post('salida') ?>">
			<input type="hidden" name="adultos" value="<?php echo $this->input->post('adultos') ?>">
			<input type="hidden" name="menores" value="<?php echo $this->input->post('menores') ?>">
			<input type="hidden" name="hotel" value="<?php echo $this->input->post('hotel') ?>">
			<div class="panel-body">
			<div class="col-xs-4">
				</div>
				<div class="col-xs-4">
					<center>
						<button type="submit" name="reservar" value="Seleccione una opción" class="btn btn-hotel boton-redondo">
							<span class="icon-ok"></span>
						</button>
					</center>
				</div>
				<div class="col-xs-4">
					<label id="habitaciones" class="pull-right"></label>
				</div>
			</div>
         	
         	<?php echo form_close(); ?>
         	
        	<?php }else{ ?>
			<h1 class="text-center"><?php echo $texto['no_habitaciones'];?></h1>
			<h3 class="text-center"><?php echo $texto['otras_opciones'];?></h3>
			<div class="col-xs-12">
			<div class="offer offer-hotel">
				<div class="shape">
					<div class="shape-text">
						<span class="icon-star"></span>						
					</div>
				</div>
				<div class="offer-content">
					<div class="row">
						<?php foreach ($hoteles_menu as $hotel) { ?>
	    				<?php if($id_hotel!=$hotel->id_hotel){ ?>
	    					<div class="col-xs-3">
	    					<a href="<?php echo base_url().'index.php/'.$this->uri->segment(1).'/inicio/hotel/'.$hotel->id_hotel ?>">
	    						<img src="<?php echo base_url().'assets/uploads/logos/'.$hotel->logo_url;?>" class="logo_img_menu">
	    					</a>
	    					</div>
	    				<?php } ?>
						<?php } ?>
					</div>
				</div>
			</div>
			</div>
			</div>
			</div>
			
		
			<?php } ?>
			
		</div>
	</div> 

	






<!---------------------------------------------------------------------------------
-----------------------------------------------------------------------------------
					
						Modal monedas 

-----------------------------------------------------------------------------------
---------------------------------------------------------------------------------->	

<div class="modal fade" id="monedas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  	<div class="modal-dialog">
    	<div class="modal-content">
      		<div class="modal-header">
        		<h4 class="modal-title" id="myModalLabel"><?php echo $texto['monedas']?></h4>
      		</div>
      		<?php if(isset($monedas)){ ?>
			<form method="post" action="<?php echo base_url().'index.php'.$this->uri->segment(1).'reserva/habitacion' ?>">
	  				<input type="hidden" name="entrada" value="<?php echo $this->input->post('entrada') ?>">
						<input type="hidden" name="salida" value="<?php echo $this->input->post('salida') ?>">
						<input type="hidden" name="adultos" value="<?php echo $this->input->post('adultos') ?>">
						<input type="hidden" name="menores" value="<?php echo $this->input->post('menores') ?>">
						<input type="hidden" name="hotel" value="<?php echo $this->input->post('hotel') ?>">
				<center>
	  			<ul class="list-inline">
	  			<?php foreach ($monedas as $moneda) { ?>
					<li>
						<center>
							<input class="moneda" name="boton1" type="image" title="<?php echo $moneda->moneda;?> - <?php echo $moneda->abreviatura;?>" rel="tooltip" src="<?php echo base_url().'assets/uploads/monedas/'.$moneda->imagen;?>" onclick="document.cookie = 'moneda=<?php echo $moneda->id_moneda ?>'">
						</center>
						<p class="moneda-leyenda">
							<?php echo $moneda->moneda;?> - <?php echo $moneda->abreviatura;?><br>
						</p>
					</li>
				<?php } ?>
				</ul>
				</center>
				</form>  
			<?php } ?>
    	</div>
  	</div>
 </div>
	


<!---------------------------------------------------------------------------------
-----------------------------------------------------------------------------------
					
						Modal habitaciones 

-----------------------------------------------------------------------------------
---------------------------------------------------------------------------------->	

<?php foreach ($habitaciones as $habitacion) { ?>
<div class="modal fade" id="habitacion<?php echo $habitacion->id_habitacion?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  	<div class="modal-dialog">
    	<div class="modal-content">
      		<div class="modal-header">
        		<h4 class="modal-title" id="myModalLabel"><?php echo $texto['habitacion']?> : <?php echo $habitacion->habitacion?></h4>
      		</div>
      		<form method="post" class="form-horizontal" role="form" accept-charset="utf-8" action="<?php echo base_url().'index.php/'.$this->uri->segment(1).'/consulta/email_habitacion'?>"/>
      		<div class="modal-body">
      			<div class="form-group">
    				<label for="nombre" class="col-sm-2 control-label"><?php echo $texto['mensaje']?></label>
    				<div class="col-sm-10">
      				<textarea class="form-control" name="consulta" rows="3" placeholder="<?php echo $texto['ingrese'].' '.$texto['mensaje']?>"  required></textarea>
    				</div>
  				</div>
      			<div class="form-group">
    				<label for="nombre" class="col-sm-2 control-label"><?php echo $texto['email']?></label>
    				<div class="col-sm-10">
      				<input class="form-control" name="email" type="email" placeholder="<?php echo $texto['ingrese'].' '.$texto['email']?>"  required>
    				</div>
  				</div>
  				<div class="form-group">
    				<label for="nombre" class="col-sm-2 control-label"><?php echo $texto['nombre']?></label>
    				<div class="col-sm-10">
    				<input type="text" class="form-control" name="nombre" placeholder="<?php echo $texto['ingrese'].' '.$texto['nombre']?>"  required>
    				</div>
  				</div>
  				<div class="form-group">
    				<label for="apellido" class="col-sm-2 control-label"><?php echo $texto['apellido']?></label>
    				<div class="col-sm-10">
    				<input type="text" class="form-control" name="apellido" placeholder="<?php echo $texto['ingrese'].' '.$texto['apellido']?>"  required>
    				</div>
  				</div>  
  					<input type="hidden" name="id_habitacion" value="<?php echo $habitacion->id_habitacion?>">				
  					<input type="hidden" name="habitacion" value="<?php echo $habitacion->habitacion?>">
      			</div>
      			
      			<div class="modal-footer">
      				<input type="hidden" name="id_hotel" value="<?php echo $id_hotel?>" >
        			<button type="button" class="btn btn-hotel boton-redondo-medium" data-dismiss="modal" title="<?php echo $texto['cerrar']?>">
        				<span class="icon-remove"></span>
        			</button>
        			<button type="submit" class="btn btn-hotel boton-redondo-medium" title="<?php echo $texto['email']?>">
        				<span class="icon-paperplane"></span>
        			</button>
      			</div>
      		</div>
      		</form>
    	</div>
  	</div>

<?php } ?>



<script>
	$(document).ready(function(){
		validarHabitacion();
 	});
</script>

