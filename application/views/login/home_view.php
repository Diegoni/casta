<div class="container">
<div class="row">
	<div class="col-md-12">
		<div class="">
   			<h2 class="bs-docs-featurette-title">Bienvenido  <?php echo $usuario; ?>!</h2>
   			<p class="lead">Sistema de gestión de hoteles</p>
   		</div>
   	</div>
</div>
<div class="row">
        <div class="col-md-6">
            <div class="blockquote-box blockquote-primary clearfix">
            	<a  href='<?php echo site_url('admin/reserva/reservas_abm')?>'>
	                <div class="square pull-left">
	                    <span class="icon-tagalt-pricealt icon-lg"></span>
	                </div>
                </a>
                <h4>
                    Reservas</h4>
                <p>
                    Descripción de reservas
                </p>
            </div>
           	<div class="blockquote-box blockquote-info clearfix">
                <a  href='<?php echo site_url('admin/huesped/huespedes_abm')?>'>
	                <div class="square pull-left">
	                    <i class="icon-user icon-lg"></i>
	                </div>
                </a>
                <h4>
                    Huéspedes</h4>
                <p>
                    Descripción de huéspedes
                </p>
            </div>
            <div class="blockquote-box blockquote-success clearfix">
                <a  href='<?php echo site_url('admin/habitacion/habitaciones_abm')?>'>
	                <div class="square pull-left">
	                    <span class="icon-bed icon-lg"></span>
	                </div>
                </a>
                <h4>
                    Habitaciones</h4>
                <p>
                    Descripción de habitaciones
                </p>
            </div>
        </div>
        <div class="col-md-6">
           <div class="blockquote-box blockquote-success clearfix">
                <a  href='<?php echo site_url('admin/hotel/hoteles_abm')?>'>
                	<div class="square pull-left">
                    	<i class="icon-office-building icon-lg"></i>
                	</div>
                </a>
                <h4>
                    Hoteles</h4>
                <p>
                    Descripción de hoteles
                </p>
            </div>
            <div class="blockquote-box blockquote-warning clearfix">
                <a  href='<?php echo site_url('admin/mensaje/mensajes_abm')?>'>
                	<div class="square pull-left">
                    	<span class="icon-emailalt icon-lg"></span>
                	</div>
                </a>
                <h4>
                    Mensajes</h4>
                <p>
                    Descripción de mensajes
                </p>
            </div>
            <div class="blockquote-box blockquote-default clearfix">
                <a  href='<?php echo site_url('admin/articulo/articulos_abm')?>'>
                	<div class="square pull-left">
                    	<i class="icon-document icon-lg"></i>
                	</div>
                </a>
                <h4>
                    Artículos</h4>
                <p>
                    Descripción de artículos
                </p>
            </div>
        </div>
    </div>
</div>
   
</body>
</html>