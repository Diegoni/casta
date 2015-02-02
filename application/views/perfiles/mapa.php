<?php echo $direccion;?>
<br />
<iframe
	width="600" height="400" frameborder="0" scrolling="no"
	marginheight="0" marginwidth="0"
	src="http://maps.google.com/maps?q=<?php echo $direccion;?>&amp;output=embed"></iframe>
<br />
<small><a
	href="http://maps.google.com/maps?q=<?php echo $direccion;?>&amp;source=embed"
	style="color: #0000FF; text-align: left"><?php echo $this->lang->line('Ver mapa más grande');?></a></small>
