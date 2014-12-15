<?php $id_hotel=$_COOKIE['id_hotel']; ?>
	<div class="col-md-8">
		<div class="panel panel-hotel">
			<!-- al final del archivo esta el codigo para un menu dinamico en vez de este estatico-->
			<div class="panel-heading">
				<?php 
				foreach ($hoteles as $hotel) {
					$provincia=$hotel->provincia;
				}
				echo $provincia." ".$texto['te_espera'];
				?>
			</div>
			
	  	<div class="panel-body" id="panel-carrusel">
		<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
			
			<!-- indicadores del carrusel -->
			<ol class="carousel-indicators">
				<?php 
				$i=0;
				if($imagenes_carrusel){
				foreach ($imagenes_carrusel as $imagenes) { ?>
				<li data-target="#carousel-example-generic" data-slide-to="<?php echo $imagenes->orden-1;?>" class="<?php if($i==0){echo 'active';}?>"></li>
				<?php $i=$i+1?>
				<?php } ?>
			</ol>
			
			<!-- imagenes del carrusel -->
			<div class="carousel-inner">
				<?php 
				$i=0;
				foreach ($imagenes_carrusel as $imagenes) { ?>
				<div class="item <?php if($i==0){echo 'active';}?>">
					<center>
          				<img alt="slide" src="<?php echo base_url().'assets/uploads/carrusel/'.$imagenes->imagen;?>" width="600" height="350">
          			</center>
					<?php if($imagenes->descripcion!=""){ ?>
						<div class="carousel-caption">
							<p><?php echo $imagenes->descripcion;?></p>
						</div>
					<?php } ?>
        		</div>
        		<?php $i=$i+1?>
				<?php } ?>
			</div>
			<?php } ?>
			<!-- direcciones del carrusel -->
      		<a class="left carousel-control" href="#carousel-example-generic" data-slide="prev">
        		<span class="glyphicon glyphicon-chevron-left"></span>
      		</a>
      		<a class="right carousel-control" href="#carousel-example-generic" data-slide="next">
        		<span class="glyphicon glyphicon-chevron-right"></span>
      		</a>
      		
    	</div>
    	</div>
    	</div>
	</div>
</div>	



<!-- Menú dinamico con las categorias
				<?php 
				$i=0;
				if(!empty($categorias)){
				foreach ($categorias as $categoria) { 
					if($i<3){?>
					<a class="panel-menu" href="<?php echo base_url().'index.php/categoria/articulos/'.$categoria->id_categoria.'/'.$id_hotel; ?>">
						<?php echo $categoria->categoria ?>
					</a>	
				<?php 
					}
				$i=$i+1;
				}} ?>
				-->