/**
 * Unicorn Admin Template
 * Diablo9983 -> diablo9983@gmail.com
**/
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
            console.log("<?php echo site_url('user/auth/login');?>");
            $.post("<?php echo site_url('user/auth/login');?>", {
                    username: $('#username').val(),
                    password: $('#password').val()
                },
                function(data){
                    if (data.success) 
                    {
                        $("#status").fadeTo(500,1,function(){            
                            $(this).html("<div class=\"alert alert-success\">Form was submitted!</div>").fadeTo(5000, 0); 
                        });
                        //window.location = '<?php echo $url;?>';
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