<?php
$obj = get_instance();
$obj->load->library('Convert');

$obj->convert->rtf($file);

die();
