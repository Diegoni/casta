<?php
require_once(realpath(dirname(__FILE__).'/../../').'/config/config.inc.php');
require_once(dirname(__FILE__).'/tmbannerblock.php');

if (!$id = Tools::getValue('id'))
	die();

$module = new tmbannerblock();
echo $module->_getFormItem(intval($id), true);

?>