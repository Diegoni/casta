<?php $id_hotel=$_COOKIE['id_hotel']?>
<?php 
  					$telefono=array();
					$direccion=array();
					
					foreach ($hoteles as $hotel) {
						if (!(in_array($hotel->calle." - ".$hotel->provincia, $direccion))) {
							$direccion[]=$hotel->hotel.", ".$hotel->calle.", ".$hotel->provincia;
						}	
					} 
					
					foreach ($direccion as $key => $value) {
						$direccion_final=$value;
					}
				?>
<div class="row">
<div class="col-md-8 col-md-offset-2">
	<div class="panel panel-hotel">
		<div class="panel-heading"><?php echo $texto['como_llegar']?> </div>
		<div class="panel-body">
			<div class="panel panel-hotel">
				<script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=false&languaje=sp&libraries=places"></script>
				<script type="text/javascript" src="<?php echo base_url().'librerias/main/js/google_maps.js'?>"></script>

    			<div id="map_canvas" style="float:left;width:100%;height:60%; margin-bottom: 15px;"></div>
				<form class="form-horizontal" role="form">
					
					<div class="form-group">
    					<label for="inputEmail3" class="col-sm-2 control-label">
    						<?php echo $texto['destino'];?>:
    					</label>
    					<div class="col-sm-10">
							<input type="text" id="end" class="form-control" value="<?php echo $direccion_final ?>" readonly>
						</div>
  					</div>
			
					<div class="form-group">
    					<label for="inputEmail3" class="col-sm-2 control-label">
    						<?php echo $texto['desde'];?>:
    					</label>
    					<div class="col-sm-10">
							<input type="text" class="form-control" id="start" value="" onFocus="geolocate()">
						</div>
  					</div>
  					
  					<!--
  					<div class="form-group">
    					<label for="inputEmail3" class="col-sm-2 control-label">
    						<?php echo $texto['puntos_intermedios'];?>:
    					</label>
    					<div class="col-sm-10">
							<input type="text" id="waypoints1" class="form-control">
							<div class="separador"></div>
							<input type="text" id="waypoints2" class="form-control">
							<div class="separador"></div>
							<input type="text" id="waypoints3" class="form-control">
						</div>
  					</div>
  					
  					<div class="form-group">
    					<label for="inputEmail3" class="col-sm-2 control-label">
    						<?php echo $texto['vehiculo_consumo'];?>
    					</label>
    					<div class="col-sm-10">
    						<div class="input-group">
      							<input type="text" class="form-control" id="consumo" value="8">
      							<div class="input-group-addon"><?php echo $texto['litros_kilometros'];?></div>
							</div>
						</div>
  					</div>
  			
  					<div class="form-group">
    					<label for="inputEmail3" class="col-sm-2 control-label">
    						<?php echo $texto['precio_combustible'];?>
    					</label>
    					<div class="col-sm-10">
							<input type="text" class="form-control" id="combustible" value="9">
						</div>
  					</div>
  					  -->					
  					<div class="form-group">
    					<label for="inputEmail3" class="col-sm-2 control-label">
    					</label>
    					<div class="col-sm-10">
							<a onclick="calcRoute(); document.getElementById('como_llegar').disabled=false;" class="btn btn-default show_hide2"><?php echo $texto['ruta']?></a>
						</div>
  					</div>
  			
  				<div class="slidingDiv2">
	  				<div class="form-group">
		    			<label for="inputEmail3" class="col-sm-2 control-label">
		    			</label>
		    			<div class="col-sm-10">
							<div id="directions_panel" class="alert alert-success"></div>
						</div>
	  				</div>
  			
	    		
		    		<div class="form-group">
	    				<label for="inputEmail3" class="col-sm-2 control-label">
	    				</label>
	    				<div class="col-sm-10">
							<a id="a" class='show_hide btn btn-default' title='<?php echo $texto['leer_mas']?>'><?php echo $texto['como_llegar']?></a>
						</div>
	  				</div>
		    		<div class='slidingDiv'>
						<div id="directions-panel"></div>
					</div>
				</div>
				</form>
				</div>
					<div class="col-md-12">
					<center>
						<a href="javascript:window.history.back();" type="submit" class="btn btn-hotel boton-redondo" title="<?php echo $texto['volver']?>" rel="tooltip">
							<span class="icon-chevron-left"></span>
						</a>
					</center>
				</div>
			</div>
		</div>
	</div>
</div>