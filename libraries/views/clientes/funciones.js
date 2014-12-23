$(document).ready(function(){
	$(".autocompletar").keyup(function(){
		var info = $(this).val();
		$.post('cliente/autocompletar',{ info : info }, function(data){
			if(data != ''){
				$('.contenedor_autocomplete').show();
				$(".contenedor_autocomplete").html(data);
			}else{
				$(".contenedor_autocomplete").html('');
			}
	    })
    })
})