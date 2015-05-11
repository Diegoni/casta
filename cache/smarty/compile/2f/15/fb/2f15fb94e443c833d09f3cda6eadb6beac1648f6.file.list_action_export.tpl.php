<?php /* Smarty version Smarty-3.1.19, created on 2015-03-20 06:40:15
         compiled from "/var/www/casta/admin/themes/default/template/controllers/request_sql/list_action_export.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1098915783550beaff550135-97479497%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2f15fb94e443c833d09f3cda6eadb6beac1648f6' => 
    array (
      0 => '/var/www/casta/admin/themes/default/template/controllers/request_sql/list_action_export.tpl',
      1 => 1426844063,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1098915783550beaff550135-97479497',
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
  'unifunc' => 'content_550beaff55d131_82204312',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_550beaff55d131_82204312')) {function content_550beaff55d131_82204312($_smarty_tpl) {?>
<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['href']->value, ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo $_smarty_tpl->tpl_vars['action']->value;?>
" class="btn btn-default">
	<i class="icon-cloud-upload"></i> <?php echo $_smarty_tpl->tpl_vars['action']->value;?>

</a><?php }} ?>
