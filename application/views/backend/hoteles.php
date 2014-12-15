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
    			<?php echo $output; ?>
  			</div>
		</div>
    </div>
</div>    


