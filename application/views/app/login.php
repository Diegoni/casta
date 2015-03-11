<?php echo js_libreria('jquery/jquery.js'); ?>

<?php echo js_libreria('bootstrap/js/bootstrap.js'); ?>
<?php echo css_libreria('bootstrap/css/bootstrap.css'); ?>

<?php echo js_libreria('main/js/login.js'); ?>
<?php echo css_libreria('main/css/login.css'); ?>


<div class="container">
	<div class="login-container">
		<div id="output"></div>
		<div class="avatar"></div>
		<div class="form-box">
			<?php $mensaje=validation_errors(); ?>
            <?php if($mensaje!=""){ ?>
            	<div class="alert alert-danger animated fadeInUp">
            		Login incorrecto
            	</div>	
            <?php }else{ ?>
            	<div class="alert alert-success animated fadeInUp">
            		<?php echo $this->lang->line('ingrese')." ".$this->lang->line('usuario')." & ".$this->lang->line('pass') ?>
            	</div>
            <?php } ?>
			<?php echo form_open('sys/app/verifylogin'); ?>
				<input name="username" type="text" placeholder="<?php echo $this->lang->line('usuario')?>">
				<input name="password" type="password" placeholder="<?php echo $this->lang->line('pass')?>">
				<button class="btn btn-info btn-block login" type="submit"><?php echo $this->lang->line('login')?></button>
			<?php echo form_close() ?>
		</div>
	</div>
</div>