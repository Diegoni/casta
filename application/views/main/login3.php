<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title><?php echo $title;?></title>
<link rel="shortcut icon"
    href="<?php echo image_asset_url($this->config->item('bp.application.icon')); ?>" />
		<meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
<?php echo css_asset('bootstrap.min.css', 'beta'); ?>
<?php echo css_asset('bootstrap-responsive.min.css', 'beta'); ?>
<?php echo css_asset('unicorn.login.css', 'beta'); ?>
<?php echo css_asset('uniform.css', 'beta'); ?>
<?php echo css_asset('unicorn.main.css', 'beta'); ?>

    </head>
    <body>
        <div id="logo">
            <img src="<?php echo image_asset_url('logo.png', 'beta'); ?>" alt="" />
        </div>
        <div id="loginbox">            
            <form id="login_form" class="form-vertical" method="post" action="#" name="login_form" novalidate="novalidate">
				<p><?php echo $this->lang->line('Indique nombre de usuario y contraseña para continuar.');?></p>
                <div class="control-group">
                    <div class="controls">
                        <div class="input-prepend">
                            <span class="add-on"><i class="icon-user"></i></span><input type="text" placeholder="<?php echo $this->lang->line('Usuario');?>" id="username" name="username"/>
                        </div>
                    </div>
                </div>
                <div class="control-group">
                    <div class="controls">
                        <div class="input-prepend">
                            <span class="add-on"><i class="icon-lock"></i></span><input type="password" placeholder="<?php echo $this->lang->line('Contraseña');?>" 
                            id="password" name="password"/>
                        </div>
                    </div>
                </div>
                <div class="form-actions">
                    <span class="pull-right"><input id="login_button" type="submit" class="btn btn-inverse" value="<?php echo $this->lang->line('Login');?>" /></span>
                </div>
            </form>
        </div>
                <div id="status"></div>

<?php echo js_asset('lib/jquery.min.js', 'beta'); ?>
<?php echo js_asset('lib/jquery.validate.js', 'beta'); ?>


<script type="text/javascript">
/* <![CDATA[ */
$(document).ready(function(){

    if($.browser.msie == true && $.browser.version.slice(0,3) < 10) {
        $('input[placeholder]').each(function(){ 
       
            var input = $(this);       
           
            $(input).val(input.attr('placeholder'));
                   
            $(input).focus(function(){
                 if (input.val() == input.attr('placeholder')) {
                     input.val('');
                 }
            });
           
            $(input).blur(function(){
                if (input.val() == '' || input.val() == input.attr('placeholder')) {
                    input.val(input.attr('placeholder'));
                }
            });
        });
    }

    $("#login_form").validate({
        rules:{
            password:{
                required:true
            },
            username:{
                required:true,
            }
        },
        errorClass: "help-inline",
        errorElement: "span",
        highlight:function(element, errorClass, validClass) {
            $(element).parents('.control-group').addClass('error');
        },
        unhighlight: function(element, errorClass, validClass) {
            $(element).parents('.control-group').removeClass('error');
            $(element).parents('.control-group').addClass('success');
        },
        submitHandler: function(data) {
            $.post("<?php echo site_url('user/auth/login');?>", {
                    username: $('#username').val(),
                    password: $('#password').val()
                },
                function(data){
                    if (data.success) 
                    {
                        $("#status").fadeTo(500,1,function(){            
                            $(this).html("<div class=\"alert alert-success\">" + data.message + "</div>").fadeTo(5000, 0); 
                        });
                        window.location = '<?php echo $url;?>';
                    }
                    else
                    { 
                        $("#status").fadeTo(500,1,function(){            
                            $(this).html("<div class=\"alert alert-error\">" + data.message + "</div>").fadeTo(5000, 0); 
                        });
                    }
                }, "json"
            );
            return false;
        }
    });
});
/* ]]> */
</script>
    </body>
</html>
