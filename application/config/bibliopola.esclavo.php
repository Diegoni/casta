<?php
error_reporting(E_ERROR);

$config['bp.import.database.allow'] = TRUE;
$config['bp.export.database.allow'] = TRUE;
$config['bp.ventas.procesar.allow'] = FALSE;
$config['bp.import.remoto.allow'] = FALSE;

$config['bp.import.remote.server'] = 'http://app.alibri.es/index.php';
$config['bp.import.remote.username'] = 'export';
$config['bp.import.remote.password'] = 'ca7feb341e7a3bac2bc2839efaa3c952b59f2d23';

$config['bp.application.title'] 	= 'Bibliopola-LOCAL';
$config['bp.application.name'] 		= $config['bp.application.title'] . ' ' . $config['bp.application.version'];
$config['bp.application.icon']		= 'bibliopola-green.ico';

$config['bp.application.style'] 	= 'xtheme-blue.css';
