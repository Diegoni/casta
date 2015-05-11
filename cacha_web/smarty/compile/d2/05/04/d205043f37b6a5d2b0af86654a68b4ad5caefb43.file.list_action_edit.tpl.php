<?php /* Smarty version Smarty-3.1.19, created on 2015-03-20 06:56:55
         compiled from "/var/www/casta/admin2015/themes/default/template/helpers/list/list_action_edit.tpl" */ ?>
<?php /*%%SmartyHeaderCode:772798810550beee73ecf07-25812425%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd205043f37b6a5d2b0af86654a68b4ad5caefb43' => 
    array (
      0 => '/var/www/casta/admin2015/themes/default/template/helpers/list/list_action_edit.tpl',
      1 => 1426844065,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '772798810550beee73ecf07-25812425',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'href' => 0,
    'action' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_550beee73fe932_60080790',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_550beee73fe932_60080790')) {function content_550beee73fe932_60080790($_smarty_tpl) {?>
<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['href']->value, ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['action']->value, ENT_QUOTES, 'UTF-8', true);?>
" class="edit">
	<i class="icon-pencil"></i> <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['action']->value, ENT_QUOTES, 'UTF-8', true);?>

</a><?php }} ?>
