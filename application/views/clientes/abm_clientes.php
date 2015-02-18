<div class="container">
	
	<?php 
	if(isset($mensaje))
	{
		echo $mensaje;	
	}
	?>
	<?php 
		if(isset($b_clientes))
		{
			foreach ($b_clientes as $row) {
				$b_cliente = array(
						'nIdCliente'		=> $row->nIdCliente,
						'cNombre'			=> $row->cNombre,
						'cApellido'			=> $row->cApellido,
						'cEmpresa'			=> $row->cEmpresa,
						'cCuil'				=> $row->cCuil,	
						'nIdTipoCliente'	=> $row->nIdTipoCliente,
						'nIdGrupoCliente'	=> $row->nIdGrupoCliente,
						'nIdTipoTarifa'		=> $row->nIdTipoTarifa,
						'nIdIdioma'			=> $row->nIdIdioma,
						'cReferencia'		=> $row->cReferencia,
						'nIdEstado'			=> $row->nIdEstado
				);
			}
		}
		else {
			$b_cliente = array(
					'nIdCliente'		=> NULL,
					'cNombre'			=> NULL,
					'cApellido'			=> NULL,
					'cEmpresa'			=> NULL,
					'cCuil'				=> NULL,
					'nIdTipoCliente'	=> NULL,
					'nIdGrupoCliente'	=> NULL,
					'nIdTipoTarifa'		=> NULL,
					'nIdIdioma'			=> NULL,
					'cReferencia'		=> NULL,
					'nIdEstado'			=> NULL		
			);
		}
		?>
	<!----------------------------------------------------------------------------------
	------------------------------------------------------------------------------------
			Busqueda
	------------------------------------------------------------------------------------
	----------------------------------------------------------------------------------->
		
	<div class="row search_form">
		<form class="form-horizontal" method="post">
		<?php echo autocomplete($clientes, 'b_codigo', 'nIdCliente');?>
		<?php echo input_helper_horizontal('b_codigo', $b_cliente['nIdCliente'], 2, $texto['codigo']);?>
		<?php echo label_helper_horizontal($texto['codigo'], 1);?>
		<?php echo input_helper_horizontal('b_nombre', $b_cliente['cNombre']." ".$b_cliente['cApellido'], 7, $texto['nombre']." ".$texto['apellido']);?>
		<?php echo autocomplete($clientes, 'b_nombre', array('cNombre', 'cApellido'), 'nIdCliente', 'b_codigo');?>
		<div class="col-sm-2">
			<button class="btn btn-default form-control" type="submit">
				<i class="fa fa-search"></i> <?php echo  $texto['buscar']?>
			</a>
		</div>
		</form>
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
		<input name="nIdCliente" value="<?php echo $b_cliente['nIdCliente']?>" type="hidden"/>
		<div class="form-group">
			<?php echo label_helper_horizontal($texto['nombre']." , ".$texto['apellido'], 2);?>
			<?php echo input_helper_horizontal('cNombre', $b_cliente['cNombre'], 5, $texto['nombre'], 'text');?>
			<?php echo input_helper_horizontal('cApellido', $b_cliente['cApellido'], 4, $texto['apellido'], 'text');?>
			<?php echo label_helper_horizontal('', 1);?>
	  	</div>
	  		
	  	<div class="form-group">
			<?php echo label_helper_horizontal($texto['empresa']);?>
			<?php echo textarea_helper_horizontal('cEmpresa', $b_cliente['cEmpresa'], 9, 3);?>
			<?php echo label_helper_horizontal('',1);?>
	  	</div>
	  		
	  	<div class="form-group">
			<?php echo label_helper_horizontal($texto['cuil']);?>
			<?php echo input_helper_horizontal('cCuil', $b_cliente['cCuil'], 4, $texto['cuil'], 'text');?>
			<?php echo check_helper_horizontal('bExentoIVA', $texto['exento']);?>
			<?php echo check_helper_horizontal('bRecargo', $texto['recargo']);?>
			<?php echo check_helper_horizontal('bCredito', $texto['tiene']." ".$texto['credito']);?>
	  	</div>
	  		
	  	<div class="form-group">
	  		<?php echo label_helper_horizontal($texto['tipo']." ".$texto['cliente']);?>
	  		<?php echo select_helper_horizontal('nIdTipoCliente', $tipos,  $b_cliente['nIdTipoCliente'], 4, 'required');?>
	  		<?php echo label_helper_horizontal($texto['grupo']." ".$texto['cliente']);?>
	  		<?php echo select_helper_horizontal('nIdGrupoCliente', $grupos, $b_cliente['nIdGrupoCliente'], 4);?>
	  	</div>
	  		
	  	<div class="form-group">
	  		<?php echo label_helper_horizontal($texto['tarifa']);?>
	  		<?php echo select_helper_horizontal('nIdTipoTarifa', $tarifas, $b_cliente['nIdTipoTarifa'], 4);?>
	  		<?php echo label_helper_horizontal($texto['idioma']);?>
	  		<?php echo select_helper_horizontal('nIdIdioma', $idiomas, $b_cliente['nIdIdioma'], 4);?>
	  	</div>
	  		
	  	<div class="form-group">
	  		<?php echo label_helper_horizontal($texto['referencia']);?>
	  		<?php echo input_helper_horizontal('cReferencia', $b_cliente['cReferencia'], 4, $texto['referencia']);?>
	  		<?php echo label_helper_horizontal($texto['estado']);?>
	  		<?php echo select_helper_horizontal('nIdEstado', $estados, $b_cliente['nIdEstado'], 4);?>
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
