<div class="row">
	<div class="col-md-12">
		<div class="btn-group" role="group" aria-label="...">
			<div class="btn-group" role="group">
				<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
      				<i class="fa fa-th-list"></i>
      				<?php echo $texto['acciones']?>
					<span class="caret"></span>
				</button>
				<ul class="dropdown-menu" role="menu">
					<li><a href="#">Dropdown link</a></li>
					<li><a href="#">Dropdown link</a></li>
				</ul>
			</div>
			
			<button type="button" class="btn btn-default">
				<i class="fa fa-file-o"></i> <?php echo $texto['nuevo']?>
			</button>
			
			<button type="button" class="btn btn-default">
				<i class="fa fa-floppy-o"></i> <?php echo $texto['guardar']?>
			</button>
			
			<button type="button" class="btn btn-default">
				<i class="fa fa-refresh"></i> <?php echo $texto['refresh']?>
			</button>
			
			<button type="button" class="btn btn-default">
				<i class="fa fa-print"></i> <?php echo $texto['imprimir']?>
			</button>
		</div>
	</div>
</div>