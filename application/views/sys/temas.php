<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Ext JS Themes Example</title>

<?php $this->load->helper('asset'); ?>
<!-- Theme includes -->
<?php $version = $this->config->item('bp.application.extjs');?>
<?php echo css_asset('ext-all.css', $version);?>
<?php
$style = $this->config->item('bp.application.style');
if (isset($style) && ($style!='')) echo css_asset($style, $version);
?>

<link rel="stylesheet" type="text/css" title="blue"
	href="<?php echo css_asset_url('xtheme-blue.css', $version);?>" />
<link rel="stylesheet" type="text/css" title="gray"
	href="<?php echo css_asset_url('xtheme-gray.css', $version);?>" />
<link rel="stylesheet" type="text/css" title="access"
	href="<?php echo css_asset_url('xtheme-access.css', $version);?>" />
<link rel="stylesheet" type="text/css" title="yourtheme"
	href="<?php echo css_asset_url('yourtheme.css', $version);?>" />
<link rel="stylesheet" type="text/css" title="blueen"
	href="<?php echo css_asset_url('xtheme-blueen.css', $version);?>" />
<link rel="stylesheet" type="text/css" title="gray-extend"
	href="<?php echo css_asset_url('xtheme-gray-extend.css', $version);?>" />
<link rel="stylesheet" type="text/css" title="newgentheme"
	href="<?php echo css_asset_url('xtheme-newgentheme.css', $version);?>" />
<link rel="stylesheet" type="text/css" title="slate"
	href="<?php echo css_asset_url('xtheme-slate.css', $version);?>" />
<link rel="stylesheet" type="text/css" title="slickness"
	href="<?php echo css_asset_url('xtheme-slickness.css', $version);?>" />
<link rel="stylesheet" type="text/css" title="tp"
	href="<?php echo css_asset_url('xtheme-tp.css', $version);?>" />
<!-- 
        By default, yourtheme.css is an exact copy of x-themeblue.css. 
        Customize your theme by editing yourtheme.css and customize your 
        own images in the images/yourtheme/ directory. 
    -->

<?php echo js_asset("{$version}/adapter/ext/" . ($this->config->item('js.debug')?'ext-base-debug.js':'ext-base.js'));?>
<?php echo js_asset("{$version}/" . ($this->config->item('js.debug')?'ext-all-debug.js':'ext-all.js'));?>
<?php echo js_asset('themes.'.(!$this->config->item('js.debug')?'min.':'') .'js', 'app');?>
<?php echo js_asset('styleswitcher.'.(!$this->config->item('js.debug')?'min.':'') .'js', 'app');?>

<style type="text/css">
body {
	font-family: 'Helvetica Neue', tahoma, helvetica, sans-serif;
	font-size: 12px;
}

h1 a:link,h1 a:visited {
	color: #046BCA;
}

h1 a:hover,h1 a:focus,h1 a:active {
	color: #1C417C;
}

div#header {
	height: 65px;
	width: 1090px;
	padding: 25px 0 10px 0;
	margin: 0 50px;
}

div#header h1 {
	font-family: MyriadPro-Semibold, 'Myriad Pro Semibold', 'Myriad Pro',
		'Trebuchet MS', Tahoma, arial, sans-serif;
	font-size: 250%;
}

.x-viewport body {
	overflow: auto;
}

form#styleswitcher {
	background-color: #f3f3f3;
	background-color: rgba(243, 243, 243, .333);
	border: 1px solid #ddd;
	border-color: rgba(221, 221, 221, .333);
	border-radius: 8px;
	-moz-border-radius: 8px;
	-ms-border-radius: 8px;
	-o-border-radius: 8px;
	-webkit-border-radius: 8px;
	float: left;
	padding: 8px 10px;
	width: auto;
}

form#styleswitcher select {
	font-size: 16px;
	line-height: 16px;
}

div#header h1 span {
	color: inherit;
	font-family: 'Helvetica Neue', tahoma, helvetica, verdana, sans-serif;
	font-size: 12px;
	font-weight: normal;
	line-height: 16px;
	padding-left: 25px;
}
</style>
</head>

<body>
<div id="header">
<form id="styleswitcher"><label for="styleswitcher_select">Choose Theme:
</label> <select name="styleswitcher_select" id="styleswitcher_select">
	<option value="blue" selected="true">Blue Theme</option>
	<option value="gray">Gray Theme</option>
	<option value="access">Accessibility Theme</option>
	<option value="yourtheme">Your Theme</option>
	<option value="blueen">Blueen</option>
	<option value="gray-extend">Gray Extend</option>
	<option value="newgentheme">New Gen Theme</option>
	<option value="slate">Slate</option>
	<option value="slickness">Slickness</option>
	<option value="tp">TP</option>
</select></form>
</div>
</body>
</html>
