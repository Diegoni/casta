<script>
$(function() {
    var clientes_array = [
      	<?php 
		foreach ($clientes as $cliente) {
			echo '"'.$cliente->cNombre.', '.$cliente->cApellido.'",';
		}
		?>
    ];
    $( "#b_nombre" ).autocomplete({
      minLength: 2, //minimo de caracteres para buscar
      source: function(request, response) {
			var results = $.ui.autocomplete.filter(clientes_array, request.term);
			response(results.slice(0, 10));
    		}//fuente y limitacion
    });
});
  
  
  
 
</script>
