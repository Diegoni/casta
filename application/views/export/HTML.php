<?php
header("Content-type: text/html; charset=UTF-8");
header("Content-Disposition: attachment; filename={$name}");
//header("Pragma: no-cache");
//header("Expires: 0");
readfile($file);
die();

