<?php

#$this->load->plugin('wkhtmltopdf');

#pdf_from_file($file, null, null, null, $attached = FALSE);
#die();

$obj = get_instance();
$obj->load->library('PdfLib');

$obj->pdflib->create($file, null, null, null, TRUE, FALSE);

#$this->PdfLib($file, null, null, null, $attached = FALSE);

die();