<div class="container">
<div class="row">
	<div class="col-md-2">
		<div class="panel panel-default">
  			<div class="panel-heading">
  				<i class="icon-taskmanager-logprograms"></i> Logs
  			</div>
  			<div class="panel-body">
  				<ul class="nav nav-pills nav-stacked">
	            	<li><a  href='<?php echo site_url('admin/log/logs_articulos_abm')?>'>Artículos</a></li>
            		<li><a  href='<?php echo site_url('admin/log/logs_habitaciones_abm')?>'>Habitaciones</a></li>	            			
            		<li><a  href='<?php echo site_url('admin/log/logs_hoteles_abm')?>'>Hoteles</a></li>
            		<li><a  href='<?php echo site_url('admin/log/logs_huespedes_abm')?>'>Huespedes</a></li>
            		<li><a  href='<?php echo site_url('admin/log/logs_mensajes_abm')?>'>Mensajes</a></li>
            		<li><a  href='<?php echo site_url('admin/log/logs_otros_abm')?>'>Otros</a></li>
            		<li><a  href='<?php echo site_url('admin/log/logs_reservas_abm')?>'>Reservas</a></li>
            		<li><a  href='<?php echo site_url('admin/log/logs_usuarios_abm')?>'>Usuarios</a></li>
          		</ul>
  			</div>
		</div>
	</div>

	<div class="col-md-10">
		<div class="panel panel-default">
  			<div class="panel-heading">
  				<i class="icon-taskmanager-logprograms"></i> Logs
  			</div>
  			<div class="panel-body">
    			<?php echo $output; ?>
  			</div>
		</div>
    </div>
</div>    


