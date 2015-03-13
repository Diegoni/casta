	<script>
	$(document).ready(function(){
		$('#name').trigger('chosen:activate');	
		
		$("#agregar").click(function(){
			if($("#upc").val()=="" && $("#name").val()=="")
			{
				alert("<?php echo $this->lang->line('seleccione')." ".$this->lang->line('una')." ".$this->lang->line('opcion')?>");	
				$("#name").focus();
				$('#name').trigger('chosen:activate');	
			}
			else
			if($("#cantidad").val()=="" || $("#cantidad").val()==0)
			{
				alert("<?php echo $this->lang->line('ingrese')." ".$this->lang->line('cantidad')?>");	
				$("#cantidad").focus();
			}
			else
			if($("#precio").val()=="" || $("#precio").val()==0)
			{
				alert("<?php echo $this->lang->line('ingrese')." ".$this->lang->line('precio')?>");	
				$("#precio").focus();
			}
			else
			{
				$.ajax({
					url: '<?php echo base_url(); ?>' + 'index.php/supplier/supplier/buscar',
					type: 'POST',
					data: $('#form').serialize(),
					success: function(msj){
						$('.cloundcontainer').append('<div class="cloud">'+msj+'</div>');
					}
				});
				document.getElementById("cantidad").value = '';
				document.getElementById("precio").value = '';
				$(".chosen-select").val('').trigger("chosen:updated");
				$('#name').trigger('chosen:activate');			
			}
		});
		
		$(document).keypress(function(e) {
		    if(e.which == 13) {
		    	if($('#cantidad').is(":focus"))
		    	{
		    		$('#precio').focus();
		    	}
		    	else
		    	if($('#precio').is(":focus"))
		    	{
		    		$('#agregar').click();
		    	}	
		    	else
		    	{
		    		$('#cantidad').focus();	
		    	}
		    }
		});
		
		$("#guardar").click(function(){
			alert()
		});
		
		$('#cantidad').focus(function(){
			var name = $('#name').val();
			$.ajax({
					url: '<?php echo base_url(); ?>' + 'index.php/supplier/supplier/buscar_precio/' + name,
					type: 'POST',
					data: $('#form').serialize(),
					success: function(msj){
						myString = $.trim(msj);
						$('#precio').val(myString);
					}
			});
		});
	});
	</script>
	
	<div class='container'>  
	    <div class="col-md-12">
			<div class="panel panel-default">
	  			<div class="panel-heading">
	  				<?php echo $this->lang->line('pedidos') ?>
	  			</div>
	  			<div class="panel-body">
	  				<div class="row">
					<form class='form-horizontal' method="post">
						<?php echo label_helper_horizontal($this->lang->line('proveedor')); ?>
						<?php echo select_helper_horizontal('supplier', $supplier, NULL, 8); ?>
						<div class='col-md-2'>
							<?php echo single_button($this->lang->line('save'), 'guardar', 'fa fa-floppy-o', 'success', 'button') ?>
						</div> 
					</form>
					</div>
					<hr>
					
					<div class="row">
					<form class='form-horizontal' method="post" id="form">
						<?php echo select_helper_horizontal('upc', $products_upc, NULL,2); ?>
						<?php echo select_helper_horizontal('name', $products_name, NULL,6); ?>
						<?php echo input_helper_horizontal('cantidad', NULL, 1, $this->lang->line('cantidad')); ?>
						<?php echo input_helper_horizontal('precio', NULL, 2, $this->lang->line('precio')); ?>
						<div class='col-md-1'>
							<?php echo single_button(NULL, 'agregar', 'fa fa-plus-circle', 'primary', 'button') ?>
						</div> 
					</form>
					</div>
					<hr>
					
					<div class="row">
						<div class="col-md-1">ID</div>
						<div class="col-md-2"><?php echo $this->lang->line('codigo') ?></div>
						<div class="col-md-5"><?php echo $this->lang->line('nombre') ?></div>
						<div class="col-md-1"><?php echo $this->lang->line('cantidad') ?></div>
						<div class="col-md-1"><?php echo $this->lang->line('precio') ?></div>
						<div class="col-md-2"><?php echo $this->lang->line('opciones') ?></div>
					</div>
					<div class="cloundcontainer"></div>
				</div>
		    </div>
	    </div>
    </div>