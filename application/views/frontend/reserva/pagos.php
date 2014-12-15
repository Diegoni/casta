	<?php $id_hotel=$_COOKIE['id_hotel'];?>
	<div class="col-md-8 col-md-offset-2">
		<div class="panel panel-hotel">
			<div class="panel-heading"><?php echo $texto['datos_personales'];?></div>
		  	<div class="panel-body">
		  		<div id="seleccion">
		  			<?php echo $mensaje; ?>
		  		</div>
		  		
<script language="Javascript">

  function imprSelec(nombre){
	var ficha = document.getElementById(nombre);
	var ventimp = window.open(' ', 'popimpr');
	
	ventimp.document.write( ficha.innerHTML );
	ventimp.document.close();
	ventimp.print( );
	ventimp.close();

  } 

</script> 
		  		<center>
			  		<a href="<?php echo base_url().'index.php/inicio/hotel/'.$id_hotel?>" class="btn btn-hotel boton-redondo" title="<?php echo $texto['home']?>">
						<span class="icon-home"></span>
					</a>
					<a href="javascript:imprSelec('seleccion')" class="btn btn-hotel boton-redondo" title="<?php echo $texto['imprimir']?>">
						<span class="fa fa-print" style="line-height: 1.5; color:#fff;"></span>
					</a>
				</center>
		  	</div>
		</div>
	</div>
</div>	