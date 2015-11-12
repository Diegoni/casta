<?php /* Smarty version Smarty-3.1.19, created on 2015-11-12 09:20:46
         compiled from "C:\xampp2\htdocs\casta\admin1978\themes\default\template\helpers\list\list_action_edit.tpl" */ ?>
<?php /*%%SmartyHeaderCode:251855644841ea28867-40266207%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e9952b85ce24e393527868d6ea0a1a1034a38b02' => 
    array (
      0 => 'C:\\xampp2\\htdocs\\casta\\admin1978\\themes\\default\\template\\helpers\\list\\list_action_edit.tpl',
      1 => 1424703476,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '251855644841ea28867-40266207',
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
  'unifunc' => 'content_5644841ea3ff64_23116013',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5644841ea3ff64_23116013')) {function content_5644841ea3ff64_23116013($_smarty_tpl) {?>
<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['href']->value, ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['action']->value, ENT_QUOTES, 'UTF-8', true);?>
" class="edit">
	<i class="icon-pencil"></i> <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['action']->value, ENT_QUOTES, 'UTF-8', true);?>

</a><?php }} ?>
