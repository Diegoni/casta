<?php /* Smarty version Smarty-3.1.19, created on 2015-11-12 09:20:46
         compiled from "C:\xampp2\htdocs\casta\admin1978\themes\default\template\helpers\list\list_action_delete.tpl" */ ?>
<?php /*%%SmartyHeaderCode:85825644841ea57665-71842925%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '05219fc673821e1b59a556c0eb6600b96e573ea5' => 
    array (
      0 => 'C:\\xampp2\\htdocs\\casta\\admin1978\\themes\\default\\template\\helpers\\list\\list_action_delete.tpl',
      1 => 1424703476,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '85825644841ea57665-71842925',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'href' => 0,
    'confirm' => 0,
    'action' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5644841ea8a2f5_97025448',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5644841ea8a2f5_97025448')) {function content_5644841ea8a2f5_97025448($_smarty_tpl) {?>
<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['href']->value, ENT_QUOTES, 'UTF-8', true);?>
"<?php if (isset($_smarty_tpl->tpl_vars['confirm']->value)) {?> onclick="if (confirm('<?php echo $_smarty_tpl->tpl_vars['confirm']->value;?>
')){return true;}else{event.stopPropagation(); event.preventDefault();};"<?php }?> title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['action']->value, ENT_QUOTES, 'UTF-8', true);?>
" class="delete">
	<i class="icon-trash"></i> <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['action']->value, ENT_QUOTES, 'UTF-8', true);?>

</a><?php }} ?>
