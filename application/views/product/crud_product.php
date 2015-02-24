<div class="container">
	<?php 
	if(isset($mensaje))
	{
		echo $mensaje;	
	}
	
	if(isset($b_registros))
	{
		foreach ($b_registros as $key => $value) {
			$b_registro[$key] = $value;
		}
		
		foreach ($b_registros_lang as $key => $value) {
			if(!in_array($key, $b_registro))
			{
				$b_registro[$key] = $value;
			}
			
		}
	}
	else 
	{
		foreach ($registro_model as $key => $value) {
			$b_registro[$key] = NULL;
		}
		
		foreach ($lang_model as $key => $value) {
			if(!in_array($key, $b_registro))
			{
				$b_registro[$key] = NULL;
			}
			
		}
			
		$b_registro['id_product']	= NULL;
	}
	 
	?>
	<!----------------------------------------------------------------------------------
	------------------------------------------------------------------------------------
			Busqueda
	------------------------------------------------------------------------------------
	----------------------------------------------------------------------------------->
		
	<div class="row search_form">
		<form class="form-horizontal" method="post">
		<?php echo input_helper_horizontal('b_codigo', $b_registro['id_product'], 2, $texto['codigo']);?>
		<?php echo label_helper_horizontal($texto['codigo'], 1);?>
		<?php echo input_helper_horizontal('b_nombre', NULL, 7, $texto['nombre']." ".$texto['producto']);?>
		<?php echo autocomplete($productos, 'b_nombre', array('id_product', 'name'), 'id_product', 'b_codigo');?>
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
		
		</div>
	</div>
	
	
	<!----------------------------------------------------------------------------------
	------------------------------------------------------------------------------------
			Submenu
	------------------------------------------------------------------------------------
	----------------------------------------------------------------------------------->
	
	<div class="row submenu">
		<div class="col-sm-12">
		
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
			<?php echo label_helper_horizontal($texto['nombre'], 2);?>
			<?php echo input_helper_horizontal('name', $b_registro['name'], 4, $texto['nombre'], 'text');?>
			<?php echo label_helper_horizontal($texto['codigo']." ".$texto['referencia'], 2);?>
			<?php echo input_helper_horizontal('name', $b_registro['reference'], 4, $texto['referencia'], 'text');?>
		</div>
		
		<div class="form-group">
			<?php echo label_helper_horizontal($texto['descripcion']." ".$texto['corta']);?>
			<?php echo textarea_helper_horizontal('description_short', $b_registro['description_short'], 10, 3);?>
			<?php echo label_helper_horizontal('',1);?>
	  	</div>
	  	
	  	<div class="form-group">
			<?php echo label_helper_horizontal($texto['descripcion']);?>
			<?php echo textarea_helper_horizontal('description', $b_registro['description'], 10, 5);?>
			<?php echo label_helper_horizontal('',1);?>
	  	</div>
	  	
	 </div>
	</div>
	
</div>
