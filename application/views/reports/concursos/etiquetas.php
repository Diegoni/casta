<?php foreach($etiquetas as $etq):?>
<?php $lineas = $this->utils->partirTeixells($etq['cTexto'], $this->config->item('concursos.teixells.length')); ?>
<?php if (count($lineas) > 1) : ?>
{D0190,0470,0170|}
{AY;+00,0|}
{C|}{PC00;0045,0085,08,10,Q,00,B|}{RC00;<?php echo $lineas[0]; ?>|}
{PC00;0045,0130,08,10,Q,00,B|}{RC00;<?php echo $lineas[1]; ?>|}
{XS;I,0001,0002C4100|}
<?php else: ?>
{D0190,0470,0170|}
{AY;+00,0|}
{C|}{PC00;0045,0105,10,15,Q,00,B|}{RC00;<?php echo $lineas[0]; ?>|}{XS;I,0001,0002C4100|}
<?php endif; ?>
<?php endforeach;?>