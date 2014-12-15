<?php $id_hotel=$_COOKIE['id_hotel']?>
<div class="container">	
	<div class="row menu">
	<div class="col-md-4">
		<?php  
			foreach ($hoteles as $hotel) {
				$id_hotel=$hotel->id_hotel;
				$logo_url=array();
				if (!(in_array($hotel->logo_url, $logo_url))) {
					$logo_url[]=$hotel->logo_url;	
				} 
  		}?>
		<center>
		<a href="<?php echo base_url().'index.php/'.$this->uri->segment(1).'/inicio/hotel/'.$id_hotel; ?>" class="logo">
			<img src="<?php echo base_url().'assets/uploads/logos/'.$logo_url[0];?>" class="logo_img">
		</a>
		</center>
	</div>	
	<div class="col-md-2">
		<ul class="list-unstyled pull-right">
   			
		</ul>
	</div>                
    <div class="col-md-6">
    	<div class="row" id="row-banderas">
    		<?php foreach ($hoteles_menu as $hotel) { ?>
    			<?php if($id_hotel!=$hotel->id_hotel){ ?>
    			<div class="col-md-3  col-sm-3 col-xs-3">
    				<a href="<?php echo base_url().'index.php/'.$this->uri->segment(1).'/inicio/hotel/'.$hotel->id_hotel ?>">
    				<img src="<?php echo base_url().'assets/uploads/logos/'.$hotel->logo_url;?>" class="logo_img_menu img-responsive" alt="Responsive image">
    				</a>
    			</div>
    			<?php } ?>
			<?php } ?>
		</div>
	</div>
	<div class="col-md-1">
		<ul class="list-unstyled pull-right">
   			
		</ul>
	</div>
	</div>
	


<!---------------------------------------------------------------------------------
-----------------------------------------------------------------------------------
					
							Sub-menú

-----------------------------------------------------------------------------------
---------------------------------------------------------------------------------->	


<nav class="navbar navbar-default" role="navigation">
	<div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
		<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
			<span class="sr-only">Toggle navigation</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>
	</div>
	
	<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
		<ul class="nav navbar-nav">
			<li>
				<a class="panel-menu" href="<?php echo base_url().'index.php/'.$this->uri->segment(1).'/hoteles/habitaciones/'.$id_hotel; ?>">
					<?php echo $texto['habitaciones'] ?>
				</a>
			</li>
			<li>
				<a class="panel-menu" href="<?php echo base_url().'index.php/'.$this->uri->segment(1).'/categoria/articulos/6/'.$id_hotel; ?>">
					<?php echo $texto['servicios'] ?>
				</a>
			</li>
			<li>
				<a class="panel-menu" href="<?php echo base_url().'index.php/'.$this->uri->segment(1).'/hoteles/galeria/'.$id_hotel; ?>">
					<?php echo $texto['galeria'] ?>
				</a>
			</li>
			<li>
				<a class="panel-menu" href="<?php echo base_url().'index.php/'.$this->uri->segment(1).'/categoria/articulos/3/'.$id_hotel; ?>">
					<?php echo $texto['promociones'] ?>
				</a>
			</li>
			<li>
				<a class="panel-menu" href="<?php echo base_url().'index.php/'.$this->uri->segment(1).'/hoteles/como_llegar/'.$id_hotel; ?>">
					<?php echo $texto['como_llegar'] ?>
				</a>
			</li>
		</ul>
		
		
		<div class="row" id="row-banderas-submenu">
    		<?php 
    		foreach ($hoteles_menu as $hotel) { 
    			if($id_hotel!=$hotel->id_hotel){
    				echo "<div class='col-md-3  col-sm-3 col-xs-3'>";
    					echo "<a href='".base_url().'index.php/inicio/hotel/'.$hotel->id_hotel."'>";
    						echo "<img src='".base_url().'assets/uploads/logos/'.$hotel->logo_url."' class='logo_img_menu_submenu img-responsive' alt='Responsive image'>";
    					echo "</a>";
    				echo "</div>";
				}
			} ?>
		</div>
		
		<ul class="nav navbar-nav navbar-right">
		<?php 
		$form_url = str_replace($this->uri->segment(1).'/', '', uri_string());
		
		if($form_url=='reserva/habitacion'){
			foreach ($idiomas as $idioma) { 
				echo "<li class='li-moneda'>";
					echo form_open($idioma->url.'/reserva/habitacion');
						echo "<input type='hidden' name='hotel'		value=".$this->input->post('hotel').">";
						echo "<input type='hidden' name='entrada'	value=".$this->input->post('entrada').">";
						echo "<input type='hidden' name='salida'	value=".$this->input->post('salida').">";
						echo "<input type='hidden' name='adultos'	value=".$this->input->post('adultos').">";
						echo "<input type='hidden' name='menores'	value=".$this->input->post('menores').">";
						echo "<input class='moneda-menu' type='image' title=".$idioma->idioma." rel='tooltip' src=".base_url().'assets/uploads/idiomas/'.$idioma->imagen.">";
			 		echo form_close(); 
				echo "</li>";
			}
		}else if($form_url=='reserva/datos'){
			foreach ($idiomas as $idioma) { 
				echo "<li class='li-moneda'>";
					echo form_open($idioma->url.'/reserva/datos');
						echo "<input type='hidden' name='hotel'		value=".$this->input->post('hotel').">";
						echo "<input type='hidden' name='entrada'	value=".$this->input->post('entrada').">";
						echo "<input type='hidden' name='salida'	value=".$this->input->post('salida').">";
						echo "<input type='hidden' name='adultos'	value=".$this->input->post('adultos').">";
						echo "<input type='hidden' name='menores'	value=".$this->input->post('menores').">";
						foreach ($habitaciones as $clave => $valor) {
	    					echo "<input type='hidden' name='habitacion".$clave."' value='".$valor."'>";
	    					echo "<input type='hidden' name='precio".$clave."' value='".$this->input->post('precio'.$clave)."'>";
						}
						echo "<input class='moneda-menu' type='image' title=".$idioma->idioma." rel='tooltip' src=".base_url().'assets/uploads/idiomas/'.$idioma->imagen.">";
			 		echo form_close(); 
				echo "</li>";
			}
		}else{
			foreach ($idiomas as $idioma) { ?>
				<li class="li-moneda">
					<input class="moneda-menu" 
						name="boton1" type="image" 
						title="<?php echo $texto['cambiar_idioma'];?>" 
						rel="tooltip" 
						src="<?php echo base_url().'assets/uploads/idiomas/'.$idioma->imagen;?>"
						value="<?php echo str_replace(base_url().'index.php/'.$this->uri->segment(1).'/',
													  base_url().'index.php/'.$idioma->url."/", 
													  current_url());?>" 
						onclick="
						var url = $(this).val();
			    		window.location = url;">
				</li>
			<?php }?>
		<?php }?>
		</ul>
		
		
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>


<!---------------------------------------------------------------------------------
-----------------------------------------------------------------------------------
					
							Modal consulta

-----------------------------------------------------------------------------------
---------------------------------------------------------------------------------->	


<div class="modal fade" id="email" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  	<div class="modal-dialog">
    	<div class="modal-content">
      		<div class="modal-header">
        		<h4 class="modal-title" id="myModalLabel"><?php echo $texto['consulta']?></h4>
      		</div>
      		<form method="post" class="form-horizontal" role="form" accept-charset="utf-8" action="<?php echo base_url().'index.php/'.$this->uri->segment(1).'/consulta/envio'?>" />
      		<div class="modal-body">
      			<div class="form-group">
    				<label for="nombre" class="col-sm-2 control-label"><?php echo $texto['mensaje']?></label>
    				<div class="col-sm-10">
      				<textarea class="form-control" name="consulta" rows="3" placeholder="<?php echo $texto['ingrese']." ".$texto['mensaje']?>" required></textarea>
    				</div>
  				</div>
      			<div class="form-group">
    				<label for="nombre" class="col-sm-2 control-label"><?php echo $texto['email']?></label>
    				<div class="col-sm-10">
      				<input class="form-control" name="email" type="email" placeholder="<?php echo $texto['ingrese']." ".$texto['su']." ".$texto['email']?>" required>
    				</div>
  				</div>
  				
  				<div class="form-group">
    				<label for="nombre" class="col-sm-2 control-label"><?php echo $texto['nombre']?></label>
    				<div class="col-sm-10">
    				<input type="text" class="form-control" name="nombre" placeholder="<?php echo $texto['ingrese']." ".$texto['su']." ".$texto['nombre']?>" required>
    				</div>
  				</div>
  				<div class="form-group">
    				<label for="apellido" class="col-sm-2 control-label"><?php echo $texto['apellido']?></label>
    				<div class="col-sm-10">
    				<input type="text" class="form-control" name="apellido" placeholder="<?php echo $texto['ingrese']." ".$texto['su']." ".$texto['apellido']?>" required>
    				</div>
  				</div>
  				<div class="form-group">
    				<label for="telefono"class="col-sm-2 control-label"><?php echo $texto['telefono']?></label>
    				<div class="col-sm-10">
    				<input type="text" class="form-control" name="telefono" placeholder="<?php echo $texto['ingrese']." ".$texto['su']." ".$texto['telefono']?>"> 
    				</div>
  				</div>
      		</div>
      		<div class="modal-footer">
      			<input type="hidden" name="id_hotel" value="<?php echo $id_hotel?>" >
        		<button type="button" class="btn btn-hotel boton-redondo-medium" data-dismiss="modal" title="<?php echo $texto['cerrar']?>"><span class="icon-remove"></span></button>
        		<button type="submit" class="btn btn-hotel boton-redondo-medium" title="<?php echo $texto['enviar_consulta']?>"><span class="icon-ok"></span></button>
      		</div>
      		</form>
    	</div>
  	</div>
</div>





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




<!---------------------------------------------------------------------------------
-----------------------------------------------------------------------------------
					
						Modal telefono 

-----------------------------------------------------------------------------------
---------------------------------------------------------------------------------->	

<div class="modal fade" id="telefono" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  	<div class="modal-dialog modal-sm">
    	<div class="modal-content">
      		<div class="modal-header">
        		<h4 class="modal-title" id="myModalLabel"><?php echo $texto['telefono']?></h4>
      		</div>
      		<div class="modal-body">
      			<div class="form-group">
    				<label for="nombre" class="col-sm-3 control-label"><?php echo $texto['telefono']?></label>
    				<label for="nombre" class="col-sm-9 control-label">
    					<?php 
						foreach ($telefono as $key => $value) {
							echo "<p class='telefonos'>".$value."</p><br>";
						}
						?>
    				</label>
  				</div>
      			<!--
      			<div class="form-group">
    				<label for="nombre" class="col-sm-3 control-label">Skype</label>
    				<div class="col-sm-9">
      				<a href="skype:contact-mt?call" class="btn btn-default"><span>carollo_hotel</span></a>
    				</div>
  				</div>
  				-->
      		</div>
      		<div class="modal-footer">
        		<button type="button" class="btn btn-hotel boton-redondo-medium" data-dismiss="modal" title="<?php echo $texto['cerrar']?>"><span class="icon-remove"></span></button>
      		</div>
    	</div>
  	</div>
</div>


<!---------------------------------------------------------------------------------
-----------------------------------------------------------------------------------
					
						Modal dirección 

-----------------------------------------------------------------------------------
---------------------------------------------------------------------------------->	
<?php
foreach ($hoteles as $hotel) {
	$fondo_url=$hotel->fondo_intro;

} 
?>

<div class="modal fade" id="direccion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  	<div class="modal-dialog modal-lg">
    	<div class="modal-content">
      		<div class="modal-header">
        		<h4 class="modal-title" id="myModalLabel"><?php echo $texto['direccion']?></h4>
      		</div>
      		<div class="modal-body">
      			<center>
      				<img src="<?php echo base_url().'assets/uploads/logos/'.$fondo_url?>" class="mapa-direccion img-responsive" alt="Responsive image">	
      			</center>
      		</div>
      		<div class="modal-footer">
        		<button type="button" class="btn btn-hotel boton-redondo-medium" data-dismiss="modal" title="<?php echo $texto['cerrar']?>"><span class="icon-remove"></span></button>
      		</div>
      		</form>
    	</div>
  	</div>
</div>