<?php foreach ($configs as $config) {
	$max_adultos=$config->max_adultos;
	$max_menores=$config->max_menores;
	$id_hotel=$_COOKIE['id_hotel'];
}?>

<div class="row">
	<div class="col-md-4">
		<div class="panel panel-hotel">
	  		<div class="panel-heading"><?php echo $texto['reserva_online']?></div>
	  		<div class="panel-body" id="panel-form-reserva">
	  			<?php echo form_open('reserva/habitacion');?> 
	    			<div class="form-group margin-bottom">
						<label for="exampleInputEmail1"><i class="icon-arrow-right icono-rojo"></i> <?php echo $texto['entrada']?></label>
						<div class="input-group">
							<input class="form-control" name="entrada" id="entrada" type="entrada" placeholder="Fecha entrada" value="<?php echo date("d/m/Y"  /*, strtotime("+1 day")*/); ?>" autocomplete="off">
							<div class="input-group-addon" onclick="document.getElementById('entrada').focus();">
								<span class="icon-calendarthree"></span>
							</div>
    					</div>
				  	</div>
				  	<div class="form-group margin-bottom">
					    <label for="exampleInputEmail1"><i class="icon-arrow-left icono-rojo"></i> <?php echo $texto['salida']?></label>
						<div class="input-group">
							<input class="form-control" name="salida" id="salida" type="salida" placeholder="Fecha salida" value="<?php echo date("d/m/Y",  strtotime("+1 day")); ?>" autocomplete="off">
							<div class="input-group-addon" onclick="document.getElementById('salida').focus();">
								<span class="icon-calendarthree"></span>
							</div>
    					</div>
					</div>
					
					<div class="form-group margin-bottom">
						<label for="exampleInputPassword1"><i class="fa fa-user icono-rojo"></i> <?php echo $texto['ocupacion']?></label>
						<select class="form-control" name="adultos" id="adultos">
							<?php $i=1;
							do{
								if($i==1){ ?>
									<option value="<?php echo $i;?>"><?php echo $i;?> <?php echo $texto['adulto']?></option>	
								<?php }else{?>
									<option value="<?php echo $i;?>" <?php if($i==2){echo "selected";};?>><?php echo $i;?> <?php echo $texto['adultos'] ?></option>
							<?php 
								}
							$i=$i+1;
							}while($i<=$max_adultos);?>
						</select>
						<div class="separador margin-bottom"></div>
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
			  		<input type="hidden" value="<?php echo $id_hotel;?>" name="hotel">
					
						<center>
							<button type="submit" class="btn btn-hotel boton-redondo">
								<span class="icon-ok"></span>
							</button>		
						</center>
				  	
				<?php echo form_close(); ?>	
			</div>
		</div>
		
		<?php 
		if(isset($banner) && $banner!=""){
			
		foreach ($banner as $articulo) {?>
		<div class="panel panel-hotel">
			<div class="panel-heading"><?php echo $articulo->categoria?></div>
	  		<div class="panel-body">
	  			<?php echo $articulo->articulo; ?>
	  		</div>
	  	</div>
	  	<?php
		}
		} ?>	
		
		
		<!--
		<?php if(isset($monedas)){ ?>
		<div class="panel panel-hotel">
			<div class="panel-heading"><?php echo $texto['monedas']?></div>
	  		<div class="panel-body">
	  			<form method="post" action="<?php echo base_url().'index.php/reserva/habitacion' ?>">
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
	  		</div>
		</div>
		<?php } ?>
		
		<?php if(isset($monedas)){ ?>
		<!--
		<div class="panel panel-hotel">
			<div class="panel-heading"><?php echo $texto['idiomas']?></div>
	  		<div class="panel-body">
	  			<form method="post" action="<?php echo base_url().'index.php/reserva/habitacion' ?>">
	  				<input type="hidden" name="entrada" value="<?php echo $this->input->post('entrada') ?>">
						<input type="hidden" name="salida" value="<?php echo $this->input->post('salida') ?>">
						<input type="hidden" name="adultos" value="<?php echo $this->input->post('adultos') ?>">
						<input type="hidden" name="menores" value="<?php echo $this->input->post('menores') ?>">
						<input type="hidden" name="hotel" value="<?php echo $this->input->post('hotel') ?>">
	  			<?php foreach ($idiomas as $idioma) { ?>
						<input class="moneda" 
						name="boton1" type="image" 
						title="<?php echo $idioma->idioma;?>" rel="tooltip" 
						src="<?php echo base_url().'assets/uploads/idiomas/'.$idioma->imagen;?>" 
						onclick="document.cookie = 'idioma=<?php echo $idioma->id_idioma ?>'">
				<?php } ?>
				</form>  
	  		</div>
		</div>
		
		<?php } ?>
		-->
	</div>
	
	
	
	
	
	<!--
		Para elegir por tipos
		
		<label for="exampleInputPassword1"><i class="fa fa-user"></i> Tipo</label>
						<select class="form-control" name="tipo">
							<?php 
							foreach ($tipos_habitacion as $tipo) { ?>
								<option value="<?php echo $tipo->id_tipo_habitacion;?>"><?php echo $tipo->tipo_habitacion;?></option>
							<?php } ?>
						</select>
	-->