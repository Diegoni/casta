<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css" />
 <script>
	function carga_ajax(ruta,id,div) 
	{
		$.post(ruta,{id:id,},function(resp)
		{
			$("#"+div+"").html(resp);
		});
	}
    </script>
<?php echo js_libreria('jquery/jquery.js'); ?> 
<h1>Ejemplo de trabajo con ajax desde Codeigniter</h1>
<?php 
$num1="46";
$num2="67";
?>
<a href="javascript:void(0);" 
	onclick="carga_ajax('<?php echo base_url()?>index.php/ajax/respuesta','1','aqui')">
	noticia 1</a>
<br />
<a href="javascript:void(0);" onclick="carga_ajax('<?php echo base_url()?>index.php/ajax/respuesta','2','otro')">noticia 2</a>
<hr />
<div id="aqui">texto inicial</div>
<hr />
<p id="otro"></p>
