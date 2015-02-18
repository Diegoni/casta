//----------------------------------------------------------------------------------
//------------------------------------------------------------------------------------
//			Mensaje de form
//------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------->
  
  $(function() {
    $( "#dialog-message-ok" ).dialog({
      	modal: true,
     	show: "blind",
    	hide: "clip",
    	dialogClass: "dialog-message-ok",    	
    	open: function(event, ui) {
        	setTimeout(function(){
            	$('#dialog-message-ok').dialog('close');                
        	}, 1500);
    	}
    });
  });
  

  $(function() {
    $( "#dialog-message-error" ).dialog({
      	modal: true,
     	show: "blind",
    	hide: "clip",
    	dialogClass: "dialog-message-error",    	
		buttons: {
	        Ok: function() {
	          $( this ).dialog( "close" );
	        }
      	}
    });
  });
  
  
//------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------
//			Validación de formularios
//------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------> 
  

$(document).ready(function() {
    $('.input-group input[required], .input-group textarea[required], .input-group select[required]').on('keyup change', function() {
		var $form = $(this).closest('form'),
            $group = $(this).closest('.input-group'),
			$addon = $group.find('.input-group-addon'),
			$icon = $addon.find('span'),
			state = false;
            
    	if (!$group.data('validate')) {
			state = $(this).val() ? true : false;
		}else if ($group.data('validate') == "email") {
			state = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test($(this).val())
		}else if($group.data('validate') == 'phone') {
			state = /^[(]{0,1}[0-9]{3}[)]{0,1}[-\s\.]{0,1}[0-9]{3}[-\s\.]{0,1}[0-9]{4}$/.test($(this).val())
		}else if ($group.data('validate') == "length") {
			state = $(this).val().length >= $group.data('length') ? true : false;
		}else if ($group.data('validate') == "number") {
			state = !isNaN(parseFloat($(this).val())) && isFinite($(this).val());
		}

		if (state) {
				$addon.removeClass('danger');
				$addon.addClass('success');
				$icon.attr('class', 'glyphicon glyphicon-ok');
		}else{
				$addon.removeClass('success');
				$addon.addClass('danger');
				$icon.attr('class', 'glyphicon glyphicon-remove');
		}
        
        if ($form.find('.input-group-addon.danger').length == 0) {
            $form.find('[type="submit"]').prop('disabled', false);
        }else{
            $form.find('[type="submit"]').prop('disabled', true);
        }
	});
    
    $('.input-group input[required], .input-group textarea[required], .input-group select[required]').trigger('change');
});
  
  
//------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------
//			Validación de formularios
//------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------> 


function carga_ajax(ruta,id,div) 
	{
		var id=$("#"+id+"").val();
		var arr = div;
		
		$.post(ruta,{id:id,},function(resp)
		{
			for (var i in arr)
			{
				$("#"+arr[i]+"").val(resp);
			}
		});
		
	}