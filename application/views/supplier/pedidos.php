<body>
	<div class='container'>  
	    <div class="col-md-12">
			<div class="panel panel-default">
	  			<div class="panel-heading">
	  				<?php echo $this->lang->line('pedidos') ?>
	  			</div>
	  			<div class="panel-body">
	  				<div class="row">
					<form class='form-horizontal'>
						<?php echo label_helper_horizontal($this->lang->line('proveedor')); ?>
						<?php echo select_helper_horizontal('supplier', $supplier); ?>
					</form>
					</div>
					<hr>
					
					<div class="row">
					<form class='form-horizontal' method="post">
						<?php echo label_helper_horizontal($this->lang->line('codigo')); ?>
						<?php echo input_helper_horizontal('upc', NULL, 4, 'UPC'); ?>
						<?php echo label_helper_horizontal($this->lang->line('cantidad')); ?>
						<?php echo input_helper_horizontal('cantidad', NULL, 4, $this->lang->line('cantidad')); ?>
						<?php echo label_helper_horizontal($this->lang->line('nombre')); ?>
						<?php echo input_helper_horizontal('name', NULL, 8, $this->lang->line('nombre')); ?>
						<div class='col-md-2'>
							<?php echo single_button($this->lang->line('agregar'), 'agregar', 'fa fa-plus-circle', 'primary', 'submit') ?>
						</div> 
					</form>
					</div>
					<hr>
					
					<div class="row">
						<div class="col-md-2"><?php echo $this->lang->line('codigo') ?></div>
						<div class="col-md-6"><?php echo $this->lang->line('nombre') ?></div>
						<div class="col-md-2"><?php echo $this->lang->line('cantidad') ?></div>
						<div class="col-md-2"><?php echo $this->lang->line('opciones') ?></div>
					</div>
				</div>
		    </div>
	    </div>
    </div>
</body>
</html>
