<div class="container">
<div class="row">
	<div class="col-md-2">
		<div class="panel panel-default">
  			<div class="panel-heading">
  				<i class="icon-document"></i> Artículos
  			</div>
  			<div class="panel-body">
  				<ul class="nav nav-pills nav-stacked">
	            	<li><a  href='<?php echo site_url('admin/articulo/articulos_abm')?>'>Artículos</a></li>
	            	<li><a  href='<?php echo site_url('galeria/imagenes_articulos')?>'>Imágenes</a></li>
	            	<li><a  href='<?php echo site_url('admin/articulo/categorias_abm')?>'>Categorías</a></li>
					<li><a  href='<?php echo site_url('admin/articulo/estados_articulo')?>'>Estados artículo</a></li>
					<li><a  href='<?php echo site_url('admin/articulo/config_articulos/edit/1')?>'>Config Artículos</a></li>		
          		</ul>
  			</div>
		</div>
	</div>

	<div class="col-md-10">
		<div class="panel panel-default">
  			<div class="panel-heading">
  				<i class="icon-document"></i> Artículos
  			</div>
  			<div class="panel-body">
    			<?php echo $output; ?>
  			</div>
		</div>
    </div>
</div>    


