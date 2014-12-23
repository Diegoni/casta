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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="es">
<head>
    <link rel="shortcut icon"
    href="<?php echo image_asset_url($this->config->item('bp.application.icon')); ?>" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $this->config->item('bp.application.name');?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Gestión de librerías">
    <meta name="author" content="Alejandro López">

    <!-- Le styles -->
<?php echo css_asset('bootstrap.min.css', 'beta'); ?>
<?php echo css_asset('bootstrap-responsive.min.css', 'beta'); ?>
<?php echo css_asset('unicorn.main.css', 'beta'); ?>
<?php #echo css_asset('uniform.css', 'beta'); ?>
<?php #echo css_asset('fullcalendar.css', 'beta'); ?>
<?php echo css_asset('select2.css', 'beta'); ?>
<?php echo css_asset('unicorn.grey.css', 'beta'); ?>
    <!--<link rel="stylesheet" href="src/skin/unicorn/css/unicorn.grey.css" class="skin-color" />-->

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
<!--        <div id="style-switcher">
            <i class="icon-arrow-left icon-white"></i>
            <span>Style:</span>
            <a href="#grey" style="background-color: #555555;border-color: #aaaaaa;"></a>
            <a href="#blue" style="background-color: #2D2F57;"></a>
            <a href="#red" style="background-color: #673232;"></a>
        </div>
-->
<div id="content"></div>

<!-- Le javascript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->

<?php echo js_asset('lib/jquery.min.js', 'beta'); ?>
<?php echo js_asset('lib/jquery.ui.custom.js', 'beta'); ?>
<?php echo js_asset('lib/bootstrap.min.js', 'beta'); ?>

<?php #echo js_asset('lib/excanvas.js', 'beta'); ?>
<?php #echo js_asset('lib/jquery.flot.min.js', 'beta'); ?>
<?php #echo js_asset('lib/jquery.flot.resize.min.js', 'beta'); ?>
<?php #echo js_asset('lib/jquery.peity.min.js', 'beta'); ?>
<?php #echo js_asset('lib/fullcalendar.js', 'beta'); ?>
<?php #echo js_asset('lib/unicorn.dashboard.js', 'beta'); ?>
<?php echo js_asset('lib/jquery.validate.js', 'beta'); ?>
<?php echo js_asset('lib/jquery.uniform.js', 'beta'); ?>
<?php echo js_asset('lib/select2.min.js', 'beta'); ?>
<?php echo js_asset('lib/jquery.dataTables.min.js', 'beta'); ?>
<?php #echo js_asset('lib/unicorn.tables.min.js', 'beta'); ?>

<?php echo js_asset('lib/underscore-min.js', 'beta'); ?>
<?php echo js_asset('lib/backbone-min.js', 'beta'); ?>

<?php echo js_asset('models/models.js', 'beta'); ?>
<?php echo js_asset('views/sidebar.js', 'beta'); ?>
<?php echo js_asset('views/header.js', 'beta'); ?>
<?php echo js_asset('views/home.js', 'beta'); ?>
<?php echo js_asset('views/catalogo/buscar.js', 'beta'); ?>

<?php echo js_asset('functions.js'); ?>

<?php echo js_asset('main.js', 'beta'); ?>
<?php echo js_asset('utils.js', 'beta'); ?>

<script type="text/javascript" src="<?php echo site_url('sys/app/lang');?>"></script>
<script type="text/javascript" src="<?php echo site_url('sys/app/routes');?>"></script>
<script type="text/javascript" src="<?php echo site_url('sys/app/constants');?>"></script>
<script type="text/javascript" src="<?php echo site_url('sys/app/js_status');?>"></script>

</body>
</html>
