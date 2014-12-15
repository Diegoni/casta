<?php foreach ($configs as $config) {
	$max_adultos=$config->max_adultos;
	$max_menores=$config->max_menores;
	$id_hotel=$_COOKIE['id_hotel'];
}?>

<div class="row">
	<div class="col-md-4">
		<div class="panel panel-hotel">
	  		<div class="panel-heading"><?php echo $texto['consulta'];?></div>
	  		<div class="panel-body">
	  			<form method="post" role="form" accept-charset="utf-8" action="<?php echo base_url().'index.php/consulta/envio'?>" />
  					<div class="form-group">
    					<label for="mensaje"><i class="fa fa-envelope-o"></i> <?php echo $texto['mensaje'];?></label>
    					<textarea class="form-control" name="consulta" rows="3" required placeholder="<?php echo $texto['ingrese']." ".$texto['mensaje']?>"></textarea>
  					</div>
  					<div class="form-group">
    					<label for="email"><i class="fa fa-envelope-o"></i> <?php echo $texto['email'];?></label>
    					<input class="form-control" name="email" type="email" placeholder="<?php echo $texto['ingrese']." ".$texto['su']." ".$texto['email']?>" required>
  					</div>
  					<div class="form-group">
    					<label for="nombre"><i class="fa fa-user"></i> <?php echo $texto['nombre'];?></label>
    					<input type="text" class="form-control" name="nombre" placeholder="<?php echo $texto['ingrese']." ".$texto['su']." ".$texto['nombre']?>" required>
  					</div>
  					<div class="form-group">
    					<label for="apellido"><i class="fa fa-user"></i> <?php echo $texto['apellido'];?></label>
    					<input type="text" class="form-control" name="apellido" placeholder="<?php echo $texto['ingrese']." ".$texto['su']." ".$texto['apellido']?>" required>
  					</div>
  					<div class="form-group">
    					<label for="telefono"><i class="fa fa-phone"></i> <?php echo $texto['telefono'];?></label>
    					<input type="text" class="form-control" name="telefono" placeholder="<?php echo $texto['ingrese']." ".$texto['su']." ".$texto['telefono']?>">
  					</div>
  					<div class="form-group">
  						<center>
  							<input type="hidden" name="id_hotel" value="<?php echo $id_hotel?>" >
  							<button type="submit" class="btn btn-hotel boton-redondo">
								<span class="icon-ok"></span>
							</button>	
  						</center>
  					</div>
				</form>
			</div>
		</div>
		
		
	</div>