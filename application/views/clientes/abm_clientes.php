<div class="container">
	
	<?php 
	if(isset($mensaje))
	{
		echo $mensaje;	
	}
	?>
	<!----------------------------------------------------------------------------------
	------------------------------------------------------------------------------------
			Busqueda
	------------------------------------------------------------------------------------
	----------------------------------------------------------------------------------->
		
	<div class="row search_form">
		<?php echo autocomplete($clientes, 'b_codigo', 'nIdCliente');?>
		<?php echo input_helper_horizontal('b_codigo', NULL, 2, $texto['codigo']);?>
		<?php echo label_helper_horizontal($texto['codigo']);?>
		<?php echo autocomplete($clientes, 'b_nombre', array('cNombre', 'cApellido'));?>
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
							'perfiles'	=> $texto['perfiles'],
							'temas'		=> $texto['temas'],
							'notas'		=> $texto['notas'],
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
				<?php echo input_helper_horizontal('cNombre', NULL, 5, $texto['nombre'], 'email');?>
				<?php echo input_helper_horizontal('cApellido', NULL, 4, $texto['apellido'], 'text');?>
				<?php echo label_helper_horizontal('', 1);?>
	  		</div>
	  		
	  		<div class="form-group">
				<?php echo label_helper_horizontal($texto['empresa']);?>
				<?php echo textarea_helper_horizontal('cEmpresa', NULL, 9, 3);?>
				<?php echo label_helper_horizontal('',1);?>
	  		</div>
	  		
	  		<div class="form-group">
				<?php echo label_helper_horizontal($texto['cuil']);?>
				<?php echo input_helper_horizontal('cCuil', NULL, 4, $texto['cuil'], 'text');?>
				<?php echo check_helper_horizontal('bExentoIVA', $texto['exento']);?>
				<?php echo check_helper_horizontal('bRecargo', $texto['recargo']);?>
				<?php echo check_helper_horizontal('bCredito', $texto['tiene']." ".$texto['credito']);?>
	  		</div>
	  		
	  		<div class="form-group">
	  			<?php echo label_helper_horizontal($texto['tipo']." ".$texto['cliente']);?>
	  			<?php echo select_helper_horizontal('nIdTipoCliente', $tipos, 4, 'required');?>
	  			<?php echo label_helper_horizontal($texto['grupo']." ".$texto['cliente']);?>
	  			<?php echo select_helper_horizontal('nIdGrupoCliente', $grupos, 4);?>
	  		</div>
	  		
	  		<div class="form-group">
	  			<?php echo label_helper_horizontal($texto['tarifa']);?>
	  			<?php echo select_helper_horizontal('nIdTipoTarifa', $tarifas, 4);?>
	  			<?php echo label_helper_horizontal($texto['idioma']);?>
	  			<?php echo select_helper_horizontal('nIdIdioma', $idiomas, 4);?>
	  		</div>
	  		
	  		<div class="form-group">
	  			<?php echo label_helper_horizontal($texto['referencia']);?>
	  			<?php echo input_helper_horizontal('cReferencia', NULL, 4, $texto['referencia']);?>
	  			<?php echo label_helper_horizontal($texto['estado']);?>
	  			<?php echo select_helper_horizontal('nIdEstado', $estados, 4);?>
	  		</div>
	</div>
	</form>
	<div class="row abm_form tab-pane fade" id="perfiles">
		Perfiles
	</div>
	<div class="row abm_form tab-pane fade" id="temas">
		Temas
	</div>
	<div class="row abm_form tab-pane fade" id="notas">
		<?php echo label_helper_horizontal($texto['notas']); ?>
		<?php echo textarea_helper_horizontal('cNotas', NULL, 10, 3, TRUE); ?>
	</div>
	</div>
</div>
