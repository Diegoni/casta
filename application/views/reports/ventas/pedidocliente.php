<?php
$estado = isset($estado)?$estado:0;
$titulo = ($nIdEstado==3)?$this->lang->line('report-presupuesto'):$this->lang->line('report-Pedido de Cliente');
$borrador = FALSE;
$nIdDocumento = $nIdPedido;
$texto_condiciones = ($nIdEstado==3)?str_replace('%cd%', $this->config->item('bp.presupuesto.caducidad'), $this->lang->line('text-presupuesto')):$this->lang->line('text-pedidocliente');

if ($estado)
{
	foreach ($lineas as $k => $linea)
	{
		$lineas[$k]['cExtra'] = $this->lang->line($linea['cEstado']);
		if ($linea['nIdEstado'] == 5) $lineas[$k]['cExtra'] .= ' / ' . $this->lang->line($linea['cEstadoLibro']);
	}
}

require(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'base.php');
