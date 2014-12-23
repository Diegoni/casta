<?php
/**
 * Bibliopola
 * 
 * Gestión de librerías
 * 
 * @package Bibliopola 5.0
 * @subpackage Views
 * @category Views
 * @author Alejandro López
 * @copyright Copyright (c) 2008-2009, ALIBRI
 * @link http://bibliopola.net
 * @since Version 5.0
 * @filesource
 */
?>

var login = function(result, url) {
	if (result)
	{
		window.location = url;
	}
};

Ext.app.formLogin(login, '<?php echo $url;?>');
