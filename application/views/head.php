<html>
<head>
<title>Casta</title>

<script type="text/javascript">// <![CDATA[

// ]]></script>

<!--BEGIN META TAGS-->
<META NAME="keywords" CONTENT="">
<META NAME="description" CONTENT="Hotel">
<META NAME="rating" CONTENT="General">
<META NAME="ROBOTS" CONTENT="ALL">
<!--END META TAGS-->

<!-- Charset tiene que estar en utf-8 para que tome ñ y acentos -->
<meta http-equiv="Content-type" content="text/html" charset="utf-8" />


<!-- Iconos -->
<link type="image/x-icon" href="imagenes/favicon.ico" rel="icon" />
<link type="image/x-icon" href="imagenes/favicon.ico" rel="shortcut icon" />

<?php
/**********************************************************************************
 **********************************************************************************
 * 
 * 				Librerias
 * 
 * ********************************************************************************
 **********************************************************************************/

	// Jquery
	echo js_libreria('jquery/jquery.js'); 

	// Bootstrap
	//echo js_libreria('bootstrap/js/bootstrap.js');
	//echo css_libreria('bootstrap/css/bootstrap.css');
	//echo css_libreria('bootstrap/css/bootstrap_back.css');
	echo js_libreria('eden-ui/js/bootstrap.js');
	echo css_libreria('eden-ui/css/bootstrap.css');
	echo css_libreria('eden-ui/skins/eden.css');
	
	// Jquery UI
	echo js_libreria('jquery-ui/jquery-ui.js');
	echo css_libreria('jquery-ui/jquery-ui.css');
	
	// Menu
	echo css_libreria('main/css/menu.css');
	
	// Iconos
	echo css_libreria('font/whhg-font/css/whhg.css');
	echo css_libreria('font/font-awesome/css/font-awesome.css');
	
	// Propios
	echo js_libreria('main/js/main.js');
	echo css_libreria('main/css/main.css');
	
	// Chosen
	echo js_libreria('chosen/chosen.jquery.js');
	echo css_libreria('chosen/chosen.css');
	
	//CKEditor
	echo js_libreria('ckeditor/ckeditor.js');

	//Bootstrap Switch, ver como cambiar tamaño
	/*
	echo css_libreria('bootstrap-switch-master/dist/css/bootstrap3/bootstrap-switch.css');
	echo js_libreria('bootstrap-switch-master/dist/js/bootstrap-switch.js');
	echo js_libreria('bootstrap-switch-master/docs/js/highlight.js');
	echo js_libreria('bootstrap-switch-master/docs/js/main.js');
	*/
			
  	// Librerias del controlador
  	if(isset($js_libreria))
  	{
  		if(is_array($js_libreria)){
  			foreach ($js_libreria as $key => $value) {
				echo $value;
			}	
	  	}
	  	else
	  	{
	  		echo $js_libreria;
	  	}
			
  	}