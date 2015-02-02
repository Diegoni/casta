<?php
header("Content-type: application/octet-stream; charset=UTF-8");
header("Content-Disposition: attachment; filename={$name}");
header("Pragma: no-cache");
header("Expires: 0");

//$c = file_get_contents($file);
//echo $c;
//echo utf8_decode($c);
readfile($file);
die();

