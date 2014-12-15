<?php
$id_hotel=$_COOKIE['id_hotel'];
if($articulos){ 
if($cantidad_categorias==2 || $cantidad_categorias==1){?>
	<div class='row'>
	<?php foreach ($articulos as $articulo) {
	if($cantidad_categorias==2){
		$class="col-lg-6 col-md-6 col-sm-12 pm-column-spacing";
	}else{
		$class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 col-sm-12 pm-column-spacing";
	}
	?>
	<div class="<?php echo $class?>">
		<div class="panel panel-hotel">
			<div class="panel-body">
				<div class="pm-news-post-container">
					<div class="pm-news-post-image" style="background-image:url('<?php echo base_url().'assets/uploads/articulos/'.$articulo->archivo_url?>');">
						<div class="pm-news-post-title">
							<p class="pm-titulo">
								<?php echo $articulo->titulo;?>
							</p>
						</div>
					</div>
					<div class="pm-news-post-desc-container">
						<p class="pm-news-post-excerpt">
							<?php echo $articulo->subtitulo;?> 
						</p>
						<a class="btn btn-hotel boton-redondo-medium pull-right" href="<?php echo base_url().'index.php/'.$this->uri->segment(1).'/categoria/articulos/'.$articulo->id_categoria.'/'.$id_hotel; ?>" title="<?php echo $texto['leer_mas'];?>">
					    	<span class="icon-chevron-down"></span>
					    </a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php } ?>
	</div>
<?php }else if($cantidad_categorias==3){ ?>
	<div class='row'>
	<?php foreach ($articulos as $articulo) {?>
		<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 pm-center pm-column-spacing">
			<div class="panel panel-hotel">
				<div class="panel-body">
					<?php if($articulo->archivo_url!=""){?>
						<img class="img-responsive pm-image-border alignnone wp-image-200 size-full" src="<?php echo base_url().'assets/uploads/articulos/'.$articulo->archivo_url?>" alt="image1" width="357" height="147">
					<?php } ?>
					<h6 style="text-align: center;"><?php echo $articulo->titulo;?></h6>
					<p style="text-align: center;"><?php echo $articulo->subtitulo;?></p>
					&nbsp;
					<a class="btn btn-hotel boton-redondo-medium pull-right" href="<?php echo base_url().'index.php/'.$this->uri->segment(1).'/categoria/articulos/'.$articulo->id_categoria.'/'.$id_hotel; ?>" title="<?php echo $texto['leer_mas'];?>">
			    		<span class="icon-chevron-down"></span>
			    	</a>
			     </div>
			</div>
		</div>
	<?php } ?>
	</div>
<?php } ?>
<?php } ?>
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
	