<?php $id_hotel=$_COOKIE['id_hotel']?>
<div class="row">
<div class="col-md-8 col-md-offset-2">
	<div class="panel panel-hotel">
		<div class="panel-heading"><?php echo $texto['habitaciones']?></div>
		<?php if($habitaciones){?>
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
				<div class="panel-body">
        			<div class="col-md-4 text-center vertical-middel nombre-habitacion">
        				<h3><small> <?php echo $habitacion->habitacion; ?> </small></h3>
        			</div>
          			<div class="media col-md-4 thumbnail">
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
											<img alt="slide" src="<?php echo base_url().'assets/uploads/habitaciones/'.$imagenes->imagen;?>" width="300" height="175">	
										</center>								
										
									</a>
									<?php $i=$i+1?>
								<?php }} ?>
							</div>
						</div>
					</div>    	
                	<div class="col-md-4 text-center vertical-middel" style="margin-top: 6%">
                		
                		<div class="stars button-seleccion-habitacion">
							<a href="<?php echo base_url().'index.php/'.$this->uri->segment(1).'/habitacion/view/'.$habitacion->id_habitacion.'/'.$id_hotel;?>" class="btn btn-hotel boton-redondo-medium a-seleccion-habitacion" title="<?php echo $texto['leer_mas']?>" rel="tooltip">
								<span class="icon-chevron-down"></span>
							</a>
							<a href="#" class="btn btn-hotel boton-redondo-medium" title="<?php echo $texto['email']?>" rel="tooltip" data-toggle="modal" data-target="#habitacion<?php echo $habitacion->id_habitacion?>">
								<span class="icon-paperplane"></span>
							</a> 	
						</div>
                		
                		<div class="stars" >
                        	<?php echo $texto['adultos']?>: <?php 
                        	for ($i=0; $i < $habitacion->adultos; $i++) { 
								echo "<i rel='tooltip' title='".$texto['maximo_adultos']."' class='fa fa-user'></i> ";
							}?>  
                    	</div>
                    	<div class="stars" >
                    		<?php echo $texto['menores']?>: <?php 
                    		if( $habitacion->menores>0 ){
                    			for ($i=0; $i < $habitacion->menores; $i++) { 
									echo "<i rel='tooltip' title='".$texto['maximo_menores']."' class='fa fa-child'></i> ";
								}	
                    		}else{
                    			echo $texto['sin_menores'];
                    		}?>
                    	</div>
                    </div>	
                </div>
            <hr>
         	<?php } ?>	
         	<div class="panel-body">
					<center>
						<a href="javascript:window.history.back();" type="submit" class="btn btn-hotel boton-redondo" title="<?php echo $texto['volver']?>" rel="tooltip">
							<span class="icon-chevron-left"></span>
						</a>
					</center>
			</div>

         	
        	<?php }else{ ?>
			<h1 class="text-center">No hay habitaciones disponibles</h1>
			<h3 class="text-center">Otras opciones disponibles</h3>
			<div class="col-xs-12">
			<div class="offer offer-hotel">
				<div class="shape">
					<div class="shape-text">
						top								
					</div>
				</div>
				<div class="offer-content">
					<h3 class="lead">
						Habitación
					</h3>
					<p>Descripción</p>
				</div>
			</div>
			</div>
		
			<?php } ?>

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
    				<label for="email" class="col-sm-2 control-label"><?php echo $texto['email']?></label>
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
    				<input type="text" class="form-control" name="apellido" placeholder="<?php echo $texto['ingrese'].' '.$texto['apellido']?>" required>
    				</div>
  				</div>  
  					<input type="hidden" name="id_habitacion" value="<?php echo $habitacion->id_habitacion?>">				
  					<input type="hidden" name="habitacion" value="<?php echo $habitacion->habitacion?>">
  					<input type="hidden" name="id_hotel" value="<?php echo $id_hotel?>">
      			</div>
      			
      			<div class="modal-footer">
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
</div>
<?php } ?>



<script>
	$(document).ready(function(){
		validarHabitacion();
 	});
</script>

