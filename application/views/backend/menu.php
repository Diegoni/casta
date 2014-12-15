<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
<?php if(isset($css_files)){ ?>
<?php foreach($css_files as $file): ?>
	<link type="text/css" rel="stylesheet" href="<?php echo $file; ?>" />
<?php endforeach; ?>	
<?php } ?>

<?php if(isset($js_files)){ ?>
<?php foreach($js_files as $file): ?>
	<script src="<?php echo $file; ?>"></script>
<?php endforeach; ?>
<?php } ?>
</head>
<body>
<nav class="navbar navbar-inverse" role="navigation">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a title="Administración" class="navbar-brand"href='<?php echo site_url('admin/home')?>'>Admin.</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
	<ul class="nav navbar-nav">
		
		<li class="dropdown">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="icon-tagalt-pricealt"></span> 
				Reservas 
				<?php if($cant_reservas>0){
					echo "<span class='badge badge-success'>".$cant_reservas."</span>";	
				} ?>				
				<b class="caret"></b>
			</a>
			<ul class="dropdown-menu">
				<?php if($cant_reservas>0){ ?>
				<li><a  href='#' data-toggle="modal" data-target="#modal_reservas">Nuevas					
				<?php echo "<span class='badge'>".$cant_reservas."</span></a></li>"; } ?>	
				<li><a  href='<?php echo site_url('admin/reserva/reservas_abm')?>'>Reservas</a></li>
				<li><a  href='<?php echo site_url('admin/reserva/vuelos_abm')?>'>Vuelos</a></li>
				<li><a  href='<?php echo site_url('admin/reserva/disponibilidades_abm')?>'>Cierre de ventas</a></li>
				<li><a  href='<?php echo site_url('admin/reserva/estados_reserva')?>'>Estados reserva</a></li>
          	</ul>
        </li>
        
        	
        <li class="dropdown">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-user"></i>  Huéspedes<b class="caret"></b></a>
			<ul class="dropdown-menu">
            	<li><a  href='<?php echo site_url('admin/huesped/huespedes_abm')?>'>Huéspedes</a></li>
            	<li><a  href='<?php echo site_url('admin/huesped/telefonos_huesped')?>'>Teléfonos</a></li>
            	<li><a  href='<?php echo site_url('admin/huesped/emails_huesped')?>'>Emails</a></li>
            	<li><a  href='<?php echo site_url('admin/huesped/direcciones_huesped')?>'>Direcciones</a></li>
            	<li><a  href='<?php echo site_url('admin/huesped/tarjetas_huesped')?>'>Tarjetas</a></li>
            	<li><a  href='<?php echo site_url('admin/huesped/tipos_tarjeta')?>'>Tipos de tarjeta</a></li>
            	<li><a  href='<?php echo site_url('admin/huesped/tipos_huesped')?>'>Tipos huésped</a></li>
            	<li><a  href='<?php echo site_url('admin/huesped/estados_huesped')?>'>Estados huésped</a></li>
          	</ul>
        </li>
        
        
        <li class="dropdown">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="icon-bed"></span> Habitaciones<b class="caret"></b></a>
			<ul class="dropdown-menu">
            	<li><a  href='<?php echo site_url('admin/habitacion/habitaciones_abm')?>'>Habitaciones</a></li>
				<li><a  href='<?php echo site_url('admin/habitacion/tarifas_abm')?>'>Tarifas</a></li>
				<li><a  href='<?php echo site_url('admin/habitacion/tarifas_temporales_abm')?>'>Tarifas temporales</a></li>
				<li><a  href='<?php echo site_url('admin/habitacion/monedas_abm')?>'>Monedas</a></li>
				<li><a  href='<?php echo site_url('admin/habitacion/servicios_abm')?>'>Servicios</a></li>
				<li><a  href='<?php echo site_url('admin/habitacion/tipos_habitacion')?>'>Tipos de habitación</a></li>
				<li><a  href='<?php echo site_url('admin/habitacion/tipo_tarifa_abm')?>'>Tipos de tarifa</a></li>
				<li><a  href='<?php echo site_url('admin/habitacion/estados_habitacion')?>'>Estados habitación</a></li>
          	</ul>
        </li>
		
		
		
		<li class="dropdown">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-office-building"></i> Hoteles<b class="caret"></b></a>
			<ul class="dropdown-menu">
            	<li><a  href='<?php echo site_url('admin/hotel/hoteles_abm')?>'>Hoteles</a></li>
				<li><a  href='<?php echo site_url('admin/hotel/telefonos_hotel')?>'>Teléfonos</a></li>
            	<li><a  href='<?php echo site_url('admin/hotel/emails_hotel')?>'>Emails</a></li>
            	<li><a  href='<?php echo site_url('admin/hotel/direcciones_hotel')?>'>Direcciones</a></li>
				<!--
				<li class="divider"></li>
            	<li><a  href='<?php echo site_url('admin/hotel/config')?>'>Configuración</a></li>
            	<li><a  href='<?php echo site_url('admin/hotel/detalle_config')?>'>Configuración avanzada</a></li>
            	-->
            </ul>
        </li>
        
        
        <li class="dropdown">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="icon-emailalt"></span> 
				Mensajes
				<?php if($cant_mensajes>0){
					echo "<span class='badge badge-success'>".$cant_mensajes."</span>";	
				} ?>
				<b class="caret"></b></a>
			<ul class="dropdown-menu">
				<?php if($cant_mensajes>0){ ?>
				<li><a  href='#' data-toggle="modal" data-target="#modal_mensajes">Nuevos					
				<?php echo "<span class='badge'>".$cant_mensajes."</span></a></li>"; } ?>
            	<li><a  href='<?php echo site_url('admin/mensaje/mensajes_abm')?>'>Mensajes</a></li>
				<!--
				<li><a  href='<?php echo site_url('admin/mensaje/tipos_mensaje')?>'>Tipos de mensaje</a></li>
				-->
				<li><a  href='<?php echo site_url('admin/mensaje/estados_mensaje')?>'>Estados mensaje</a></li>
          	</ul>
        </li>
        
		
		<li class="dropdown">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-document"></i> Artículos<b class="caret"></b></a>
			<ul class="dropdown-menu">
            	<li><a  href='<?php echo site_url('admin/articulo/articulos_abm')?>'>Artículos</a></li>
            	<li><a  href='<?php echo site_url('galeria/imagenes_articulos')?>'>Imágenes</a></li>
				<li><a  href='<?php echo site_url('admin/articulo/estados_articulo')?>'>Estados artículo</a></li>
				<li class="divider"></li>
				<li><a  href='<?php echo site_url('admin/articulo/categorias_abm')?>'>Categorías</a></li>
				<li><a  href='<?php echo site_url('admin/articulo/config_articulos/edit/1')?>'>Config Artículos</a></li>
				
          	</ul>
        </li>
        
        
        
        <li class="dropdown">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="icon-workshirt"></span> Usuarios<b class="caret"></b></a>
			<ul class="dropdown-menu">
            	<li><a href='<?php echo site_url('admin/usuario/usuarios_abm')?>'>Usuarios</a></li>
            	<li><a href='<?php echo site_url('admin/usuario/telefonos_usuario')?>'>Teléfonos</a></li>
            	<li><a href='<?php echo site_url('admin/usuario/emails_usuario')?>'>Emails</a></li>
            	<li><a href='<?php echo site_url('admin/usuario/direcciones_usuario')?>'>Direcciones</a></li>
            	<li><a href='<?php echo site_url('admin/usuario/estados_usuario')?>'>Estados usuario</a></li>
            	<li class="divider"></li>
				<li><a href='<?php echo site_url('admin/usuario/accesos_abm')?>'>Accesos</a></li>
				<li><a href='<?php echo site_url('admin/usuario/detalle_accesos')?>'>Detalle accesos</a></li>
          	</ul>
        </li>
        
        
        
		<li class="dropdown">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="icon-clipboard-paste"></span> Otros<b class="caret"></b></a>
			<ul class="dropdown-menu">
            	<li><a  href='<?php echo site_url('admin/otro/departamentos_abm')?>'>Departamentos</a></li>
            	<li><a  href='<?php echo site_url('admin/otro/provincias_abm')?>'>Provincias</a></li>
            	<li><a  href='<?php echo site_url('admin/otro/paises_abm')?>'>Países</a></li>
            	<li class="divider"></li>
				<li><a  href='<?php echo site_url('admin/otro/tipos_abm')?>'>Tipos</a></li>
				<li><a  href='<?php echo site_url('admin/otro/aerolineas_abm')?>'>Aerolineas</a></li>
				<li><a  href='<?php echo site_url('admin/traduccion')?>'>Traducción</a></li>
				<li class="divider"></li>
				<li><a  href='<?php echo site_url('admin/otro/terminos_abm')?>'>Términos y condiciones</a></li>
				<li><a  href='<?php echo site_url('admin/otro/ayudas_abm')?>'>Ayudas de la página</a></li>
				<li><a  href='<?php echo site_url('admin/otro/idiomas_abm')?>'>Idiomas</a></li>
				<li><a  href='<?php echo site_url('admin/otro/config_correo/edit/1')?>'>Config correo</a></li>
          	</ul>
        </li>
        
        <li class="dropdown">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="icon-taskmanager-logprograms"></span> Logs<b class="caret"></b></a>
			<ul class="dropdown-menu">
            	<li><a  href='<?php echo site_url('admin/log/logs_articulos_abm')?>'>Artículos</a></li>
            	<li><a  href='<?php echo site_url('admin/log/logs_habitaciones_abm')?>'>Habitaciones</a></li>
            	<li><a  href='<?php echo site_url('admin/log/logs_hoteles_abm')?>'>Hoteles</a></li>
            	<li><a  href='<?php echo site_url('admin/log/logs_huespedes_abm')?>'>Huespedes</a></li>
            	<li><a  href='<?php echo site_url('admin/log/logs_mensajes_abm')?>'>Mensajes</a></li>
            	<li><a  href='<?php echo site_url('admin/log/logs_otros_abm')?>'>Otros</a></li>
            	<li><a  href='<?php echo site_url('admin/log/logs_reservas_abm')?>'>Reservas</a></li>
            	<li><a  href='<?php echo site_url('admin/log/logs_usuarios_abm')?>'>Usuarios</a></li>
          	</ul>
        </li>
        <li>
        	<a href="<?php echo site_url('admin/home/logout')?>"><span class="icon-off"></span> Salir</a>
        </li>
        
       
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>


<span id="forkongithub">
    <a href='<?php echo site_url()?>' target="_blank">Sitio</a> 
  </span>
