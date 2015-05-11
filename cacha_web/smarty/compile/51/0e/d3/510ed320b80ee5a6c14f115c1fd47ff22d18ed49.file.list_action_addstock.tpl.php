<?php /* Smarty version Smarty-3.1.19, created on 2015-03-20 06:40:22
         compiled from "/var/www/casta/admin/themes/default/template/helpers/list/list_action_addstock.tpl" */ ?>
<?php /*%%SmartyHeaderCode:353583588550beb06baa4b5-07837061%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '510ed320b80ee5a6c14f115c1fd47ff22d18ed49' => 
    array (
      0 => '/var/www/casta/admin/themes/default/template/helpers/list/list_action_addstock.tpl',
      1 => 1426844064,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '353583588550beb06baa4b5-07837061',
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
  'unifunc' => 'content_550beb06bb7710_32135614',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_550beb06bb7710_32135614')) {function content_550beb06bb7710_32135614($_smarty_tpl) {?>
<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['href']->value, ENT_QUOTES, 'UTF-8', true);?>
" class="edit btn btn-default" title="<?php echo $_smarty_tpl->tpl_vars['action']->value;?>
">
	<i class="icon-circle-arrow-up"></i> <?php echo $_smarty_tpl->tpl_vars['action']->value;?>

</a><?php }} ?>
