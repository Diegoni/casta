<?php /* Smarty version Smarty-3.1.19, created on 2015-03-20 06:40:15
         compiled from "/var/www/casta/admin/themes/default/template/controllers/logs/employee_field.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1155828970550beaffbcf613-80472315%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '34eda3fd83c9bc48125004c617b9c8a63aee6091' => 
    array (
      0 => '/var/www/casta/admin/themes/default/template/controllers/logs/employee_field.tpl',
      1 => 1426844061,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1155828970550beaffbcf613-80472315',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'employee_image' => 0,
    'employee_name' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_550beaffbf6406_00442058',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_550beaffbf6406_00442058')) {function content_550beaffbf6406_00442058($_smarty_tpl) {?>
<span class="employee_avatar_small">
	<img class="imgm img-thumbnail" alt="" src="<?php echo $_smarty_tpl->tpl_vars['employee_image']->value;?>
" width="32" height="32" />
</span>
<?php echo $_smarty_tpl->tpl_vars['employee_name']->value;?>
<?php }} ?>
