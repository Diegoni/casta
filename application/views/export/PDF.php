<?php
$obj = get_instance();
$obj->load->library('PdfLib');

$obj->pdflib->create($file);

die();

