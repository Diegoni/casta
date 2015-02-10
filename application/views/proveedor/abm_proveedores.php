<div class="container">
	<!----------------------------------------------------------------------------------
	------------------------------------------------------------------------------------
			Busqueda
	------------------------------------------------------------------------------------
	----------------------------------------------------------------------------------->
		
	<div class="row search_form">
		<?php echo autocomplete($proveedores, 'b_codigo', 'nIdCliente');?>
		<?php echo input_helper_horizontal('b_codigo', NULL, 2, $texto['codigo']);?>
		<?php echo label_helper_horizontal($texto['codigo']);?>
		<?php echo autocomplete($proveedores, 'b_nombre', array('cNombre', 'cApellido'));?>
		<?php echo input_helper_horizontal('b_nombre', NULL, 8, $texto['nombre']." ".$texto['apellido']);?>
	</div>
	
	<form class="form-horizontal" method="post">
	<!----------------------------------------------------------------------------------
	------------------------------------------------------------------------------------
			Acciones
	------------------------------------------------------------------------------------
	----------------------------------------------------------------------------------->
	
	<div class="row action_buttons">
		<div class="col-md-12">
			<?php echo action_button($texto['action']); ?>
			<?php echo add_button($texto['add']); ?>
			<?php echo save_button($texto['save']); ?>
			<?php echo refresh_button($texto['refresh']); ?>
			<?php echo print_button($texto['print']); ?>
		</div>
	</div>
	
	
	<!----------------------------------------------------------------------------------
	------------------------------------------------------------------------------------
			Submenu
	------------------------------------------------------------------------------------
	----------------------------------------------------------------------------------->
	
	<div class="row submenu">
		<div class="col-sm-12">
			<?php
				$datos = array
						(
							'general'	=> $texto['general'],
							'descuentos'=> $texto['descuentos'],
							'perfiles'	=> $texto['perfiles'],
							'usuarios'	=> $texto['usuarios'],
							'historico'	=> $texto['historico'],
							'busqueda'	=> $texto['busqueda']
						);
						
				echo sub_menu($datos);
			?>
		</div>
	</div>
	
	<!----------------------------------------------------------------------------------
	------------------------------------------------------------------------------------
			Formulario
	------------------------------------------------------------------------------------
	----------------------------------------------------------------------------------->
	<div class="tab-content">
	<div class="row abm_form tab-pane fade in active" id="general">
			<div class="form-group">
				<?php echo label_helper_horizontal($texto['nombre']." , ".$texto['apellido'], 2);?>
				<?php echo input_helper_horizontal('cNombre', NULL, 5, $texto['nombre']);?>
				<?php echo input_helper_horizontal('cApellido', NULL, 4, $texto['apellido']);?>
				<?php echo label_helper_horizontal('', 1);?>
	  		</div>
	  		<div class="form-group">
				<?php echo label_helper_horizontal($texto['empresa']);?>
				<?php echo textarea_helper_horizontal('cEmpresa', NULL, 9, 3);?>
				<?php echo label_helper_horizontal('',1);?>
	  		</div>
	  		<div class="form-group">
				<?php echo label_helper_horizontal($texto['cuil']);?>
				<?php echo input_helper_horizontal('cCuil', NULL, 4, $texto['cuil']);?>
				<?php echo check_helper_horizontal('c_exento', $texto['exento']);?>
				<?php echo check_helper_horizontal('c_recargo', $texto['recargo']);?>
				<?php echo check_helper_horizontal('c_cuenta', $texto['tiene']." ".$texto['cuenta']);?>
	  		</div>
	  		<div class="form-group">
	  			<?php echo label_helper_horizontal($texto['descuentos']);?>
				<?php echo input_helper_horizontal('fDescuento', NULL, 3, $texto['descuentos']);?>
				<?php echo label_helper_horizontal($texto['compra']." ".$texto['minima']);?>
				<?php echo input_helper_horizontal('fDescuento', NULL, 3, $texto['compra']." ".$texto['minima']);?>
				<?php echo check_helper_horizontal('c_desactivado', $texto['desactivado']);?>
	  		</div>
	</div>
	</form>
	<div class="row abm_form tab-pane fade" id="perfiles">
		Perfiles
	</div>
	<div class="row abm_form tab-pane fade" id="descuentos">
		Temas
	</div>
	</div>
</div>
