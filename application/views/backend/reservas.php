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
    			<?php echo $output; ?>
  			</div>
		</div>
    </div>
</div>    


