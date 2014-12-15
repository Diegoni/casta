<?php $id_hotel=$_COOKIE['id_hotel']?>
  	<aside class="flotante">
    	<div class="btn-toolbar">
			<div class="btn-group">
				<a href="javascript:agregar()" class="btn btn-footer btn-lg" title='<?php echo $texto['favoritos']?>'><i class="icon-favoritefile"></i></a>
		      	<a href="#" class="btn btn-footer btn-lg" title='<?php echo $texto['enviar_consulta']?>' data-toggle="modal" data-target="#email"><i class="icon-emailalt"></i></a>
		      	<a href="#" class="btn btn-footer btn-lg" title='<?php echo $texto['telefono']?>' data-toggle="modal" data-target="#telefono"><i class="icon-phonealt"></i></a>
		      	<a href="#" class="btn btn-footer btn-lg" title='<?php echo $texto['direccion']?>' data-toggle="modal" data-target="#direccion"><!--<i class="icon-map"></i>--><img style="width: 24px" src="<?php echo base_url().'assets/uploads/brujula-01.png'?>"> </a>
		      	<!--<a href="<?php echo base_url().'index.php/hoteles/como_llegar/'.$id_hotel; ?>" class="btn btn-footer btn-lg" title='<?php echo $texto['direccion']?>'><i class="icon-map-marker"></i></a>-->
		      	<a href="#" class="scrollup btn btn-footer btn-lg" title='<?php echo $texto['arriba']?>'><span class="icon-arrow-up"></span></a>
		  	</div>
		</div>      
	</aside>
  	  	
  	
  	<div class="row">
  		<div class="col-md-12">
  			<!--<div class="panel panel-default">
  				<div class="panel-body">-->
  					
  					<div class="row">
  					<?php 
  					$telefono=array();
					$direccion=array();
					foreach ($hoteles as $hotel) {
  						if (!(in_array($hotel->telefono, $telefono))) {
    						$telefono[]=$hotel->telefono;	
						} 
						if (!(in_array($hotel->calle." - ".$hotel->provincia, $direccion))) {
							$direccion[]=$hotel->calle." - ".$hotel->provincia;
						}	
					} ?>
					<div class="col-md-4">			
					<center>
						<h4><i class="fa fa-phone icono-footer"></i></h4>
						<p class="texto-footer">
						<?php 
						foreach ($telefono as $key => $value) {
							echo $value."<br>";
						}
						?>
						</p>
					</center>
					</div>
					<div class="col-md-4">
					<center>
	   					<h4><i class="fa fa-map-marker icono-footer"></i></h4> 
						<p class="texto-footer">
							<?php 
							foreach ($direccion as $key => $value) {
								echo $value."<br>";
							}
							?>
						</p>
					</center>
					</div>
					<div class="col-md-4">
					<center>
   						<h4><i class="fa fa-envelope-o icono-footer"></i></h4>
   						<p class="texto-footer">
						<?php 
						if($emails_hotel){
							foreach ($emails_hotel as $email) {
								echo $email->email."<br>";
							}	
						}
						?>
						</p>
					</center>
					</div>
				</div>
   				<!--		
    			</div>
			</div>-->
  		</div>
  	</div>
	<div class="divider"><br><br></div>
	<div class="row">
		<div class="col-md-12">
		<div class="afip">
			<?php
			foreach ($hoteles as $hotel) {
				if($hotel->usar_codigo==1 && $hotel->codigo_afip!=""){
					$class_copyright = "pull-right";
				?>
					<a href="<?php echo $hotel->codigo_afip ?>" target="_blank">
						<img src="<?php echo base_url().'assets/uploads/afip.jpg' ?>" class="img-responsive img-afip">
					</a>
				<?php
				}else{
					$class_copyright = "pull-left";
				}
			}
			?>
			
		</div>
		
		<div class="copyright <?php echo $class_copyright ?>">
			<small>
			<a href="http://tmsgroup.com.ar" target="_blank">
				<span class="icon-copyright"></span> <?php echo $texto['copyright']?>
			</a>
			</small>
		</div>
		</div>	
	</div> 		
    
</div><!-- cierra el <div class="container"> -->
	


</body>
</html>
