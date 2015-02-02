<?php 
$titulo = $this->lang->line('report-Albarán de Salida');
if ($bExamen) $titulo.= ' - ' . $this->lang->line('report-Examen');
$borrador = ($nIdEstado == 1);
$nIdDocumento = $nIdAlbaran;
$texto_condiciones = $this->lang->line('text-albaransalida');

# Concurso
if (!empty($cBiblioteca))
	$extra_name = ' <span class="reverse">' . $cBiblioteca . ' - ' . $cSala . '</span><br/>';

# Suscripciones
$ci = get_instance();
$ci->load->model('suscripciones/m_suscripcion');
$ci->load->model('ventas/m_albaransalida');
$sus[] = array();
$hay = FALSE;
$sus2 = $ci->m_albaransalida->get_suscripciones($nIdAlbaran);
if (count($sus) > 0)
{
	foreach ($lineas as $k => $linea)
	{	
		#Comprobación de suscripciones
		foreach ($sus2 as $k2 => $reg)
		{
			if (!isset($sus[$reg['nIdSuscripcion']]))
			{
				$sus[$reg['nIdSuscripcion']] = $ci->m_suscripcion->load($reg['nIdSuscripcion'], TRUE);
			}
			#var_dump($sus[$reg['nIdSuscripcion']]['nIdRevista'], $linea['nIdLibro']);

			if ($sus[$reg['nIdSuscripcion']]['nIdRevista'] == $linea['nIdLibro'])
			{
				$suscripcion = $sus[$reg['nIdSuscripcion']];
				if ($suscripcion['nIdTipoEnvio'] == 1)
				{
					$direccionenvio = format_address_print($suscripcion['direccionenvio']);
					if (empty($suscripcion['direccionenvio']['cTitular']))
					$direccionenvio = format_name($suscripcion['cliente']['cNombre'], $suscripcion['cliente']['cApellido'], $suscripcion['cEmpresa']) . 
					'<br/>' . $direccionenvio;
				}
				else
				{
					$direccionenvio = $this->config->item('company.name') . '<br />' . (($this->config->item('company.address.1') != '') ? $this->config->item('company.address.1') . '<br/>' : '') . (($this->config->item('company.address.2') != '') ? $this->config->item('company.address.2') . '<br/>' : '') . (($this->config->item('company.address.3') != '') ? $this->config->item('company.address.3') : '');
				}
		
				$extra = '<strong>' . $this->lang->line('report-Número Suscripción') . '</strong>: ' . $suscripcion['nIdSuscripcion'] . '<br/>';
				if (isset($linea['cRefInterna']) && trim($linea['cRefInterna']) != '')
					$extra .='<strong>' . $this->lang->line('report-Año/Vol') . '</strong>: ' . $linea['cRefInterna'] . '<br/>';
				$extra .= '<strong>' . $this->lang->line('report-Dirección Envío') . '</strong>: ' . $direccionenvio;
				$hay = TRUE;
				$lineas[$k]['cExtra2']  = $extra;
				#La quita por si hay más suscripciones del mismo artículo
				unset($sus2[$k2]);
				break;
			}
		}
	}	
}
#die();
if ($hay)
{
	require(APPPATH . 'views' . DIRECTORY_SEPARATOR . 'reports' . DIRECTORY_SEPARATOR . 'params.php');
	$num_lineas_1 = (int) ($num_lineas_1  / 3);
	$num_lineas_2 = (int) ($num_lineas_2 / 3);
}

require(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'base.php'); 
