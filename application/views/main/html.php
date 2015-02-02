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
 * @version		$Rev: 435 $
 * @filesource
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title><?php echo $title;?></title>
<?php if (isset($css) && ($css !== FALSE))
{
	if (is_array($css))
	{
		foreach($css as $cs)
		{
			if (is_array($cs))
			{
			echo css_asset($cs[0], $cs[1]);
			}
			else
			{
				echo css_asset($cs);
			}
		}
	}
	else
	{
		echo css_asset($css);
	}
}?>
<?php echo js_asset('tablesort.'.(!$this->config->item('js.debug')?'min.':'') .'js');?>
</head>
<body>

<div class="report"><?php echo $html;?></div>
</body>
</html>
