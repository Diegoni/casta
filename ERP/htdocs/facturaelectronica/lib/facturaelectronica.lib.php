<?php
function prepare_head(){
	global $langs, $conf;

	$h = 0;
	$head = array();
	
	$head[$h][0] = DOL_URL_ROOT."/facturaelectronica/admin/generar.php";
	$head[$h][1] = 'Generar';
	$head[$h][2] = 'generar';
	$h++;
	
	$head[$h][0] = DOL_URL_ROOT."/facturaelectronica/admin/config.php";
	$head[$h][1] = 'Config';
	$head[$h][2] = 'config';
	$h++;


	$object = new stdClass();

 	complete_head_from_modules($conf,$langs,$object,$head,$h,'paypaladmin');
	complete_head_from_modules($conf,$langs,$object,$head,$h,'paypaladmin','remove');

    return $head;
}