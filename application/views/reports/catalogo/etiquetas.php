<?php 
$titlelen = isset($titlelen)?$titlelen:30;
$autorlen = isset($autorlen)?$autorlen:30;
?>
<?php foreach($etiquetas as $etq):?>
{D0330,0630,0290|}
{AY;+00,0|}
{C|}
<?php if ($precio): ?>
{PC00;0050,0060,10,15,Q,00,W|}
{RC00;<?php echo format_price($etq['fPVP'], FALSE);?> EUR|}
<?php endif; ?>
{PC00;0280,0270,10,15,G,00,B|}
{RC00;<?php echo format_date(time());?>|}
{PC00;0210,0270,10,15,G,00,B|}
{RC00;<?php echo $etq['cSimbolo'];?>|}
{PC00;0100,0270,10,15,G,00,B|}
{RC00;<?php echo $etq['nIdLibro'];?>|}
<?php /*{PC00;0430,0270,10,10,G,00,F|}
{RC00;<?php echo $etq['nIdProveedor'];?>|}*/?>
{PC00;0340,0070,10,10,G,00,F|}
{RC00;<?php echo $this->config->item('company.name');?>|}
{XB00;0110,0140,9,1,03,0,0100|}
{RB00;<?php echo $etq['nIdLibro'];?>|}
{PC00;0040,0100,10,15,G,00,B|}
{RC00;<?php echo format_title(str_replace("\'", ' ', $etq['cAutores']), $autorlen);?>|}
{PC00;0040,0130,10,15,G,00,B|}
{RC00;<?php echo format_title(str_replace("\'", ' ', $etq['cTitulo']), $titlelen);?>|}
{XS;I,<?php echo sprintf('%04d', $etq['nCantidad'])?>,0002C4100|}
<?php endforeach;?>
