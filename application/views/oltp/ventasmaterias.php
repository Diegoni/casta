<?php $this->load->library('HtmlFile'); ?>
<?php echo get_instance()->htmlfile->orientation(ORIENTATION_LANDSCAPE); ?>
<?php
if ( ! function_exists('_vs_cabecera'))
{
	function _vs_cabecera($c1, $view, $subtotal = FALSE)
	{
		$cab = "<table cellspacing=\"0\" cellpadding=\"3\"
		class=\"SummaryDataGrid\"
		style=\"BORDER-TOP-WIDTH: 0px; BORDER-LEFT-WIDTH: 0px; BORDER-BOTTOM-WIDTH: 0px; BORDER-COLLAPSE: collapse; BORDER-RIGHT-WIDTH: 0px\">
		<tr class=\"HeaderStyle\">
		<td class=HeaderStyle>{$c1}</td>
		<td class=HeaderStyle>{$view->lang->line('Ene.')}</td>
		<td class=HeaderStyle>{$view->lang->line('Feb.')}</td>
		<td class=HeaderStyle>{$view->lang->line('Mar.')}</td>
		<td class=HeaderStyle>{$view->lang->line('Abr.')}</td>
		<td class=HeaderStyle>{$view->lang->line('May.')}</td>
		<td class=HeaderStyle>{$view->lang->line('Jun.')}</td>
		<td class=HeaderStyle>{$view->lang->line('Jul.')}</td>
		<td class=HeaderStyle>{$view->lang->line('Ago.')}</td>
		<td class=HeaderStyle>{$view->lang->line('Sep.')}</td>
		<td class=HeaderStyle>{$view->lang->line('Oct.')}</td>
		<td class=HeaderStyle>{$view->lang->line('Nov.')}</td>
		<td class=HeaderStyle>{$view->lang->line('Dic.')}</td>";

		if ($subtotal)
			$cab .= "<td class=HeaderStyle>{$view->lang->line('Ac.')}</td>";

		$cab .= "<td class=HeaderStyle>{$view->lang->line('Total')}</td></tr>";
		return $cab;
	}
}

if ( ! function_exists('_vs_end'))
{
	function _vs_end()
	{
		return '</table>';
	}
}
if ( ! function_exists('_vs_linea'))
{
	function _vs_linea($title, $reg, $f, $mes = -1)
	{
		static $last1 = null, $last2 = null;
		$texto = '<tr><td class="SelectedBold">' . $title . '</td>';
		$total = 0;
		for($i=0; $i < 12; $i++)
		{
			if ($i < $mes) $total += $reg[$i];
			$texto .= '<td class="tablaimparright">'.@$f($reg[$i]) .'</td>';
		}
		if ($mes > 0)
		{
			if ($f == 'format_percent') 
			{
				$total = (isset($last1) && isset($last2))?((($last2-$last1) / $last1)*100):null;
			}
			else
			{
				$last1 = $last2;
				$last2 = $total;
			}
			$texto .= '<td class="SelectedFooterRight">'.@$f($total) .'</td>';	
		}
		$texto .= '<td class="SelectedFooterRight">'.@$f($reg[12]) .'</td></tr>';
		return $texto;
	}
}
$fecha_url = urlencode(str_replace('/', '-', $fecha));
#$fecha_url2 = urlencode(str_replace('/', '-', $fecha2));
?>

<div style='page-break-after: always;'><?php $url= site_url("oltp/oltp/ventas_materias/{$id}/{$fecha_url}");?>
<?php if ($link): ?><a href="javascript:parent.Ext.app.execCmd({url: '<?php echo $url;?>'});">
<?php endif; ?>
<h1><?php echo $materia; ?></h1>
<?php if ($link): ?></a><?php endif; ?>

<h2><?php echo $this->lang->line('Ventas'); ?></h2>
<?php echo _vs_cabecera('Año', $this, TRUE); ?> 
<?php foreach($ventas as $a => $venta): ?>
<?php echo _vs_linea($a , $venta, 'format_price', $mes); ?> 
<?php if (isset($comparacion[$a])): ?>
<?php echo _vs_linea('%' , $comparacion[$a], 'format_percent', $mes); ?> 
<?php endif; ?>
<?php endforeach; ?> 
<?php echo _vs_end(); ?>

</div>
