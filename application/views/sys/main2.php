<?php
/**
 * Bibliopola
 *
 * Estructura básica para mostrar un formulario como una aplicación independiente
 *
 * @package     Bibliopola 6.0
 * @subpackage  Views
 * @category    app
 * @author      Alejandro López
 * @copyright   Copyright (c) 2008-2009, ALIBRI
 * @link        http://bibliopola.net
 * @since       Version 5.0
 * @filesource
 */
?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
  
<head>
    <link rel="shortcut icon" href="<?php echo image_asset_url($this->config->item('bp.application.icon.beta')); ?>" />
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php echo $this->config->item('bp.application.name');?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Gestión de librerías">
    <meta name="author" content="Alejandro López">

    <!-- Le styles -->
<?php echo css_asset('bootstrap.min.css', 'beta2'); ?>
<?php echo css_asset('bootstrap-responsive.min.css', 'beta2'); ?>

<?php echo css_asset('main.css', 'beta2'); ?>
<?php echo css_asset('style.css', 'beta2'); ?>
<?php echo css_asset('DT_bootstrap.css', 'beta2'); ?>
<?php echo css_asset('style2.css', 'beta2'); ?>
<?php echo css_asset('bootstrap-responsive.min.css', 'beta2'); ?>
<?php echo css_asset('bootstrap-responsive.min.css', 'beta2'); ?>
<?php echo css_asset('bootstrap-responsive.min.css', 'beta2'); ?>
<?php echo css_asset('showLoading.css', 'beta2'); ?>
<?php echo js_asset('vendor/modernizr-2.6.2-respond-1.1.0.min.js', 'beta2'); ?>

<style>
body {
    padding-top: 60px;
    padding-bottom: 40px;
}
.nav-tabs > li .close {
    margin: -2px 0 0 10px;
    font-size: 18px;
}
.marginBottom {
    margin-bottom :1px !important;
}
.operationDiv {
    padding:5px 10px 5px 5px;
}
.operationDivWrapper {
    margin-top:-1px;
}
.leftMenu {
    height :70%;
    background-color: #E6E6E6;
    border-right: 2px solid #BFBFBF;
}
.container-fluid > .content{
    margin-left:280px;
}
.categories li {

}
.categories li:focus{

}
#main-items tbody tr td{
    cursor:pointer !important;
}
.highlight td{
    font-weight:bold;
}
td.mini-thumbnail img{
    max-height:50px;
    max-width:100px;
}
td.mini-thumbnail{
    text-align:center;
}            
</style>

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <script>
    </script>

</head>

<body>

<div id="app_header"></div>
<div id="app_sidebar"></div>
<div id="content"></div>

<!-- Le javascript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->

<?php echo js_asset('lib/jquery.min.js', 'beta2'); ?>
<?php echo js_asset('lib/jquery.ui.custom.js', 'beta2'); ?>
<?php echo js_asset('lib/bootstrap.min.js', 'beta2'); ?>

<?php echo js_asset('plugins.js', 'beta2'); ?>

<?php echo js_asset('jquery.dataTables.js', 'beta2'); ?>
<?php echo js_asset('DT_bootstrap.js', 'beta2'); ?>
<?php echo js_asset('jquery.jeditable.js', 'beta2'); ?>
<?php echo js_asset('jquery.showLoading.min.js', 'beta2'); ?>
<?php echo js_asset('ckeditor/ckeditor.js', 'beta2'); ?>
<?php echo js_asset('ckeditor/adapters/jquery.js', 'beta2'); ?>
<?php echo js_asset('datatables/jquery.dataTables.min.js', 'beta2'); ?>
<?php echo js_asset('datatables/jquery.dataTables.sorting.js', 'beta2'); ?>

<?php #echo js_asset('lib/underscore-min.js', 'beta2'); ?>
<?php #echo js_asset('lib/backbone-min.js', 'beta2'); ?>
<?php #echo js_asset('vendor/mustache/0.5.0-dev/js/mustache.js', 'beta2'); ?>
<?php echo js_asset('vendor/underscore/1.1.6/js/underscore.js', 'beta2'); ?>
<?php echo js_asset('vendor/backbone/0.5.1/js/backbone.js', 'beta2'); ?>
<?php echo js_asset('vendor/mustache/0.5.0-dev/js/mustache.js', 'beta2'); ?>

<?php echo js_asset('functions.js'); ?>

<script type="text/javascript" src="<?php echo site_url('sys/app/lang');?>"></script>
<script type="text/javascript" src="<?php echo site_url('sys/app/routes');?>"></script>
<script type="text/javascript" src="<?php echo site_url('sys/app/constants');?>"></script>
<script type="text/javascript" src="<?php echo site_url('sys/app/js_status');?>"></script>

<?php echo js_asset('models/models.js', 'beta2'); ?>
<?php echo js_asset('views/sidebar.js', 'beta2'); ?>
<?php echo js_asset('views/header.js', 'beta2'); ?>
<?php echo js_asset('views/home.js', 'beta2'); ?>
<?php echo js_asset('views/catalogo.js', 'beta2'); ?>

<?php echo js_asset('utils.js', 'beta2'); ?>
<?php echo js_asset('main.js', 'beta2'); ?>

</body>
</html>
