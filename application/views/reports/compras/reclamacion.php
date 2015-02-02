<?php
$titulo = $this->lang->line('report-Reclamación Pedido Proveedor');
$nIdDocumento = $nIdReclamacion;
$texto_condiciones = $this->lang->line('text-reclamacion-pedidoproveedor');
$texto_email = $this->lang->line('text-reclamacion-pedidoproveedor-email');

require(APPPATH . 'views' . DIRECTORY_SEPARATOR . 'reports' . DIRECTORY_SEPARATOR . 'compras' . DIRECTORY_SEPARATOR . 'cancel_reclam.php');
