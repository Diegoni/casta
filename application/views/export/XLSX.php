<?php
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename={$name}");
header("Pragma: no-cache");
header("Expires: 0");

$obj = get_instance();

$obj->load->library('HtmlFile');
$obj->load->library('ExcelData');

$html = file_get_contents($file);
$tablevar = $this->lang->line('Hoja');
$user = $this->userauth->get_username();
$fout = $file . '.xlsx';
$company = $this->config->item('company.name');
$title = $this->lang->line('DATOS EXPORTADOS');
#var_dump($tablevar, $user, $fout, $title);
$res = $obj->exceldata->table2excel($html, $fout, $title, $user, $company, $tablevar, 'Excel2007');
readfile($fout);
die();