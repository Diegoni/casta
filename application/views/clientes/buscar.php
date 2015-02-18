<script>
$('#b_buscar').click(function() {
  	$.ajax({
		url: '/clientes/buscar.php',
		type: 'POST',
		async: true,
		data: 'parametro1=valor1&parametro2=valor2',
		success: procesaRespuesta,
		error: muestraError
	});
});
</script>