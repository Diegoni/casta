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