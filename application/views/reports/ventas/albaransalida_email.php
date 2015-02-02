<?php
$titulo = $this->lang->line('report-Albarán de Salida');
if ($bExamen) $titulo.= ' - ' . $this->lang->line('report-Examen');
$borrador = ($nIdEstado == 1);
$nIdDocumento = $nIdAlbaran;
$texto_condiciones = $this->lang->line('text-albaransalida');

$texto_email = $this->lang->line('text-albaransalida-email');

require('email.php');
