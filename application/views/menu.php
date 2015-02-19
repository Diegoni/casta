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
					<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $texto['catalogo'] ?><b class="caret"></b></a>
					<ul class="dropdown-menu multi-level">
						<?php echo item_menu('proveedores/proveedor/abm_proveedores', $texto['articulos']);?>
                    </ul>
				</li>
				<li>
					<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $texto['compras'] ?><b class="caret"></b></a>
					<ul class="dropdown-menu multi-level">
						<?php echo item_menu('proveedores/proveedor/abm_proveedores', $texto['proveedores']);?>
                    </ul>
				</li>
				<li>
					<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $texto['ventas'] ?> <b class="caret"></b></a>
					<ul class="dropdown-menu multi-level">
						<?php echo item_menu('clientes/cliente/abm_clientes', $texto['clientes']);?>
						<li class="dropdown-submenu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Datos Maestros</a>
                            <ul class="dropdown-menu">
                                <li class="dropdown-submenu">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Clientes</a>
                                    <ul class="dropdown-menu">
                                        <li class="dropdown-submenu">
											<?php echo item_menu('clientes/cliente/abm_clientes', $texto['clientes']);?>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                    </ul>
				</li>
			</ul>
        </div><!--/.nav-collapse -->
    </div>
</div>