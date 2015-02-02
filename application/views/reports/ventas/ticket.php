<?php $length = isset($length)?$length:25;?>
<?php $length_bold = isset($length_bold)?$length_bold:16;?>
<?php $this->load->helper('asset');?>
<?php $pvp = isset($pvp)?$pvp:1;?>
<?php $datos_factura = isset($datos_factura)?$datos_factura:0;?>
<?php $precios = isset($precios)?$precios:1;?>
<pre>
<?php
if (!isset($nNumero)) echo $this->lang->line('report-#BORRADOR#') . "\n";

if ($length < 40)
{
	echo ($this->config->item('company.name.1')!='')?'<strong>' . str_pad($this->config->item('company.name.1'), $length_bold, ' ', STR_PAD_BOTH) . "</strong>\n":'';
	echo ($this->config->item('company.name.2')!='')?'<strong>' . str_pad($this->config->item('company.name.2'), $length_bold, ' ', STR_PAD_BOTH) . "</strong>\n":'';
	echo ($this->config->item('company.name.3')!='')?'<strong>' . str_pad($this->config->item('company.name.3'), $length_bold, ' ', STR_PAD_BOTH) . "</strong>\n":'';
	echo ($this->config->item('company.address.1')!='')?str_pad($this->config->item('company.address.1'), $length, ' ', STR_PAD_BOTH) . (($length>=40)?' / ':"\n"):'';
	echo ($this->config->item('company.address.2')!='')?str_pad($this->config->item('company.address.2'), $length, ' ', STR_PAD_BOTH) . "\n":'';
	echo ($this->config->item('company.address.3')!='')?str_pad($this->config->item('company.address.3'), $length, ' ', STR_PAD_BOTH) . "\n":'';
}
else
{
	$name = ($this->config->item('company.name.1')!='')?$this->config->item('company.name.1') . ' ':'';
	$name .= ($this->config->item('company.name.2')!='')?$this->config->item('company.name.2') . ' ':'';
	$name .= ($this->config->item('company.name.3')!='')?$this->config->item('company.name.3'):'';
	echo '<strong>' . str_pad(trim($name), $length_bold, ' ', STR_PAD_BOTH) . "</strong>\n";

	$name = ($this->config->item('company.address.1')!='')?$this->config->item('company.address.1') . ' ':'';
	$name .= ($this->config->item('company.address.2')!='')?$this->config->item('company.address.2') . ' ':'';
	$name .= ($this->config->item('company.address.3')!='')?$this->config->item('company.address.3'):'';
	echo str_pad(trim($name), $length, ' ', STR_PAD_BOTH) . "\n";
}
echo str_pad($this->config->item('company.vat'), $length, ' ', STR_PAD_BOTH) ."\n";
echo str_repeat('-', $length) ."\n";

if ($length < 40)
{
	$len = $length - 12;
	$len = (int) ($len / 2);
	printf("%-10s: %{$len}s\n", $this->lang->line('report-Factura'), isset($nNumero)?format_numerofactura($nNumero, $serie['nNumero']):$nIdFactura);
	printf("%-10s: %{$len}s\n", $this->lang->line('report-Fecha'), format_date($dFecha));
}
else
{
	$len = $length / 2;
	$id = sprintf('%s: %s', $this->lang->line('report-Factura'), isset($nNumero)?format_numerofactura($nNumero, $serie['nNumero']):$nIdFactura);
	$d = sprintf('%s: %s', $this->lang->line('report-Fecha'), format_date($dFecha));
	printf("%-{$len}s%{$len}s\n", $id, $d);
}
//printf("%-10s: %{$len}s\n", $this->lang->line('report-Cliente'), $nIdCliente);
echo str_repeat('-', $length) ."\n";

if ($datos_factura==1)
{
	$len = $length - 17;
	echo str_repeat('-', $length) ."\n";
	printf("|%-14s %{$len}s|\n", $this->lang->line('report-CIFNIF'). ':', '');
	echo '|' . str_repeat(' ', $length-2) ."|\n";
	printf("|%-14s %{$len}s|\n", $this->lang->line('report-Nombre') .':', '');
	echo '|' . str_repeat(' ', $length-2) ."|\n";
	$a = $this->lang->line('report-Dirección')  .':' . str_repeat(' ', 13 - mb_strlen($this->lang->line('report-Dirección'), 'utf8'));
	echo sprintf("|%s %{$len}s|\n", $a, '');
	echo '|' . str_repeat(' ', $length-2) ."|\n";
	echo str_repeat('-', $length) ."\n\n";
}

$total = 0;
$ivas = array();
$bases = array();
$totales = array();
$actual = 0;
$len = $length - 15;

foreach($lineas as $linea)
{
	if ($precios)
	{
		echo format_title(trim($linea['cTitulo']), $length-2) ."\n";
		if ($length < 40)
		{
			printf("  %-3d x %10s\n%{$len}s = [%10s]\n", $linea['nCantidad'], format_price($linea['fPVP'], FALSE),
				(($linea['fDescuento']>0)?'(-' .format_percent($linea['fDescuento']). ')':'') , format_price($linea['fTotal'], FALSE));
		}
		else
		{
			$len2 = $length - 34;
			printf("  %-3d x %10s %{$len2}s = [%10s]\n", 
				$linea['nCantidad'], 
				format_price($linea['fPVP'], FALSE),
				(($linea['fDescuento']>0)?'(-' .format_percent($linea['fDescuento']). ')':' '), 
				format_price($linea['fTotal'], FALSE));
		}
	}
	else
	{
		echo sprintf(" %-3d ", $linea['nCantidad']) .  format_title(trim($linea['cTitulo']), $length - 6) ."\n";
	}

	$total += $linea['fTotal'];
	$ivas[$linea['fIVA']] = (isset($ivas[$linea['fIVA']])?$ivas[$linea['fIVA']]:0) + $linea['fIVAImporte'];
	$bases[$linea['fIVA']] = (isset($bases[$linea['fIVA']])?$bases[$linea['fIVA']]:0) + $linea['fBase'];
	$totales[$linea['fIVA']] = (isset($totales[$linea['fIVA']])?$totales[$linea['fIVA']]:0) + $linea['fTotal'];
}
if ($precios)
{
	echo str_repeat('-', $length) ."\n";
	$iva = 0;
	$base = 0;
	$total = 0;
	$len = $length - 18;
	printf("%-10s %-6s %{$len}s\n", $this->lang->line('report-Base'), $this->lang->line('report-IVA'), $this->lang->line('report-Importe'));
	foreach($ivas as $k => $v)
	{
		$i = format_iva((($pvp)?format_quitar_iva($totales[$k], $k):$bases[$k]), $k);
		$b = ($pvp)?format_quitar_iva($totales[$k], $k):$bases[$k];
		printf("%-10s %-6s %{$len}s\n", format_price($b, FALSE), format_number($k), format_price($i, FALSE));
		$iva += $v;
		$base += $bases[$k];
		$total += $i + $b;
	}
	echo str_repeat('-', $length) ."\n";
	$t = str_pad(format_price($total), $length_bold, ' ', STR_PAD_BOTH);
	//$t = format_price($total);
	printf("%-10s:\n<strong>%s</strong>\n", $this->lang->line('report-Total'), $t);
}

if(isset($tNotasExternas) && (trim($tNotasExternas)!=''))
{
	echo $this->lang->line('report-Comentarios') . "\n";
	echo str_repeat('-', $length) ."\n";
	echo $tNotasExternas . "\n";
}

$condiciones = $this->lang->line('text-ticket');
if (isset($condiciones) && $condiciones != '')
{
	echo str_repeat('-', $length) ."\n";
	echo wordwrap($this->lang->line('text-ticket'), $length, "\n") ."\n";
}
echo str_repeat('-', $length) ."\n";
if ($length < 40)
{
	echo str_pad($this->lang->line('report-Tel.') . ': ' . $this->config->item('company.telephone'), $length, ' ', STR_PAD_BOTH) ."\n";
	echo str_pad($this->lang->line('report-Fax.') . ': ' . $this->config->item('company.fax'), $length, ' ', STR_PAD_BOTH) ."\n";
	echo str_pad($this->lang->line('report-eMail') . ': ' . $this->config->item('company.email'), $length, ' ', STR_PAD_BOTH) ."\n";
	echo str_pad($this->lang->line('report-Web') . ': ' . $this->config->item('company.url'), $length, ' ', STR_PAD_BOTH) ."\n";
}
else
{
	$name = $this->lang->line('report-Tel.') . ': ' . $this->config->item('company.telephone');
	 /* ' / ' . $this->lang->line('report-Fax.') . ': ' . $this->config->item('company.fax');*/
	echo str_pad($name, $length, ' ', STR_PAD_BOTH) ."\n";

	$name = $this->config->item('company.email') .
		' / ' . $this->config->item('company.url');
	echo str_pad($name, $length, ' ', STR_PAD_BOTH) ."\n";
}

?>
</pre>
