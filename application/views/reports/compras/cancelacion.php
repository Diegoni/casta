<?php
$nIdDocumento = $nIdCancelacion;
$titulo = $this->lang->line('report-Cancelación Pedido Proveedor');
$texto_condiciones = $this->lang->line('text-cancelacion-pedidoproveedor');
$texto_email = $this->lang->line('text-cancelacion-pedidoproveedor-email');

require(APPPATH . 'views' . DIRECTORY_SEPARATOR . 'reports' . DIRECTORY_SEPARATOR . 'compras' . DIRECTORY_SEPARATOR . 'cancel_reclam.php');

