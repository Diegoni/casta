<div id="footer"><?php if ($totales):?> <?php
$iva = 0;
$base = 0;
$total = 0;
foreach($ivas as $k => $v)
{
	$i = format_iva((($pvp)?format_quitar_iva($totales[$k], $k):$bases[$k]), $k);
	$b = ($pvp)?format_quitar_iva($totales[$k], $k):$bases[$k];
	$iva += $i;
	$base += $b;
	$total += $i + $b;
}
if (isset($iva_add_value))
{
	$base -= $iva_add_value;
	$iva += $iva_add_value;
}
?>
<table id="totals">
<?php if ($show_ejemplares):?>
	<tr>
		<td class="total-line"><?php echo $this->lang->line('report-Ejemplares');?></td>
		<td class="total-value"><?php echo format_number($ejemplares);?></td>
	</tr>
	<?php endif; ?>
	<?php if ($show_titulos):?>
	<tr>
		<td class="total-line"><?php echo $this->lang->line('report-Títulos');?></td>
		<td class="total-value"><?php echo format_number($titulos);?></td>
	</tr>
	<?php endif; ?>
	<?php if ($precio):?>

	<tr>
		<td class="total-line"><?php echo $this->lang->line('report-Base');?></td>
		<td class="total-value"><?php echo format_price($base);?></td>
	</tr>
	<tr>
		<td class="total-line"><?php echo $this->lang->line('report-IVA');?></td>
		<td class="total-value"><?php echo format_price($iva);?></td>
	</tr>
	<tr>
		<td class="total-line"><?php echo $this->lang->line('report-Total');?></td>
		<td class="total-value"><?php echo format_price($total);?></td>
	</tr>
	<?php endif; ?>
</table>
	<?php if ($precio):?>

<table id="taxes">
	<tr>
		<td class="taxes-head"><?php echo $this->lang->line('report-IVA');?></td>
		<td class="taxes-head"><?php echo $this->lang->line('report-Base');?></td>
		<td class="taxes-head"><?php echo $this->lang->line('report-Importe');?></td>
	</tr>
	<?php $iva = 0;?>
	<?php $base = 0;?>
	<?php foreach($ivas as $k => $v):?>
	<?php 
	$diff = 0;
	if (isset($iva_add) && $k == $iva_add)
		 $diff = $iva_add_value; 	
	?>
	<tr>
		<td class="taxes-value"><?php echo format_number($k);?></td>
		<td class="taxes-value"><?php echo format_price(((($pvp)?format_quitar_iva($totales[$k], $k):$bases[$k]) - $diff), FALSE);?></td>
		<td class="taxes-value"><?php echo format_price(format_iva(((($pvp)?format_quitar_iva($totales[$k], $k):$bases[$k])), $k) + $diff, FALSE);?></td>
	</tr>
	<?php $iva += $v;?>
	<?php $base += $bases[$k];?>
	<?php endforeach;?>
</table>
	<?php endif; ?>
<div style="clear: both"></div>
<?php if (isset($extra_impuestos) && ($show_extra_impuestos)):?> <?php echo $extra_impuestos;?>
<div style="clear: both"></div>
<?php endif; ?>
<?php endif; ?> <?php require(APPPATH . 'views' . DIRECTORY_SEPARATOR . 'reports' . DIRECTORY_SEPARATOR . 'terms.php'); ?>

</div>
