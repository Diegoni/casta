<?php 
$css_files = $this->config->item('css_files');
$dbg = $this->config->item('css.debug');
#if ($dbg) 
{
	foreach($css_files as $css_file)
	{
		$file = $css_file[0];
		//$time = filemtime(css_asset_path($file . '.css', (isset($css_file[1])?$css_file[1]:null)));
		echo css_asset($file .'.css'/*.$time*/, (isset($css_file[1])?$css_file[1]:null));
	}
}
/*else
{
	echo css_asset('styles.css');
}*/
$style = $this->config->item('bp.application.style');
if (isset($style) && ($style!='')) echo css_asset($style, 'ext');
?>
<?php
if (isset($css_include))
{
	foreach ($css_include as $f)
	{
		if (isset($f[0])) echo css_asset($f[0], isset($f[1])?$f[1]:null)."\n";
		echo css_asset($f[0], $f[1]);
	}
}
?>
