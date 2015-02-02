<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Bibliopola
 *
 * Vista de página en blanco
 *
 * @package		Bibliopola 5.0
 * @subpackage	Views
 * @category	app
 * @author		Alejandro López
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @filesource
 */
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF8" />
<title><?php echo $this->config->item('bp.application.name');?></title>
<style type="text/css">
body {
	background-color: #fff;
	color: #000;
}

#loading{
	position:absolute;
	left:45%;
	top:40%;
	padding:2px;
	z-index:20001;
    height:auto;
	background:white;
	color:#555;
	font:bold 13px tahoma,arial,helvetica;
	padding:10px;
	margin:0;
    text-align:center;
    height:auto;
}

#loading img {
    margin-bottom:5px;
}
</style>
</head>
<body>
<div id="loading">
<img
	src="<?php echo image_asset_url('sys/page_red.png');?>"
	style="margin-right: 8px;" align="absmiddle" />
</div> 

</body>
</html>
