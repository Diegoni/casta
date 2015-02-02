<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
	<head>
<link rel="shortcut icon"
	href="<?php echo image_asset_url($this->config->item('bp.application.icon')); ?>" />
		<meta content='text/html; charset=iso-8859-1' http-equiv='Content-type' />
		<meta content='width=330, height=400, initial-scale=1' name='viewport' />
		<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
<title><?php echo $title;?></title>
<?php echo css_asset('login2.css', 'main'); ?>

		<script src="/javascripts/login.js?369b911a77" type="text/javascript"></script>
	</head>
	<body style='min-height:380px'>
		<div class='layout' id='page'>
			<div class='centered'>
				<div class='column' style='margin-top:-174px'>
					<div class='logo'>
					</div>
					<div class='login_page preserve_links'>
						<div class='title_graphic'>
						</div>
						<div class='login_frame flexible'>
							<div class='top'>
							</div>
							<div class='middle'>
								<div class='flash_message'>
									<div class='flash_boxes'>
									</div>

								</div>
								<form action="/sessions/create" class="onboard_form" method="post">
									<div style="margin:0;padding:0">
										<input name="authenticity_token" type="hidden" value="k9zlIQY1/SgT+kuxom9EVXQdoVIbGk22RrEwltiiaxI=" />
									</div>
									<div class='field text_field'>
										<label for="email">
											Email address
										</label>
									</div>
									<div class='another_row'>
										<input class="autotab behavior" id="email" name="email" tabindex="1" type="text" />
									</div>
									<div class='field text_field'>
										<label for="password">
											Password
										</label>
										<span>&nbsp;&nbsp;</span>
										<a href="/users/forgot_password?from_openid=false">Forgot password?</a>
									</div>
									<div class='one_more_row'>
										<input id="password" name="password" tabindex="2" type="password" value="" />
									</div>
									<div class='remember_me_field'>
										<div class='field check_box_field'>
											<input id="remember_me" name="remember_me" type="checkbox" value="1" />
											<label for="remember_me" style="display:inline">
												Stay signed in
											</label>
										</div>
									</div>
									<div class='actions'>
										<div class="right gistsubmit" onclick="document.getElementById(&quot;loading_spinner&quot;).style.display = &quot;inline&quot;;">
											<input name="commit" type="submit" value="Sign In" />
											<span></span>
										</div>
										<img alt="Sm_loader" class="right" id="loading_spinner" src="/images/loaders/sm_loader.gif?369b911a77" style="margin-top:13px;display:none;" />
										<div class="clear">
										</div>
									</div>
									<input id="time_zone_offset" name="time_zone_offset" type="hidden" value="-8" />
									<input id="from_openid" name="from_openid" type="hidden" />
									<input id="version" name="version" type="hidden" />
									<a href="/sessions/new?openid=true">Sign in with Google Apps</a>
									<br />
									<a href="/users/new">Create account</a>
									<br />
									<a href="/corp/home">Return to main Gist website</a>
									<div class="clear">
									</div>
								</form>
							</div>
							<div class='bottom'>
							</div>
						</div>
					</div>
					<script type='text/javascript'>
						//<![CDATA[
						window.onload = function() {
							signalNoAuth();
						};
						//]]>
					</script>
					<script src="/javascripts/pages/login_page.js?369b911a77" type="text/javascript"></script>
				</div>
			</div>
		</div>
		<div id='outlook_transport' style='display:none'>
		</div>
	</body>
</html> 