<!--
<a href="<?php echo base_url();?>" target="_blank">
	<img style="position: absolute; top: 0; right: 0; border: 0;" src="http://s3.amazonaws.com/github/ribbons/forkme_right_white_ffffff.png" alt="Fork me on GitHub" />
</a>
-->
<div class="container">
<div class="row">
	<div class="col-md-2">
		<div class="panel panel-primary">
  			<div class="panel-heading">
  				<i class="icon-tagalt-pricealt"></i> Reservas
  			</div>
  			<div class="panel-body">
    			<ul class="nav nav-pills nav-stacked">
	            	<li><a  href='<?php echo site_url('admin/reserva/reservas_abm')?>'>Reservas</a></li>
	            	<li><a  href='<?php echo site_url('admin/reserva/vuelos_abm')?>'>Vuelos</a></li>
	            	<li><a  href='<?php echo site_url('admin/reserva/disponibilidades_abm')?>'>Cierre de ventas</a></li>
					<li><a  href='<?php echo site_url('admin/reserva/estados_reserva')?>'>Estados reserva</a></li>
            	</ul>
  			</div>
		</div>
	</div>

	<div class="col-md-10">
		<div class="panel panel-primary">
  			<div class="panel-heading">
  				<i class="icon-tagalt-pricealt"></i> Reservas
  			</div>
  			<div class="panel-body">
  				<?php 
  					if(!empty($registro)){
  						$descripcion=$registro['disponibilidad'];
  					}else{
  						$descripcion="";	
  					}  				
  					
					if(!empty($mensaje)){ ?>
				<div class="alert alert-success alert-dismissible" role="alert">
				 	<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				  	<?php echo $mensaje ?>
				</div>	
				<?php }	
  				?>
  				<div class="row well">
  					
  				<form action="" method="post" class="form-horizontal">
  					<link href="<?php echo base_url().'librerias/ui/jquery-ui.css'?>" rel="stylesheet" media="screen">
    				<div class="col-sm-6">
    					<div class="form-group">
    						<label>Comienzo <span class="required">*</span> :</label>
    						<input id="comienzo" name="comienzo" type="text" value="" maxlength="10" class="form-control" autocomplete="off" required>
    					</div>
    					<div class="form-group">
    						<label>Final <span class="required">*</span> :</label>
    						<input id="final" name="final" type="text" value="" maxlength="10" class="form-control" autocomplete="off" required>
    					</div>
    					<a href="http://localhost/Hotel_web/index.php/es/admin/reserva/disponibilidades_abm" class="btn btn-default">Cierre de ventas</a>
    					<button type="submit" name="aceptar" value="1" class="btn btn-default">Aceptar</button>
					</div>
					<div class="col-sm-6">
    					<div class="form-group">
    						<label>Descripción <span class="required">*</span> :</label>
    						<input id="descripcion" name="descripcion" type="text" value="<?php echo $descripcion?>" class="numeric form-control" maxlength="11" required style="width: 100%">
    					</div>
    					<div class="form-group">
    						<label>Habitaciones <span class="required">*</span> :</label>
    						<select id="id_habitaciones" name="id_habitaciones[]" class="chosen-select chzn-done form-control" data-placeholder="Seleccionar habitaciones"  multiple="" style="width: 100%">
								<?php 
								if(!empty($disponibilidad_habitacion)){
									foreach ($habitaciones as $habitacion) {
										if(in_array($habitacion->id_habitacion, $disponibilidad_habitacion)){ ?>
											<option value="<?php echo $habitacion->id_habitacion ?>" selected><?php echo $habitacion->habitacion." - ".$habitacion->hotel ?></option>
									<?php }else{?>
											<option value="<?php echo $habitacion->id_habitacion ?>"><?php echo $habitacion->habitacion." - ".$habitacion->hotel ?></option>
									<?php }
										
									} 
									
								}else{
									foreach ($habitaciones as $habitacion) { ?>
										<option value="<?php echo $habitacion->id_habitacion ?>"><?php echo $habitacion->habitacion." - ".$habitacion->hotel ?></option>
								<?php } ?>
										
								<?php } ?>
							</select>
						</div>
					</div>

    			</form>
    			</div>
    			<?php if(!empty($disponibilidad_habitacion)){ ?>
    				<div class="form-group even" id="final_field_box">
						<div class="col-sm-4">
							<b>Disponibilidad</b>
						</div>
						<div class="col-sm-2">
							<b>Comienzo</b>
						</div>
						<div class="col-sm-2">
							<b>Final</b>		
						</div>
						<div class="col-sm-4">
							<b>Habitación</b>		
						</div>
					</div>	
    			<?php foreach ($cargas as $row) { ?> 
					<div class="form-group even" id="final_field_box">
						<div class="col-sm-4">
							<?php echo $row->disponibilidad ?>
						</div>
						<div class="col-sm-2">
							<?php echo date('d/m/Y', strtotime($row->entrada)); ?>
						</div>
						<div class="col-sm-2">
							<?php echo date('d/m/Y', strtotime($row->salida)); ?>		
						</div>
						<div class="col-sm-4">
							<?php echo $row->habitacion ?>		
						</div>
					</div>	
						
				<?php }
    				
    			}?>
  			</div>
		</div>
    </div>
</div> 

 <script src="<?php echo base_url().'librerias/chosen/chosen.jquery.js'?>" type="text/javascript"></script>
 <script src="<?php echo base_url().'librerias/chosen/prism.js'?>" type="text/javascript" charset="utf-8"></script>
  <script type="text/javascript">
    var config = {
      '.chosen-select'           : {},
      '.chosen-select-deselect'  : {allow_single_deselect:true},
      '.chosen-select-no-single' : {disable_search_threshold:10},
      '.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
      '.chosen-select-width'     : {width:"95%"}
    }
    for (var selector in config) {
      $(selector).chosen(config[selector]);
    }
  </script>   







