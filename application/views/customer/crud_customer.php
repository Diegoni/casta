<div class="container">
	
	<?php 
	if(isset($mensaje))
	{
		echo $mensaje;	
	}
	
	if(isset($b_clientes))
	{
		foreach ($b_clientes as $key => $value) {
			$b_cliente[$key] = $value;
		}
	}
	else 
	{
		foreach ($clientes_model as $key => $value) {
			$b_cliente[$key] = NULL;
		}
			
		$b_cliente['id_customer']	= NULL;
	}
	 
	?>
	<!----------------------------------------------------------------------------------
	------------------------------------------------------------------------------------
			Busqueda
	------------------------------------------------------------------------------------
	----------------------------------------------------------------------------------->
		
	<div class="row search_form">
		<form class="form-horizontal" method="post">
		<?php echo autocomplete($clientes, 'b_codigo', 'id_customer');?>
		<?php echo input_helper_horizontal('b_codigo', $b_cliente['id_customer'], 2, $texto['codigo']);?>
		<?php echo label_helper_horizontal($texto['codigo'], 1);?>
		<?php echo input_helper_horizontal('b_nombre', $b_cliente['firstname']." ".$b_cliente['lastname'], 7, $texto['nombre']." ".$texto['apellido']);?>
		<?php echo autocomplete($clientes, 'b_nombre', array('firstname', 'lastname'), 'id_customer', 'b_codigo');?>
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
			<!--
			<?php echo add_button($texto['add']); ?>
			-->
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
							/*'temas'		=> $texto['temas'],*/
							'notas'		=> $texto['notas'],
							/*'usuarios'	=> $texto['usuarios'],*/
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
		
		<input name="id_customer" value="<?php echo $b_cliente['id_customer']?>" type="hidden"/>
		<div class="form-group">
			<?php echo label_helper_horizontal($texto['nombre']." , ".$texto['apellido'], 2);?>
			<?php echo input_helper_horizontal('firstname', $b_cliente['firstname'], 5, $texto['nombre'], 'text');?>
			<?php echo input_helper_horizontal('lastname', $b_cliente['lastname'], 4, $texto['apellido'], 'text');?>
			<?php echo label_helper_horizontal('', 1);?>
	  	</div>
	  	
	  	<div class="form-group">
			<?php echo label_helper_horizontal($texto['empresa']);?>
			<?php echo textarea_helper_horizontal('company', $b_cliente['company'], 9, 3);?>
			<?php echo label_helper_horizontal('',1);?>
	  	</div>
	  		
	  	<div class="form-group">
			<?php echo label_helper_horizontal($texto['cuil']);?>
			<?php echo input_helper_horizontal('cuil', $b_cliente['cuil'], 4, $texto['cuil'], 'text');?>
			<?php echo check_helper_horizontal('active', $b_cliente['active'], $texto['activo']);?>
			<?php echo check_helper_horizontal('newsletter', $b_cliente['newsletter'], $texto['boletin']);?>
			<?php echo check_helper_horizontal('optin', $b_cliente['optin'], $texto['optar_en']);?>
	  	</div>
	  	
	  		
	  	<div class="form-group">
	  		<!--
	  		<?php echo label_helper_horizontal($texto['tipo']." ".$texto['cliente']);?>
	  		<?php echo select_helper_horizontal('nIdTipoCliente', $tipos,  $b_cliente['nIdTipoCliente'], 4, 'required');?>
	  		-->
	  		<?php echo label_helper_horizontal($texto['grupo']." ".$texto['cliente']);?>
	  		<?php echo select_helper_horizontal('id_default_group', $grupos, $b_cliente['id_default_group'], 4);?>
	  	</div>
	  	
	  	<!--		
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
		<div class="row">
			<div class="col-md-2">
				<?php echo single_button($texto['telefonos'], 'telefonos', 'fa-phone')?>
			</div>
			<div class="hide" id='form-telefonos'>
				
			</div>
			
			<div class="col-md-10">
				<?php 
				if(isset($telefonos))
				{
					foreach ($telefonos as $row) {
						echo "<div class='row'>";
							echo "<div class='col-md-4'>".$row->cTelefono."</div>";
							echo "<div class='col-md-4'>".$row->nIdTipo."</div>";
							echo "<div class='col-md-4'>".$row->cDescripcion."</div>";
						echo "</div>";	
					}
				}
				?>
			</div>
		</div>
		<div class="row">
			<div class="col-md-2">
				<?php echo single_button($texto['direcciones'], 'telefonos', 'icon-address')?>
			</div>
			
			<div class="col-md-10">
				<?php 
				if(isset($direcciones))
				{
					foreach ($direcciones as $row) {
						echo "<div class='row'>";
							echo "<div class='col-md-4'>".$row->cCalle." ".$row->cCP."</div>";
							echo "<div class='col-md-4'>".$row->nIdTipo."</div>";
							echo "<div class='col-md-4'>".$row->cDescripcion."</div>";
						echo "</div>";	
					}
				}
				?>
			</div>
		</div>
		<div class="row">
			<div class="col-md-2">
				<?php echo single_button($texto['emails'], 'emails', 'fa-envelope-o')?>
			</div>
			
			<div class="col-md-10">
				<?php 
				if(isset($emails))
				{
					foreach ($emails as $row) {
						echo "<div class='row'>";
							echo "<div class='col-md-4'>".$row->cEMail."</div>";
							echo "<div class='col-md-4'>".$row->nIdTipo."</div>";
							echo "<div class='col-md-4'>".$row->cDescripcion."</div>";
						echo "</div>";	
					}
				}
				?>
			</div>
		</div>
		<div class="row">
			<div class="col-md-2">
				<?php echo single_button($texto['contactos'], 'contactos', 'fa-user')?>
			</div>
			
			<div class="col-md-10">
				<?php 
				if(isset($contactos))
				{
					foreach ($contactos as $row) {
						echo "<div class='row'>";
							echo "<div class='col-md-4'>".$row->cApellido." ".$row->cNombre."</div>";
							echo "<div class='col-md-4'>".$row->nIdTipo."</div>";
							echo "<div class='col-md-4'>".$row->cDescripcion."</div>";
						echo "</div>";	
					}
				}
				?>
			</div>
		</div>
		
	</div>
	<div class="row abm_form tab-pane fade" id="temas">
		Temas
	</div>
	<div class="row abm_form tab-pane fade" id="notas">
		<?php echo label_helper_horizontal($texto['notas']); ?>
		<?php echo textarea_helper_horizontal('cNotas', NULL, 10, 3, TRUE); ?>
	</div>
	</div>
	-->
</div>
