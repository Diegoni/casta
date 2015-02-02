<?php
$lang = $this->config->item('reports.language');
$lang = preg_split('/;/', $lang);
$lang = $lang[0];
$this->load->language("report.{$lang}");

$nIdDocumento = $nIdCancelacion;
$titulo = $this->lang->line('report-Cancelación Pedido Proveedor');
$texto_condiciones = $this->lang->line('text-cancelacion-pedidoproveedor');
$texto_email = $this->lang->line('text-cancelacion-pedidoproveedor-email');

require(APPPATH . 'views' . DIRECTORY_SEPARATOR . 'compras' . DIRECTORY_SEPARATOR . 'cancel_reclam.php');
