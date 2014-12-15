
<div class="row">
	<div class="col-md-4">
		<?php foreach ($ayudas as $ayuda) { ?>
		<div class="panel panel-hotel">
	  		<div class="panel-heading"><?php echo $ayuda->titulo; ?></div>
	  		<div class="panel-body">
	  			<p id="texto_ayuda" style="display: none">
	  				<?php echo $ayuda->ayuda; ?>	
	  			</p> 			
			</div>
		</div>
		<?php } ?>
	</div>
	