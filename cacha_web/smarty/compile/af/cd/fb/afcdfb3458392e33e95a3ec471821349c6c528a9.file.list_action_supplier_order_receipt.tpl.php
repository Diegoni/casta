<?php /* Smarty version Smarty-3.1.19, created on 2015-03-20 06:40:22
         compiled from "/var/www/casta/admin/themes/default/template/helpers/list/list_action_supplier_order_receipt.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2085296366550beb06beeeb1-63095158%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'afcdfb3458392e33e95a3ec471821349c6c528a9' => 
    array (
      0 => '/var/www/casta/admin/themes/default/template/helpers/list/list_action_supplier_order_receipt.tpl',
      1 => 1426844065,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2085296366550beb06beeeb1-63095158',
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
  'unifunc' => 'content_550beb06c21fe0_70439672',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_550beb06c21fe0_70439672')) {function content_550beb06c21fe0_70439672($_smarty_tpl) {?>
<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['href']->value, ENT_QUOTES, 'UTF-8', true);?>
" class="edit btn btn-default" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['action']->value, ENT_QUOTES, 'UTF-8', true);?>
">
	<i class="icon-truck"></i> <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['action']->value, ENT_QUOTES, 'UTF-8', true);?>

</a><?php }} ?>
