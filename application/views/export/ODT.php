<?php
$obj = get_instance();
$obj->load->library('Convert');

$obj->convert->odt($file);

die();
