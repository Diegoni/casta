<?php foreach ($etiquetas as $etq):?>
{D0190,0470,0170|}
{AY;+00,0|}
{C|}
{PC00;0005,0070,15,15,Q,00,W|}
{RC00;<?php echo $etq['cDescripcion'];?>|}
{XB00;0050,0080,3,1,02,04,07,08,04,0,0070,+0000000000,0,00,N|}
{RB00;*U<?php echo $etq['nIdUbicacion'];?>*|}
{XS;I,0001,0002C4100|}
<?php endforeach; ?>