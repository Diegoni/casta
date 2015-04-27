<div class='container'>  
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<?php echo $this->lang->line('pedidos') ?>
			</div>
	  			
			<div class="panel-body">
				<ul class="nav nav-tabs">
					<li class="active"><a href="#tab1" data-toggle="tab"><?php echo $this->lang->line('ctacte') ?></a></li>
					<li><a href="#tab2" data-toggle="tab"><?php echo $this->lang->line('cheques') ?></a></li>
					<li><a href="#tab3" data-toggle="tab"><?php echo $this->lang->line('transferencia') ?></a></li>
				</ul>
  					
				<div class="tab-content">
					<div class="tab-pane active" id="tab1">
						<div class="row">
							<?php echo label_helper_horizontal($this->lang->line('pago_parcial')); ?>
							<?php echo input_helper_horizontal('ctacte', NULL, 8, $this->lang->line('pago_parcial'))?>
							<div class="col-sm-2">
								<?php echo single_button('agregar', 'agregar', NULL, 'btn btn-default')?>
							</div>
						</div>
					</div>
					
					<div class="tab-pane" id="tab2">
						<div class="row">
							
							<?php echo input_helper_horizontal('numero', NULL, 4, $this->lang->line('numero'))?>
							
							<?php echo input_helper_horizontal('banco', NULL, 3, $this->lang->line('banco'))?>
						
							<?php echo input_helper_horizontal('importe', NULL, 3, $this->lang->line('importe'))?>
						
							<div class="col-sm-2">
								<?php echo single_button('agregar', 'agregar', NULL, 'btn btn-default')?>
							</div>
						</div>
					</div>
					
					<div class="tab-pane" id="tab3">
						<div class="row">
							<?php echo $this->lang->line('transferencia') ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
	  			