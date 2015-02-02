<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="shortcut icon"
	href="<?php echo image_asset_url($this->config->item('bp.application.icon')); ?>" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $title;?></title>
<?php echo css_asset('login.css', 'main'); ?>
<!--
<link href="http://localhost/casta_test/assets/modules/main/css/login.css" rel="stylesheet" type="text/css">
-->
<?php echo js_asset('jQuery/jquery.min.js'); ?>
<?php echo js_asset('jQuery/jquery.measure.min.js'); ?>
<?php echo js_asset('jQuery/jquery.place.min.js'); ?>
<?php echo js_asset('jQuery/jquery.pulse.min.js'); ?>
<?php echo js_asset('jQuery/jquery.mask.min.js'); ?>
<?php echo js_asset('jQuery/jquery.loading.min.js'); ?>

<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){
	var login = function(event){
		$("#login_button").attr("disabled", "disabled");
		$('#login_form').loading({
			align: 'center',
			img: '<?php echo image_asset_url('loading.gif'); ?>',
			text: '<?php echo $this->lang->line('Conectando');?>',
			effect: 'update',
			mask: true  
		});
		$('#msg').hide('fast');
		$.post("<?php echo site_url('user/auth/login');?>", {
				username: $('#username').val(),
				password: $('#password').val()
			},
			function(data){
				if (data.success) 
				{
					window.location = '<?php echo $url;?>';
				}
				else
				{ 
					$('#login_form').loading(false);
					$('#msg').show('fast');
					$('#msg_text').html(data.message);
				}
			}, "json");
		return false;
	}
	
	$(".block").fadeIn(1000);				   
	$(".idea").fadeIn(1000);	
	$("#msg").hide();		
	$("#login_button").click(login);
	$("#login_form").submit(login);	
});
/* ]]> */
</script>
</head>

<body>
<div id="wrap">
<div class="idea">
	<p><?php echo $title; ?></p>
</div>

<div class="block">
<form action="#" method="post" id="login_form">
	<div class="left"></div>
	<div class="right">
		<div class="div-row">
			<input 
				type="text" 
				id="username" 
				name="username"
				onfocus="this.value='';"
				onblur="if (this.value=='') {this.value='<?php echo $this->lang->line('Username');?>';}"
				value="<?php echo $this->lang->line('Username');?>" 
			/>
		</div>
		<div class="div-row">
			<input 
				type="password" 
				id="password"
				name="password" 
				onfocus="this.value='';"
				onblur="if (this.value=='') {this.value='************';}"
				value="************" 
			/>
		</div>
		
		<div class="rm-row">&nbsp;</div>
		
		<div class="send-row">
			<input type="submit" style="visibility: hidden"/>
			<a class="button" href="#" onclick="this.blur();">
				<span id="login_button">
					<?php echo $this->lang->line('Login');?>
				</span>
			</a>
		</div>
	</div>
</form>
</div>
<div class="msg" id="msg">
	<img
		src="<?php echo image_asset_url('login/message.png'); ?>" 
		alt="" 
	/>
	<p id="msg_text"></p>
</div>

</div>
</body>
</html>
