	<div class="row">
	<div class="col-md-8 col-md-offset-2">
		<div class="panel panel-hotel">
			<div class="panel-heading"><?php echo $texto['hotel']?></div>
		  	<div class="panel-body">
		  		<?php 
					foreach ($habitaciones as $habitacion) {
						$id=$habitacion->id_hotel;
					}
				?>
				<?php	$imagenes_habitacion=$this->imagenes_hotel_model->getImagenes($id); ?>
 				<div id="main_area">
                <!-- Slider -->
                <div class="row">
                    <div class="col-xs-12" id="slider">
                        <!-- Top part of the slider -->
                        <div class="row">
                            <div class="col-sm-12 " id="carousel-bounding-box">
                                <div class="carousel slide" id="myCarousel">
                                    <!-- Carousel items -->
                                    <div class="carousel-inner">
                                    	<?php $i=0; 
                                    	if($imagenes_habitacion){
                                    	?>
                                    	<?php foreach ($imagenes_habitacion as $imagenes) { ?>
                                        <div class="item <?php if($i==0){echo "active";}?>" data-slide-number="<?php if($i<10){echo '0'.$i;}else{ echo $i;} ?>">
                                        	<a class="fancybox" rel="ligthbox" href="<?php echo base_url().'assets/uploads/hoteles/'.$imagenes->imagen;?>">
                                        		<center>
                                        			<img src="<?php echo base_url().'assets/uploads/hoteles/'.$imagenes->imagen;?>" width="600" height="350">
                                        		</center>
                                        	</a>
                                        </div>
                                        
                                        <?php $i=$i+1; ?>
                                        <?php }} ?>
                                        
                                    </div><!-- Carousel nav -->
                                    <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
                                        <span class="glyphicon glyphicon-chevron-left"></span>                                       
                                    </a>
                                    
                                    <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
                                        <span class="glyphicon glyphicon-chevron-right"></span>                                       
                                    </a>                                
                                    </div>
                            </div>

                         </div>
                    </div>
                </div><!--/Slider-->

                <div class="row hidden-xs" id="slider-thumbs">
                        <!-- Bottom switcher of slider -->
                        <ul class="hide-bullets">
                        	<?php $i=0;
							if($imagenes_habitacion){?>
                            <?php foreach ($imagenes_habitacion as $imagenes) { ?>
                            <li class="col-sm-2">
                                <a class="thumbnail" id="carousel-selector-<?php if($i<10){echo '0'.$i;}else{ echo $i;}; ?>">
                                	<center>
                                		<img src="<?php echo base_url().'assets/uploads/hoteles/'.$imagenes->imagen;?>" class="img-rounded" width="87" height="87">
                                	</center>
                                </a>
                            </li>
                            <?php $i=$i+1;?>
                            <?php }} ?>
                        </ul>                 
                </div>
        </div>
				
			</div>
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




<!--
		  		<div class='list-group'>
					<?php 
					foreach ($habitaciones as $habitacion) {
						$id=$habitacion->id_hotel;
					}?>
					<?php	$imagenes_habitacion=$this->imagenes_hotel_model->getImagenes($id); ?>
					<?php foreach ($imagenes_habitacion as $imagenes) { ?>
		            <div class='col-md-6'>
		                <a class="fancybox" rel="ligthbox" href="<?php echo base_url().'assets/uploads/hoteles/'.$imagenes->imagen;?>">
		                    <img class="img-responsive gris" alt="" src="<?php echo base_url().'assets/uploads/hoteles/'.$imagenes->imagen;?>" />
		                    <div class='text-right'>
		                        <small class='text-muted'><?php echo $imagenes->descripcion;?></small>
		                    </div>
		                </a>
		            </div> 
		            <?php } ?>
				</div>
				-->
				
	<!-- not really needed, i'm using it to center the gallery. -->
		