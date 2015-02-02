<?php $this->load->helper('asset'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="es">
<head>
<link rel="shortcut icon"
	href="<?php echo image_asset_url($this->config->item('bp.application.icon')); ?>" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $title; ?></title>
<?php echo css_asset('informes.css', 'main');?>
<?php echo js_asset('jQuery/jquery.min.js');?>

</head>

<body bgcolor="#ffffff">
<?php echo $body; ?>
</body>
</html>
