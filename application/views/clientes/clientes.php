<div class="row">
	<div class="col-md-12">
		<form class="form-horizontal" role="form">
		<div class="form-group">
			<label class="col-sm-1 control-label">
				<?php echo $texto['empresa']?>
			</label>
			<div class="col-lg-11">
				<div class="input-group">
					<input type="text" class="form-control" name="empresa">
					<span class="input-group-btn">
						<button class="btn btn-default" type="button">	<i class="fa fa-search"></i> <?php echo $texto['buscar']?></button>
      				</span>
				</div>
			</div>
		</div>
		</form>
		<form class="form-horizontal" role="form">
			<ul class="nav nav-tabs">
				<li class="active">
					<a href="#general" data-toggle="tab">
						<i class="fa fa-home"></i>
						<?php echo $texto['general']?>
					</a>
				</li>
				<li>
					<a href="#perfiles" data-toggle="tab">
						<i class="fa fa-book"></i>
						<?php echo $texto['perfiles']?>
					</a>
				</li>
				<li>
					<a href="#notas" data-toggle="tab">
						<i class="fa fa-file-text-o"></i>
						<?php echo $texto['notas']?>
					</a>
				</li>
				<li>
					<a href="#historico" data-toggle="tab">
						<i class="fa fa-archive"></i>
						<?php echo $texto['historico']?>
					</a>
				</li>
			</ul>
			<div class="tab-content">
    		<div class="tab-pane active" id="general">
				<div class="form-group">
					<label class="col-sm-1 control-label">
						<?php echo $texto['nombre']?>
					</label>
					<div class="col-sm-5">
						<input type="text" class="form-control" name="nombre">
					</div>
					<label class="col-sm-1 control-label">
						<?php echo $texto['apellido']?>
					</label>
					<div class="col-sm-5">
						<input type="text" class="form-control" name="apellido">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-1 control-label">
						<?php echo $texto['empresa']?>
					</label>
					<div class="col-sm-11">
						<input type="text" class="form-control" name="empresa">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-1 control-label">
						<?php echo $texto['cuil']?>
					</label>
					<div class="col-sm-5">
						<input type="text" class="form-control" name="cuil">
					</div>
					<label class="col-sm-1 control-label">
						<?php echo $texto['estado']?>
					</label>
					<div class="col-sm-5">
						<select class="form-control chosen-select" data-placeholder="<?php echo $texto['seleccione']." ".$texto['estado']?>" name="estadoscliente">
							<option value=""></option>
							<?php foreach ($estadoscliente as $row) { ?>
								<option value="<?php echo $row->nIdEstado?>">
									<?php echo $row->cDescripcion ?>
								</option>
							<?php } ?>
						</select>
					</div>
				</div>	
				<div class="form-group">
					<label class="col-sm-1 control-label">
						<?php echo $texto['tipo']?>
						<?php echo $texto['cliente']?>
					</label>
					<div class="col-sm-5">
						<select class="form-control chosen-select" data-placeholder="<?php echo $texto['seleccione']." ".$texto['tipo']?>" name="tiposcliente">
							<option value=""></option>
							<?php foreach ($tiposcliente as $row) { ?>
								<option value="<?php echo $row->nIdTipoCliente?>">
									<?php echo $row->cDescripcion ?>
								</option>
							<?php } ?>
						</select>
					</div>
					<label class="col-sm-1 control-label">
						<?php echo $texto['grupo']?>
						<?php echo $texto['cliente']?>
					</label>
					<div class="col-sm-5">
						<select class="form-control chosen-select" data-placeholder="<?php echo $texto['seleccione']." ".$texto['grupo']?>" name="gruposcliente">
							<option value=""></option>
							<?php foreach ($gruposcliente as $row) { ?>
								<option value="<?php echo $row->nIdGrupoCliente?>">
									<?php echo $row->cDescripcion ?>
								</option>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-1 control-label">
						<?php echo $texto['tipo']?>
						<?php echo $texto['tarifa']?>
					</label>
					<div class="col-sm-5">
						<select class="form-control chosen-select" data-placeholder="<?php echo $texto['seleccione']." ".$texto['tarifa']?>" name="tarifascliente">
							<option value=""></option>
							<?php foreach ($tarifascliente as $row) { ?>
								<option value="<?php echo $row->nIdTipoTarifa?>">
									<?php echo $row->cDescripcion." - ".$row->fMargen ?>
								</option>
							<?php } ?>
						</select>
					</div>
					<label class="col-sm-1 control-label">
						<?php echo $texto['idioma']?>
					</label>
					<div class="col-sm-5">
						<select class="form-control chosen-select" data-placeholder="<?php echo $texto['seleccione']." ".$texto['idioma']?>" name="idioma">
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-1 control-label">
						<?php echo $texto['exento_iva']?>
					</label>
					<div class="col-sm-2">
						<input id="switch-modal" type="checkbox" name="execto_iva" checked>
					</div>
					<label class="col-sm-1 control-label">
						<?php echo $texto['recargo']?>
					</label>
					<div class="col-sm-2">
						<input id="switch-modal" type="checkbox" name="recargo" checked>
					</div>
					<label class="col-sm-1 control-label">
						<?php echo $texto['credito']?>
					</label>
					<div class="col-sm-2">
						<input id="switch-modal" type="checkbox" name="credito" checked>
					</div>
					<label class="col-sm-1 control-label">
						<?php echo $texto['emails']?>
					</label>
					<div class="col-sm-2">
						<input id="switch-modal" type="checkbox" name="emails" checked>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-1 control-label">
						<?php echo $texto['saldo']?>
					</label>
					<div class="col-sm-5">
						<input type="text" class="form-control" name="saldo">
					</div>
					<label class="col-sm-1 control-label">
						<?php echo $texto['referencia']?>
					</label>
					<div class="col-sm-5">
						<input type="text" class="form-control" name="referencia">
					</div>
				</div>
			</div>
			<div class="tab-pane" id="perfiles">
				Periles
			</div>
			<div class="tab-pane" id="notas">
				Notas
			</div>
			<div class="tab-pane" id="historico">
				Historico
			</div>
			</div>					
		</form>
	</div>
</div>
</div>
