<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default">
			<?php foreach ($hoteles as $hotel) { ?>
				<div class="panel-heading">
					<?php echo $hotel->hotel?>
				</div>	
				<div class="panel-body">
	  				<?php echo $hotel->descripcion?>
				</div>
			<?php } ?>
		</div>
	</div>
</div>

	