<?php
foreach ($config_email as $config) {
	$correo		= $config->correo;
	$hotel		= $config->hotel;
	$tipo_correo= $config->tipo_correo;
}
?>
<div class="container">
<div class="row">
	<div class="col-md-2">
		<div class="panel panel-success">
  			<div class="panel-heading">
  				<i class="icon-office-building"></i> Hoteles
  			</div>
  			<div class="panel-body">
    			<ul class="nav nav-pills nav-stacked">
	            	<li><a  href='<?php echo site_url('admin/hotel/hoteles_abm')?>'>Hoteles</a></li>
					<li><a  href='<?php echo site_url('admin/hotel/telefonos_hotel')?>'>Teléfonos</a></li>
	            	<li><a  href='<?php echo site_url('admin/hotel/emails_hotel')?>'>Emails</a></li>
	            	<li><a  href='<?php echo site_url('admin/hotel/direcciones_hotel')?>'>Direcciones</a></li>
	            	<li>
	            		<a class="dropdown-toggle" data-toggle="dropdown">
							Email reserva <span class="caret"></span>
						</a>
						<ul class="dropdown-menu" role="menu">
							<li><a  href='<?php echo site_url('admin/hotel/config_email_reserva/1')?>'>Administración</a></li>			
							<li><a  href='<?php echo site_url('admin/hotel/config_email_reserva/2')?>'>Huesped</a></li>
						</ul>
					</li>
	            	<li>
	            		<a class="dropdown-toggle" data-toggle="dropdown">
							Email mensaje <span class="caret"></span>
						</a>
						<ul class="dropdown-menu" role="menu">
							<li><a  href='<?php echo site_url('admin/hotel/config_email_mensaje/1')?>'>Administración</a></li>			
							<li><a  href='<?php echo site_url('admin/hotel/config_email_mensaje/2')?>'>Huesped</a></li>
						</ul>
					</li>
	            	<li>
	            		<a class="dropdown-toggle" data-toggle="dropdown">
							Email habitación <span class="caret"></span>
						</a>
						<ul class="dropdown-menu" role="menu">
							<li><a  href='<?php echo site_url('admin/hotel/config_email_habitacion/1')?>'>Administración</a></li>			
							<li><a  href='<?php echo site_url('admin/hotel/config_email_habitacion/2')?>'>Huesped</a></li>
						</ul>
					</li>
	            	
	            	<!--
	            	<li><a  href='<?php echo site_url('admin/hotel/config')?>'>Configuración</a></li>
	            	<li><a  href='<?php echo site_url('admin/hotel/detalle_config')?>'>Configuración avanzada</a></li>
	            	-->
            	</ul>
  			</div>
		</div>
	</div>

	<div class="col-md-10">
		<div class="panel panel-success">
  			<div class="panel-heading">
  				<i class="icon-office-building"></i> Hoteles
  			</div>
  			<div class="panel-body">
  				<script src="<?php echo base_url().'/assets/grocery_crud/js/jquery-1.10.2.min.js'?>"></script>
				<script src="<?php echo base_url().'/assets/grocery_crud/texteditor/ckeditor/ckeditor.js'?>"></script>
				<script src="<?php echo base_url().'/assets/grocery_crud/texteditor/ckeditor/adapters/jquery.js'?>"></script>
				<script src="<?php echo base_url().'/assets/grocery_crud/js/jquery_plugins/config/jquery.ckeditor.config.js'?>"></script>
				<div class="row">
					<div class="col-md-3">
						Hotel
					</div>
					<div class="col-md-9">
						<b><?php echo $hotel ?></b>		
					</div>
					<div class="col-md-3">
						Tipo de correo
					</div>
					<div class="col-md-9">
						<b><?php echo $tipo_correo?></b>		
					</div>
				</div>
				<form action="" method="post" class="form-horizontal">
					<textarea id="txt" class="texteditor" name="correo" >
						<?php echo $correo?>
					</textarea>
					<select id='valor'  class="chosen-select chzn-done form-control">
						<?php foreach ($option as $key => $value) {
							echo "<option value=".$key.">".$value."</option>";
						}?>
					</select>
					<input type="button" id="btn" value="" class="btn btn-default"/><br>
					<button type="submit" name="aceptar" value="1" class="btn btn-default">Aceptar</button>
				</form>
				<script>
				$("#btn").on('click', function() {
    				var caretPos = document.getElementById("txt").selectionStart;
    				var textAreaTxt = jQuery("#txt").val();
    				var txtToAdd =  $( "#valor" ).val();
    				//$("#txt").val(textAreaTxt.substring(0, caretPos) + txtToAdd + textAreaTxt.substring(caretPos) );
    				$("#txt").val(textAreaTxt.substring(caretPos) + txtToAdd);
				});
				
				$('select').on('change', function() {
  					$( "#btn" ).val(this.value);
				})
				</script>
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



