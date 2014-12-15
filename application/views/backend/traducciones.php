<div class="container">
<div class="row">
	<div class="col-md-2">
		<div class="panel panel-danger">
  			<div class="panel-heading">
  				<span class="icon-clipboard-paste"></span> Otros
  			</div>
  			<div class="panel-body">
    			<ul class="nav nav-pills nav-stacked">
	            	<li><a  href='<?php echo site_url('admin/otro/departamentos_abm')?>'>Departamentos</a></li>
	            	<li><a  href='<?php echo site_url('admin/otro/provincias_abm')?>'>Provincias</a></li>
	            	<li><a  href='<?php echo site_url('admin/otro/paises_abm')?>'>Países</a></li>
	            	<hr>
	            	<li><a  href='<?php echo site_url('admin/otro/tipos_abm')?>'>Tipos</a></li>
	            	<li><a  href='<?php echo site_url('admin/otro/aerolineas_abm')?>'>Aerolineas</a></li>
	            	<li><a  href='<?php echo site_url('admin/traduccion')?>'>Traducción</a></li>
	            	<hr>
	            	<li><a  href='<?php echo site_url('admin/otro/terminos_abm')?>'>Términos y condiciones</a></li>
					<li><a  href='<?php echo site_url('admin/otro/ayudas_abm')?>'>Ayudas de la página</a></li>
					<li><a  href='<?php echo site_url('admin/otro/idiomas_abm')?>'>Idiomas</a></li>
					<li><a  href='<?php echo site_url('admin/otro/config_correo/edit/1')?>'>Config correo</a></li>
          		</ul>
  			</div>
		</div>
	</div>

	<div class="col-md-10">
		<div class="panel panel-danger">
  			<div class="panel-heading">
  				<span class="icon-clipboard-paste"></span> Otros
  			</div>
  			<div class="panel-body">
  				<form action="" method="post" class="form-horizontal">
  				<div class="col-sm-5">
    				<div class="form-group">
    					<label>Modulo <span class="required">*</span> :</label>
    					<select name="modulo" class="chosen-select chzn-done form-control" required>
		  					<option value=""></option>
			  				
			  				<?php 
			  				if($this->input->post("modulo")){
			  					foreach ($modulos as $modulo) {
			  						echo "<option value=".$modulo->id_modulo;
			  						if($modulo->id_modulo==$this->input->post("modulo")){
			  							echo " selected ";
			  						}
			  						echo ">".$modulo->modulo."</option>";
								}	
			  				}else{
			  					foreach ($modulos as $modulo) {
			  						echo "<option value=".$modulo->id_modulo.">".$modulo->modulo."</option>";
								}	
			  				}
			  				?>	
		  				</select>
    				</div>
    			</div>
  				
  				<div class="col-sm-5">
    				<div class="form-group">
    					<label>Idioma <span class="required">*</span> :</label>
    					<select name="idioma" class="chosen-select chzn-done form-control" required>
		  					<option value=""></option>
			  				<?php 
			  				if($this->input->post("idioma")){
			  					foreach ($idiomas as $idioma) {
			  						echo "<option value=".$idioma->id_idioma;
			  						if($idioma->id_idioma==$this->input->post("idioma")){
			  							echo " selected ";
			  						}
			  						echo ">".$idioma->idioma."</option>";
								}	
			  				}else{
				  				foreach ($idiomas as $idioma) {
									echo "<option value=".$idioma->id_idioma.">".$idioma->idioma."</option>";
								}
							}
							?>	
		  				</select>
  					</div>
    			</div>
  				<!--
  				<div class="col-sm-6">
    				<div class="form-group col-sm-6">
    					<label>Estado :</label>
    					<select name="estado" class="chosen-select chzn-done form-control">
		  					<option value=""></option>
			  				<?php 
			  				if($this->input->post("estado")){
			  					foreach ($estados_traduccion as $estado) {
			  						echo "<option value=".$estado->id_estado_traduccion;
			  						if($estado->id_estado_traduccion==$this->input->post("estado")){
			  							echo " selected ";
			  						}
			  						echo ">".$estado->estado_traduccion."</option>";
								}	
			  				}else{
				  				foreach ($estados_traduccion as $estado) {
									echo "<option value=".$estado->id_estado_traduccion.">".$estado->estado_traduccion."</option>";
								}
							}
							?>	
		  				</select>
		  			</div>
    			</div>
    			-->
    			<div class="col-sm-2">
    				<div class="form-group col-sm-6">
    					<button class="btn btn-default" name="enviar" value="1"> Enviar</button>
    				</div>
    			</div>
    			</form>
    			<div class="col-sm-12">
    			<?php 
    			if(isset($registros)){?>
    				<table class="table table-hover" id="flex1">
    					<thead>
    						<tr>
    							<td>Estado</td>
    							<?php if(isset($registro->titulo_tabla)){ ?>
    								<td>Título</td>
    							<?php }else{ ?>
    								<td>Descripción</td>
    							<?php } ?>
    							<?php if(isset($registro->hotel)){ ?>
    							<td>Hotel</td>
    							<?php } ?>
    							<td>Traduccir</td>
    						</tr>
    					</thead>
    					<tbody>
    						<?php
    						foreach ($registros as $registro) {
    							echo "<tr>";	
    							echo "<td>".$registro->label."</td>";	
								if(isset($registro->titulo_tabla)){				
									echo "<td>".$registro->titulo_tabla."</td>";
								}else{
									echo "<td>".$registro->descripcion_tabla."</td>";
								}
								if(isset($registro->hotel)){
									echo "<td>".$registro->hotel."</td>";	
								}
								echo "<td><button class='btn btn-default' data-toggle='modal' data-target='#traducir".$registro->id_tabla."'>Traducir</button></td>";
								echo "<tr>";
							}
							?>	
    					</tbody>
    				</table>
    				
    				
    				<?php
    				foreach ($registros as $registro) {
    				?>
					<div class="modal fade" id="traducir<?php echo $registro->id_tabla?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  						<div class="modal-dialog modal-extra-lg">
    						<div class="modal-content">
    						<form action="" method="post">
      							<div class="modal-header">
        							<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        							<h4 class="modal-title" id="myModalLabel"><?php echo $registro->titulo_tabla?></h4>
      							</div>
      							<div class="modal-body">
        							<div class="row">
        								<div class="slidingDiv<?php echo $registro->id_tabla?>">
        								<div class="col-sm-6">
        									<h3>Registro</h3>
        									<?php if(isset($registro->titulo_tabla)){ ?>
	        									<div class="form-group">
													<label for="exampleInputEmail1">Título</label>
													<input type="text" class="form-control" id="titulo_tabla<?php echo $registro->id_tabla?>" value="<?php echo $registro->titulo_tabla?>" readonly style="width: 100%;">
												</div>
											<?php } ?>
											<?php if(isset($registro->descripcion_tabla)){ ?>
												<div class="form-group">
													<label for="exampleInputEmail1">Descripción</label>
													<textarea class="ckeditor" rows="20" id="descripcion_tabla<?php echo $registro->id_tabla?>" style="width: 100%;" name="descripcion_tabla<?php echo $registro->id_tabla?>">
														<?php echo $registro->descripcion_tabla?>
													</textarea>
												</div>
											<?php } ?>
        								</div>
        								</div>
        								
        								<div class="col-sm-6 col_idioma<?php echo $registro->id_tabla?>">
        									<h3>Traducción</h3>
        									<?php if(isset($registro->titulo_tabla)){ ?>
	        									<div class="form-group">
													<label for="exampleInputEmail1">Título</label>
													<div class="input-group">
														<div class="input-group-addon" title="Copiar" id="copiar_titulo<?php echo $registro->id_tabla?>">
															<span class="icon-chevron-right"></span>
														</div>
														<input type="text" class="form-control" id="titulo_idioma<?php echo $registro->id_tabla?>" value="<?php echo $registro->titulo_idioma?>" name="titulo_idioma<?php echo $registro->id_tabla?>">
													</div>
												</div>
											<?php } ?>
											<?php if(isset($registro->descripcion_tabla)){ ?>
												<div class="form-group">
													<label for="exampleInputEmail1">Descripción</label>
													<div class="input-group">
														<div class="input-group-addon" title="Copiar" id="copiar_descripcion<?php echo $registro->id_tabla?>">
															<span class="icon-chevron-right"></span>
														</div>
														<textarea class="ckeditor" cols="80" id="descripcion_idioma<?php echo $registro->id_tabla?>" name="descripcion_idioma<?php echo $registro->id_tabla?>" rows="20" style="width: 100%;">	
															<?php 
															if($registro->descripcion_idioma!="-"){
																echo $registro->descripcion_idioma;
															}?>
														</textarea>
													</div>
												</div>
											<?php } ?>
        								</div>
        							
        							</div>
        							<script>
										$('#copiar_titulo<?php echo $registro->id_tabla?>').click(function(){
											$('#titulo_idioma<?php echo $registro->id_tabla?>').val($('#titulo_tabla<?php echo $registro->id_tabla?>').val());
											var valor=$("#descripcion_tabla<?php echo $registro->id_tabla?>").val();
											$("#descripcion_idioma<?php echo $registro->id_tabla?>").val(valor);
										});
										
										
										$('#copiar_descripcion<?php echo $registro->id_tabla?>').click(function(){
											var str = document.getElementById('descripcion_tabla<?php echo $registro->id_tabla?>').value;
											CKEDITOR.instances['descripcion_idioma<?php echo $registro->id_tabla?>'].insertHtml(str);
											CKEDITOR.instances['descripcion_tabla<?php echo $registro->id_tabla?>'].setDisabled();
										});
										
        							</script>
      							</div>
								<div class="modal-footer">
									<input name="modulo" type="hidden" value="<?php echo $this->input->post('modulo') ?>" />
									<input name="idioma" type="hidden" value="<?php echo $this->input->post('idioma') ?>" />
									<input name="estado" type="hidden" value="<?php echo $this->input->post('estado') ?>" />
									<input name="id_modulo_idioma" type="hidden" value="<?php echo $registro->id_modulo_idioma ?>" />
									<button type="button" class="btn btn-default show_hide<?php echo $registro->id_tabla?>"><span class="icon-chevron-left"></span>Ocultar</button>
									<button type="button" class="btn btn-default hide_show<?php echo $registro->id_tabla?>"><span class="icon-chevron-right"></span>Ver</button>
									<button type="button" class="btn btn-default"	data-dismiss="modal">Cerrar</button>
									<button type="submit" name="traducir" value="1" class="btn btn-danger" >Sin traducción</button>
									<button type="submit" name="traducir" value="3" class="btn btn-success">Finalizar</button>
									<button type="submit" name="traducir" value="2" class="btn btn-primary">Guardar</button>
								</div>
									<script>
										$('.show_hide<?php echo $registro->id_tabla?>').click(function(){
											$('.show_hide<?php echo $registro->id_tabla?>').hide();
    										$(".slidingDiv<?php echo $registro->id_tabla?>").hide(1000);
    										$(".hide_show<?php echo $registro->id_tabla?>").show();
    										$(".col_idioma<?php echo $registro->id_tabla?>").removeClass('col-sm-6');
    										$(".col_idioma<?php echo $registro->id_tabla?>").addClass('col-sm-12');
    										
    									});
    									
    									$('.hide_show<?php echo $registro->id_tabla?>').hide();
    									
    									$('.hide_show<?php echo $registro->id_tabla?>').click(function(){
											$('.show_hide<?php echo $registro->id_tabla?>').show();
    										$(".slidingDiv<?php echo $registro->id_tabla?>").show("drop",1000);
    										$(".hide_show<?php echo $registro->id_tabla?>").hide();
    										$(".col_idioma<?php echo $registro->id_tabla?>").removeClass('col-sm-12');
    										$(".col_idioma<?php echo $registro->id_tabla?>").addClass('col-sm-6');
    									});
									</script>
								</form>	
							</div>
						</div>
					</div>
					<?php } ?>
    			<?php } ?>
    			</div>
  			</div>
		</div>
    </div>
</div>  





 <script src="<?php echo base_url().'librerias/chosen/chosen.jquery.js'?>" type="text/javascript"></script>
 <script src="<?php echo base_url().'librerias/chosen/prism.js'?>" type="text/javascript" charset="utf-8"></script>
  <script type="text/javascript">
    var config = {
      '.chosen-select'           : {},
      '.chosen-select-deselect'  : {allow_single_deselect:true},
      '.chosen-select-no-single' : {disable_search_threshold:10},
      '.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
      '.chosen-select-width'     : {width:"95%"}
    }
    for (var selector in config) {
      $(selector).chosen(config[selector]);
    }
  </script>    


