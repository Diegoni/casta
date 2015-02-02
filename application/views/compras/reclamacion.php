<?php
$lang = $this->config->item('reports.language');
$lang = preg_split('/;/', $lang);
$lang = $lang[0];
$this->load->language("report.{$lang}");

$titulo = $this->lang->line('Reclamación pedido proveedor');
$nIdDocumento = $nIdReclamacion;
$texto_condiciones = $this->lang->line('text-reclamacion-pedidoproveedor');
$texto_email = $this->lang->line('text-reclamacion-pedidoproveedor-email');

require(APPPATH . 'views' . DIRECTORY_SEPARATOR . 'compras' . DIRECTORY_SEPARATOR . 'cancel_reclam.php');
