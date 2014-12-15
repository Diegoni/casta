<?php
$id_hotel=$_COOKIE['id_hotel'];
if($articulos){ 
if($cantidad_categorias==3 || $cantidad_categorias==2 || $cantidad_categorias==1){
	echo "<div class='row'>";
	 	foreach ($articulos as $articulo) { 
	 		//echo $articulo->id_categoria;?>
			<div class="col-md-<?php echo 12/$cantidad_categorias?>">
				<div class="panel panel-hotel">
			  		<div class="panel-heading"><?php echo $articulo->categoria;?></div>
			  		<div class="panel-body">
			  			<?php if($articulo->archivo_url!=""){?>
			    			<img class="img-circle img-banner" src="<?php echo base_url().'assets/uploads/articulos/'.$articulo->archivo_url?>">
			    		<?php } ?>
			    		<div class="text-banner">
			    			
			    			<?php
			    			foreach ($configs_articulos as $configs_articulo) {
			    				$usar_limite	= $configs_articulo->usar_limite;
								$max_con_foto	= $configs_articulo->max_con_foto;
								$max_sin_foto	= $configs_articulo->max_sin_foto;
							}
							
							if($usar_limite==1){
								if($articulo->archivo_url!=""){
			    					echo myTruncate($articulo->articulo, $max_con_foto, '>', '>...');	
			    				}else{
			    					echo myTruncate($articulo->articulo, $max_sin_foto, '>', '>...');
			    				}	
							}else{
								echo $articulo->articulo;
							}
			    			
			    			?>
			    		</div>
			    		<a class="btn btn-hotel boton-redondo-medium pull-right" href="<?php echo base_url().'index.php/categoria/articulos/'.$articulo->id_categoria.'/'.$id_hotel; ?>" title="<?php echo $texto['leer_mas'];?>">
			    			<span class="icon-chevron-down"></span>
			    		</a>
					</div>
				</div>
			</div>			
	<?php }
	echo "</div>";
}else if($cantidad_categorias==4 || $cantidad_categorias==6){
	$i=0;
	foreach ($articulos as $articulo) { 
		if($i==0 || $cantidad_categorias*0.5==$i){
			echo "<div class='row'>";
		} ?>		
			<div class="col-md-<?php echo 24/$cantidad_categorias?>">
				<div class="panel panel-hotel">
			  		<div class="panel-heading"><?php echo $articulo->categoria;?></div>
			  		<div class="panel-body">
			  			<?php if($articulo->archivo_url!=""){?>
			    			<img class="img-circle img-banner" src="<?php echo base_url().'assets/uploads/articulos/'.$articulo->archivo_url?>">
			    		<?php } ?>
			    		<div class="text-banner">
			    			<?php echo $articulo->articulo; ?>
			    		</div>
			    		<a class="btn btn-default btn-xs" href="<?php echo base_url().'index.php/categoria/articulos/'.$articulo->id_categoria.'/'.$id_hotel; ?>"><?php echo $texto['leer_mas'];?></a>
					</div>
				</div>
			</div>			
	<?php
		if(($cantidad_categorias*0.5)-1==$i || $cantidad_categorias-1==$i){
			echo "</div>";
		} 
	$i=$i+1;
	}//cierra el foreach 
}else if($cantidad_categorias==5){ 
	$i=0;
	foreach ($articulos as $articulo) { 
		if($i==0 || $i==3){
			echo "<div class='row'>";
		} ?>		
			<div class="col-md-<?php if($i<3){ echo 4;}else{ echo 6;}?>">
				<div class="panel panel-hotel">
			  		<div class="panel-heading"><?php echo $articulo->categoria;?></div>
			  		<div class="panel-body">
			  			<?php if($articulo->archivo_url!=""){?>
			    			<img class="img-circle img-banner" src="<?php echo base_url().'assets/uploads/articulos/'.$articulo->archivo_url?>">
			    		<?php } ?>
			    		<div class="text-banner">
			    			<?php echo $articulo->articulo; ?>
			    		</div>
			    		<a class="btn btn-default btn-xs" href="<?php echo base_url().'index.php/categoria/articulos/'.$articulo->id_categoria.'/'.$id_hotel; ?>"><?php echo $texto['leer_mas'];?></a>
					</div>
				</div>
			</div>			
	<?php
		if($i==2 || $i==4){
			echo "</div>";
		} 
	$i=$i+1;
	}//cierra 

}else if($cantidad_categorias>6){
	$i=0;
	foreach ($articulos as $articulo) {
		if($i<6){	 
		if($i==0 || 6*0.5==$i){
			echo "<div class='row'>";
		} ?>		
			<div class="col-md-4">
				<div class="panel panel-hotel">
			  		<div class="panel-heading"><?php echo $articulo->categoria;?></div>
			  		<div class="panel-body">
			  			<?php if($articulo->archivo_url!=""){?>
			    			<img class="img-circle img-banner" src="<?php echo base_url().'assets/uploads/articulos/'.$articulo->archivo_url?>">
			    		<?php } ?>
			    		<div class="text-banner">
			    			<?php echo $articulo->articulo; ?>
			    		</div>
			    		<a class="btn btn-default btn-xs" href="<?php echo base_url().'index.php/categoria/articulos/'.$articulo->id_categoria.'/'.$id_hotel; ?>"><?php echo $texto['leer_mas'];?></a>
					</div>
				</div>
			</div>			
		<?php
		if((6*0.5)-1==$i || 6-1==$i){
			echo "</div>";
		} 
		$i=$i+1;
		}
	}//cierra el foreach 
	
	
	 
 /*
	$i=0;
	foreach ($articulos as $articulo) { 
		if($i==0 || $i==3){
			echo "<div class='row'>";
		} 
		if($i<6){ ?>		
			<div class="col-md-<?php echo 4?>">
				<div class="panel panel-hotel">
			  		<div class="panel-heading"><?php echo $articulo->categoria;?></div>
			  		<div class="panel-body">
			  			<?php if($articulo->archivo_url!=""){?>
			    			<img class="img-circle img-banner" src="<?php echo base_url().'assets/uploads/articulos/'.$articulo->archivo_url?>">
			    		<?php } ?>
			    		<div class="text-banner">
			    			<?php echo $articulo->articulo; ?>
			    		</div>
					</div>
				</div>
			</div>			
	<?php
			if($i==2 || $i==5){
				echo "</div>";
			}
		}else if($i==6)	{ ?>
			<div class='row'>
			<div class="col-md-12">
				<div class="panel panel-hotel">
			  		<div class="panel-heading">Otros</div>
			  		<div class="panel-body">
			    		<div class="text-banner">
			    <?php	} ?>
							<?php if($i>5){echo $articulo->categoria;?> - <?php echo $articulo->titulo; }?>
<?php	if($i==$cantidad_categorias-1){ ?>
			    		</div>
					</div>
				</div>
			</div>	
			</div>	
			
			
<?php}
		
	$i=$i+1;
	}//cierra el foreach */
 }
 } ?>


	