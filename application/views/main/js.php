<?php
$js_files = $this->config->item('js_files');
$dbg = $this->config->item('js.debug');
#if ($dbg) 
{
	foreach($js_files as $js_file)
	{
		$file = ($dbg)?(isset($js_file[1])?$js_file[1]:$js_file[0]):($js_file[0]. ((isset($js_file[1])?'':'.min')));
		echo js_asset($file .'.js');
	}
}
/*else
{
	echo js_asset('lib.js');
}*/
?>

<script
	type="text/javascript" src="<?php echo site_url('sys/app/lang');?>"></script>
<script
	type="text/javascript" src="<?php echo site_url('sys/app/routes');?>"></script>
<script
	type="text/javascript"
	src="<?php echo site_url('sys/app/constants');?>"></script>
<script
	type="text/javascript"
	src="<?php echo site_url('sys/app/js_status');?>"></script>
<script
	type="text/javascript" src="<?php echo site_url('sys/app/js_menu');?>"></script>
<script
	type="text/javascript" src="<?php echo site_url('sys/app/lib');?>"></script>
<script
	type="text/javascript" src="<?php echo site_url('sys/app/js_search');?>"></script>

<script type="text/javascript">
</script>
<?php
if (isset($js_include))
{
	foreach($js_include as $js_file)
	{
		if (isset($js_file[0]))	echo js_asset($js_file[0], isset($js_file[1])?$js_file[1]:null)."\n";
	}
}
?>
