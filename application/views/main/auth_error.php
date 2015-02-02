<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Bibliopola
 *
 * Error de autentifación
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
body, td, tr {
	background-color: #fff;
	margin: 40px;
	font-family: Lucida Grande, Verdana, Sans-serif;
	font-size: 12px;
	color: #000;
}

#content {
	border: #999 1px solid;
	background-color: #fff;
	padding: 20px 20px 12px 20px;
}

h1 {
	font-weight: normal;
	font-size: 14px;
	color: #990000;
	margin: 0 0 4px 0;
}
</style>
</head>
<body>
<div id="content">
<table>
	<tr>
		<td valign="middle"><img
			src="<?php echo image_asset_url('Lock.png'); ?>" /></td>
		<td valign="middle">
		<h1><?php echo $this->lang->line('auth_error'); ?></h1>
		<?php echo $message; ?></td>
	</tr>
</table>

</div>

</body>
</html>
