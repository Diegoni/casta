<?php /* Smarty version Smarty-3.1.19, created on 2015-03-20 06:40:21
         compiled from "/var/www/casta/admin/themes/default/template/helpers/list/list_action_view.tpl" */ ?>
<?php /*%%SmartyHeaderCode:770044577550beb055ec5f7-97774630%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1fe6152ae29c5f5fb1d97ac9bb389a65416b8556' => 
    array (
      0 => '/var/www/casta/admin/themes/default/template/helpers/list/list_action_view.tpl',
      1 => 1426844065,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '770044577550beb055ec5f7-97774630',
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
  'unifunc' => 'content_550beb056220e8_33942897',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_550beb056220e8_33942897')) {function content_550beb056220e8_33942897($_smarty_tpl) {?>
<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['href']->value, ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['action']->value, ENT_QUOTES, 'UTF-8', true);?>
" >
	<i class="icon-search-plus"></i> <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['action']->value, ENT_QUOTES, 'UTF-8', true);?>

</a><?php }} ?>
