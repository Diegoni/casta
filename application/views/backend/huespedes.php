<div class="container">
<div class="row">
	<div class="col-md-2">
		<div class="panel panel-info">
  			<div class="panel-heading">
  				<i class="icon-user"></i> Huéspedes
  			</div>
  			<div class="panel-body">
    			<ul class="nav nav-pills nav-stacked">
	            	<li><a  href='<?php echo site_url('admin/huesped/huespedes_abm')?>'>Huéspedes</a></li>
	            	<li><a  href='<?php echo site_url('admin/huesped/telefonos_huesped')?>'>Teléfonos</a></li>
	            	<li><a  href='<?php echo site_url('admin/huesped/emails_huesped')?>'>Emails</a></li>
	            	<li><a  href='<?php echo site_url('admin/huesped/direcciones_huesped')?>'>Direcciones</a></li>
	            	<li><a  href='<?php echo site_url('admin/huesped/tarjetas_huesped')?>'>Tarjetas</a></li>
	            	<li><a  href='<?php echo site_url('admin/huesped/tipos_tarjeta')?>'>Tipos de tarjeta</a></li>
	            	<li><a  href='<?php echo site_url('admin/huesped/tipos_huesped')?>'>Tipos huésped</a></li>
	            	<li><a  href='<?php echo site_url('admin/huesped/estados_huesped')?>'>Estados huésped</a></li>
          		</ul>
  			</div>
		</div>
	</div>

	<div class="col-md-10">
		<div class="panel panel-info">
  			<div class="panel-heading">
  				<i class="icon-user"></i> Huéspedes
  			</div>
  			<div class="panel-body">
    			<?php echo $output; ?>
  			</div>
		</div>
    </div>
</div>    


