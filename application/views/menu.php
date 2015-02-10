<div class="navbar navbar-default" role="navigation">
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="#">Casta</a>
		</div>
		<div class="collapse navbar-collapse">
			<ul class="nav navbar-nav navbar-right">
				<li><a href="https://github.com/Diegoni/casta" target="_blank">Project</a></li>
			</ul>
			<ul class="nav navbar-nav">
				<li>
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">Clientes <b class="caret"></b></a>
					<ul class="dropdown-menu multi-level">
						<?php echo item_menu('clientes/cliente/abm_clientes', 'Gestion de clientes.');?>
                    </ul>
				</li>
				<li>
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">Proveedores <b class="caret"></b></a>
					<ul class="dropdown-menu multi-level">
						<?php echo item_menu('proveedores/proveedor/abm_proveedores', 'Gestion de proveedor.');?>
                    </ul>
				</li>
			</ul>
        </div><!--/.nav-collapse -->
    </div>
</div>