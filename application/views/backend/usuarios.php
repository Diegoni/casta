<div class="container">
<div class="row">
	<div class="col-md-2">
		<div class="panel panel-danger">
  			<div class="panel-heading">
  				<span class="icon-workshirt"></span> Usuarios
  			</div>
  			<div class="panel-body">
    			<ul class="nav nav-pills nav-stacked">
            		<li><a href='<?php echo site_url('admin/usuario/usuarios_abm')?>'>Usuarios</a></li>
            		<li><a href='<?php echo site_url('admin/usuario/telefonos_usuario')?>'>Teléfonos</a></li>
            		<li><a href='<?php echo site_url('admin/usuario/emails_usuario')?>'>Emails</a></li>
            		<li><a href='<?php echo site_url('admin/usuario/direcciones_usuario')?>'>Direcciones</a></li>
            		<li><a href='<?php echo site_url('admin/usuario/estados_usuario')?>'>Estados usuario</a></li>
					<li><a href='<?php echo site_url('admin/usuario/accesos_abm')?>'>Accesos</a></li>
					<li><a href='<?php echo site_url('admin/usuario/detalle_accesos')?>'>Detalle accesos</a></li>
          		</ul>
  			</div>
		</div>	
	</div>

	<div class="col-md-10">
		<div class="panel panel-danger">
  			<div class="panel-heading">
  				<span class="icon-workshirt"></span> Usuarios
  			</div>
  			<div class="panel-body">
    			<?php echo $output; ?>
  			</div>
		</div>	
    </div>
	</div>    
</div>

