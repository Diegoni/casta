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
				subtotal = $("#cantidad").val() * $("#precio").val();
				sumar_total(subtotal);
				document.getElementById("cantidad").value = '';
				document.getElementById("precio").value = '';
				$(".chosen-select").val('').trigger("chosen:updated");
				$('#name').trigger('chosen:activate');	
				$(".test").removeClass( "hide" )		
			}
		});
		
		
		function sumar_total(subtotal){
			if (typeof subtotal == "undefined")
			{
				subtotal = 0;
			}	
				
			var total = parseFloat(subtotal);
			$(".subtotal").each(function(){
				total = total + parseFloat($(this).val());
				
			})
			$("#subtotal").val(total.toFixed(2));
			
			impuesto = total * 21 / 100;
			
			$("#impuesto").val(impuesto.toFixed(2));
			
			valor_final = total + impuesto;
			
			$("#total").val(valor_final.toFixed(2));
		};
		
		
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
		
		$(".slidingDiv").hide();
		$(".title_up").hide();
        $(".title_down").show();
        $(".show_hide").show();
 
    	$('.show_hide').click(function(){
    		$(".slidingDiv").toggle( "drop", 1000 );
    		$(".title_down").slideToggle();
    		$(".title_up").slideToggle();
    	});
    	
    	 $(function() {
    		$( "#fecha" ).datepicker({ 
				dateFormat: 'dd-mm-yy', 
				minDate: -20, 
				maxDate: "+1M +10D" 
			});
			$.datepicker.regional['es'] = {
		        closeText: 'Cerrar',
		        prevText: '<Ant',
		        nextText: 'Sig>',
		        currentText: 'Hoy',
		        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
		        monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
		        dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
		        dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
		        dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
		        weekHeader: 'Sm',
		        dateFormat: 'dd/mm/yy',
		        firstDay: 1,
		        isRTL: false,
		        showMonthAfterYear: false,
		        yearSuffix: ''
		    };
    		$.datepicker.setDefaults($.datepicker.regional['es']);
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
	  				<form class='form-horizontal' method="post">
					<div class="row">
						<?php echo label_helper_horizontal($this->lang->line('proveedor')); ?>
						<?php echo select_helper_horizontal('supplier', $supplier, NULL, 6); ?>
						<div class="col-md-2">
							<a href='#' class='show_hide btn btn-default form-control'>
								<div class='title_down'>
									<i class="fa fa-arrow-down"></i>
									<?php echo $this->lang->line('detalles'); ?>
								</div>
								<div class='title_up'>
									<i class="fa fa-arrow-up"></i>
									<?php echo $this->lang->line('menos'); ?>
									<?php echo $this->lang->line('detalles'); ?>
								</div>
							</a>
						</div>
					</div>
												
						<div class='row slidingDiv'>
							<?php echo label_helper_horizontal($this->lang->line('impuesto')); ?>
							<?php echo select_helper_horizontal('taxs', $taxs, 1, 2); ?>
							<?php echo label_helper_horizontal($this->lang->line('moneda')); ?>
							<?php echo select_helper_horizontal('currencys', $currencys, 1, 2); ?>
							<?php echo label_helper_horizontal($this->lang->line('fecha')); ?>
							<?php echo input_helper_horizontal('fecha', date('d-m-Y'), 2, $this->lang->line('fecha')); ?>
						</div>
					</form>
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
					
					<div class="row hide test">
						<div class="col-md-2 cabecera"><?php echo $this->lang->line('codigo') ?></div>
						<div class="col-md-5 cabecera"><?php echo $this->lang->line('nombre') ?></div>
						<div class="col-md-1 cabecera"><?php echo $this->lang->line('cantidad') ?></div>
						<div class="col-md-1 cabecera"><?php echo $this->lang->line('precio') ?></div>
						<div class="col-md-2 cabecera"><?php echo $this->lang->line('subtotal') ?></div>
						<div class="col-md-1 cabecera"><?php echo $this->lang->line('opciones') ?></div>
					</div>
					<div class="cloundcontainer"></div>
					<hr>
					<div class="row hide test">
						<div class="col-md-2"><b class="pull-right"><?php echo $this->lang->line('subtotal') ?></b></div>
						<div class="col-md-2">
							<div class="input-group">
      							<div class="input-group-addon">$</div>
      							<input id='subtotal' class='form-control' readonly>
      						</div>
      					</div>
						<div class="col-md-2"><b class="pull-right"><?php echo $this->lang->line('impuesto') ?></b></div>
						<div class="col-md-2">
							<div class="input-group">
      							<div class="input-group-addon">$</div>
      							<input id='impuesto' class='form-control' readonly>
      						</div>
      					</div>
						<div class="col-md-2"><b class="pull-right"><?php echo $this->lang->line('total') ?></b></div>
						<div class="col-md-2">
							<div class="input-group">
      							<div class="input-group-addon">$</div>
      							<input id='total' class='form-control' readonly>
      						</div>
      					</div>
					</div>
					<hr>
					<div class="row hide test">
						<div class='col-md-6'>
						</div> 
					
						<div class='col-md-2'>
							<a href="<?php base_url().'/index.php/supplier/supplier/pedidos'?>" class='btn btn-danger form-control'>
								<i class="fa fa-trash-o"></i>
								<?php echo $this->lang->line('limpiar'); ?>
							</a>
						</div> 
						
						<div class='col-md-2'>
							<?php echo single_button($this->lang->line('save'), 'guardar', 'fa fa-floppy-o', 'primary', 'button') ?>
						</div> 
						
						<div class='col-md-2'>
							<?php echo single_button($this->lang->line('finalizar'), 'finalizar', 'fa fa-check-square-o', 'success', 'button') ?>
						</div>
					</div>	 
				</div>
		    </div>
	    </div>
    </div>