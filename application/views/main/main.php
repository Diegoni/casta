<?php
/**
 * Bibliopola
 *
 * Estructura básica para mostrar un formulario como una aplicación independiente
 *
 * @package		Bibliopola 5.0
 * @subpackage	Views
 * @category	app
 * @author		Alejandro López
 * @copyright	Copyright (c) 2008-2009, ALIBRI
 * @link		http://bibliopola.net
 * @since		Version 5.0
 * @filesource
 */

//@todo Implementar un sistema de caché y que los elementos JS se generen desde PHP
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="es">
<head>
<link rel="shortcut icon"
	href="<?php echo image_asset_url($this->config->item('bp.application.icon')); ?>" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $title; ?></title>
<?php if (!isset($css_include)) $css_include = null; ?>
<?php $this->load->view('main/css', array('css_include' => $css_include));?>
<?php if ($this->config->item('js.debug.firebug')):?>
<script type="text/javascript"
	src="https://getfirebug.com/firebug-lite.js"></script>
<?php endif; ?>

<?php if (!isset($js_include)) $js_include = null; ?>
<?php $this->load->view('main/js', array('js_include' => $js_include));?>
<script language="javascript">
Ext.onReady(function() {

	Ext.app.initApp();
	//Ext.app.auth_reload(true);
	try {
		<?php if (isset($script)): ?>
	<?php echo $script;?>
	<?php endif; ?>
	}
	catch (e)
	{
		console.dir(e);
	}
});
</script>
</head>
<body>
	<?php if (isset($body)) echo $body;?>
</body>
</html>
