<div class="container">
<div class="row">
	<div class="col-md-2">
		<div class="panel panel-warning">
  			<div class="panel-heading">
  				<span class="icon-emailalt"></span> Mensajes
  			</div>
  			<div class="panel-body">
    			<ul class="nav nav-pills nav-stacked">
            		<li><a  href='<?php echo site_url('admin/mensaje/mensajes_abm')?>'>Mensajes</a></li>
					<!--<li><a  href='<?php echo site_url('admin/mensaje/tipos_mensaje')?>'>Tipos de mensaje</a></li>-->
					<li><a  href='<?php echo site_url('admin/mensaje/estados_mensaje')?>'>Estados mensaje</a></li>
          		</ul>
  			</div>
		</div>
	</div>

	<div class="col-md-10">
		<div class="panel panel-warning">
  			<div class="panel-heading">
  				<span class="icon-emailalt"></span> Mensajes
  			</div>
  			<div class="panel-body">
    			<?php echo $output; ?>
  			</div>
		</div>
    </div>
</div>    


