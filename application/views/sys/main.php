<?php
/**
 * Bibliopola
 *
 * Gestión de librerías
 *
 * @package		Bibliopola 5.0
 * @subpackage	Views
 * @category	Views
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
<title><?php echo $this->config->item('bp.application.name');?></title>
<?php if (!isset($css_include)) $css_include = null; ?>
<?php $this->load->view('main/css', array('css_include' => $css_include));?>
<?php if ($this->config->item('js.debug.firebug')):?>
<script type="text/javascript"
	src="https://getfirebug.com/firebug-lite.js"></script>
<?php endif; ?>
</head>
<body>
<!-- 
<div id="loading-mask" style=""></div>
<div id="loading">
<div class="loading-indicator"><img
	src="<?php echo image_asset_url('snake_transparent.gif','main');?>"
	style="margin-right: 8px;" align="absmiddle" /> <?php echo $this->lang->line('Cargando'); ?>
</div>
</div> -->

<!-- <div id="d_clip_container" style="position: relative">
		<div id="d_clip_button" class="my_clip_button">&nbsp;</div></div>
	  -->
<!-- <div id="d_clip_container" style="position: relative">
<div id="d_clip_button"></div>
</div>
 -->

<!-- include everything after the loading indicator -->
<?php if (!isset($js_include)) $js_include = null; ?>
<?php $this->load->view('main/js', array('js_include' => $js_include));?>
<script language="javascript">
// Constructor de la interfaz
Ext.onReady(function() {

	Ext.app.runApp();
	//Ext.app.initClipboard();
	var l = Ext.get('loading');
	
	if (l != null) {
			l.remove();
	}
	
	l = Ext.get('loading-mask')
	
	if (l != null) {
		l.fadeOut({
				remove : true
		});
	}
});
</script>
<audio id="audio1" style="visibility:hidden;" src="<?php echo image_asset_url('beeperror.wav')?>" controls preload="auto" autobuffer>
</audio>
<audio id="audio2" style="visibility:hidden;" src="<?php echo image_asset_url('Cuen2.wav')?>" controls preload="auto" autobuffer>
</audio>
<audio id="audio3" style="visibility:hidden;" src="<?php echo image_asset_url('beep-02.mp3')?>" controls preload="auto" autobuffer>
</audio>
<audio id="audio4" style="visibility:hidden;" src="<?php echo image_asset_url('beep-4.wav')?>" controls preload="auto" autobuffer>
</audio>
<audio id="audio5" style="visibility:hidden;" src="<?php echo image_asset_url('Cuen2.wav')?>" controls preload="auto" autobuffer>
</audio>
<audio id="audio6" style="visibility:hidden;" src="<?php echo image_asset_url('censor-beep-01.mp3')?>" controls preload="auto" autobuffer>
</audio>

</body>
</html>
