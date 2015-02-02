<?php 
$titulo = isset($titulo)?$this->lang->line('report-' . $titulo):$this->lang->line('report-Factura Proforma');
$borrador = FALSE;
$nIdDocumento = $nIdPedido;
$texto_condiciones = isset($texto_condiciones)?$this->lang->line($texto_condiciones):$this->lang->line('text-facturaproforma');
$texto_condiciones = str_replace('%cd%', $this->config->item('bp.presupuesto.caducidad'), $texto_condiciones);

require(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'base.php'); 
