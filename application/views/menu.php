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
					<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $this->lang->line('catalogo') ?><b class="caret"></b></a>
					<ul class="dropdown-menu multi-level">
						<?php echo item_menu('product/product/crud_product', $this->lang->line('productos'));?>
						<?php echo item_menu('product/Stock/stock', $this->lang->line('stock'));?>
						<li class="divider"></li>
						<li class="dropdown-submenu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            	<?php echo $this->lang->line('datos')." ".$this->lang->line('maestros'); ?>
                            </a>
                            <ul class="dropdown-menu">
								<?php echo item_menu('product/feature/crud_feature/', $this->lang->line('caracteristicas'));?>
							</ul>
                        </li>
                    </ul>
				</li>
				<li>
					<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $this->lang->line('compras') ?><b class="caret"></b></a>
					<ul class="dropdown-menu multi-level">
						<?php echo item_menu('supplier/supplier/crud_supplier', $this->lang->line('proveedores'));?>
						<?php echo item_menu('supplier/supplier/pedidos', $this->lang->line('pedidos')." ".$this->lang->line('proveedores'));?>
						<li class="divider"></li>
						<li class="dropdown-submenu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $this->lang->line('datos')." ".$this->lang->line('maestros') ?></a>
                            <ul class="dropdown-menu">
                            	<?php echo item_menu('general/datos_maestros/tax', $this->lang->line('impuesto'));?>
                            	<?php echo item_menu('general/datos_maestros/currencys', $this->lang->line('moneda'));?>
								<?php echo item_menu('general/datos_maestros/forma_pago', $this->lang->line('forma_pago'));?>
								<?php echo item_menu('general/datos_maestros/condicion_pago', $this->lang->line('condicion_pago'));?>
							</ul>
                        </li>
                    </ul>
				</li>
				<li>
					<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $this->lang->line('ventas')?> <b class="caret"></b></a>
					<ul class="dropdown-menu multi-level">
						<?php echo item_menu('customer/customer/crud_customer', $this->lang->line('clientes'));?>
						
						<li class="divider"></li>
						
						<li class="dropdown-submenu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $this->lang->line('datos')." ".$this->lang->line('maestros') ?></a>
                            <ul class="dropdown-menu">
                                <li class="dropdown-submenu">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $this->lang->line('clientes') ?></a>
                                    <ul class="dropdown-menu">
                                        <li class="dropdown-submenu">
											<?php echo item_menu('customer/gender/crud_gender', $this->lang->line('genero'));?>
											<?php echo item_menu('customer/risk/crud_risk', $this->lang->line('riesgo'));?>
											<?php echo item_menu('customer/group/crud_group', $this->lang->line('grupo'));?>
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