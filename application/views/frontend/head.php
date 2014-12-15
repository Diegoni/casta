<html>
<head>
<title>Hotel</title>

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

<!-- Libreria Jquery -->
<script src="<?php echo base_url().'librerias/jquery.js'?>" type="text/javascript"></script>

<!----------------------------------------------------------------------------
------------------------------------------------------------------------------
								Bootstrap
------------------------------------------------------------------------------
----------------------------------------------------------------------------->

<link href="<?php echo base_url().'librerias/bootstrap/css/bootstrap_front_carollo.css'?>" rel="stylesheet" media="screen">
<script src="<?php echo base_url().'librerias/bootstrap/js/bootstrap.js'?>"></script>


<!----------------------------------------------------------------------------
------------------------------------------------------------------------------
								Iconos
------------------------------------------------------------------------------
----------------------------------------------------------------------------->

<link href="<?php echo base_url().'librerias/font/css/font-awesome.css'?>" rel="stylesheet">
<link href="<?php echo base_url().'librerias/font2/css/whhg.css'?>" rel="stylesheet">



<!----------------------------------------------------------------------------
------------------------------------------------------------------------------
								Propios
------------------------------------------------------------------------------
----------------------------------------------------------------------------->

<link href="<?php echo base_url().'librerias/main/css/main.css'?>" rel="stylesheet" media="screen">
<script src="<?php echo base_url().'librerias/main/js/main.js'?>"></script>


<!----------------------------------------------------------------------------
------------------------------------------------------------------------------
								Jquery UI
------------------------------------------------------------------------------
----------------------------------------------------------------------------->

<link href="<?php echo base_url().'librerias/ui/jquery-ui.css'?>" rel="stylesheet" media="screen">
<script src="<?php echo base_url().'librerias/ui/jquery-ui.js'?>"></script>

<!----------------------------------------------------------------------------
------------------------------------------------------------------------------
								Jquery UI tiem picker
------------------------------------------------------------------------------
----------------------------------------------------------------------------->


<link href="<?php echo base_url() ?>librerias/ui/jquery.ui.timepicker.css?v=0.3.3" rel="stylesheet" type="text/css" />
<script src="<?php echo base_url() ?>librerias/ui/jquery.ui.core.min.js" type="text/javascript" ></script>
<script src="<?php echo base_url() ?>librerias/ui/jquery.ui.timepicker.js?v=0.3.3" type="text/javascript"></script>



<!----------------------------------------------------------------------------
------------------------------------------------------------------------------
								Fancybox
------------------------------------------------------------------------------
----------------------------------------------------------------------------->


<link rel="stylesheet" href="<?php echo base_url() ?>librerias/fancybox/jquery.fancybox.css" media="screen">
<script src="<?php echo base_url() ?>librerias/fancybox/jquery.fancybox.js"></script>
<script src="<?php echo base_url() ?>librerias/fancybox/jquery.fancybox.pack.js"></script>



</head>
<?php 
foreach ($hoteles as $hotel) {
	$latitud=$hotel->latitud;
	$longitud=$hotel->longitud;
}
?>
<body onload="initialize(<?php echo $latitud ?>,<?php echo $longitud?>)">

		

