<?php /* Smarty version Smarty-3.1.19, created on 2015-03-20 06:55:44
         compiled from "/var/www/casta/admin2015/themes/default/template/content.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1449412433550beea01f4da1-25786008%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c23cf20cf6cda7f1aed5dcd8436fd51a240ca739' => 
    array (
      0 => '/var/www/casta/admin2015/themes/default/template/content.tpl',
      1 => 1426844057,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1449412433550beea01f4da1-25786008',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'content' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_550beea0201866_83864806',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_550beea0201866_83864806')) {function content_550beea0201866_83864806($_smarty_tpl) {?>
<div id="ajax_confirmation" class="alert alert-success hide"></div>

<div id="ajaxBox" style="display:none"></div>


<div class="row">
	<div class="col-lg-12">
		<?php if (isset($_smarty_tpl->tpl_vars['content']->value)) {?>
			<?php echo $_smarty_tpl->tpl_vars['content']->value;?>

		<?php }?>
	</div>
</div><?php }} ?>
