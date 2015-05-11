<?php /* Smarty version Smarty-3.1.19, created on 2015-04-22 11:01:39
         compiled from "C:\xampp2\htdocs\prestashop\themes\TMS\store_infos.tpl" */ ?>
<?php /*%%SmartyHeaderCode:57435537a9c38747a0-76721189%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7f118738283aa951a49ce41481626a508d62c7f0' => 
    array (
      0 => 'C:\\xampp2\\htdocs\\prestashop\\themes\\TMS\\store_infos.tpl',
      1 => 1429704547,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '57435537a9c38747a0-76721189',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'days_datas' => 0,
    'one_day' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5537a9c3953252_98407991',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5537a9c3953252_98407991')) {function content_5537a9c3953252_98407991($_smarty_tpl) {?>


	<?php  $_smarty_tpl->tpl_vars['one_day'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['one_day']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['days_datas']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['one_day']->key => $_smarty_tpl->tpl_vars['one_day']->value) {
$_smarty_tpl->tpl_vars['one_day']->_loop = true;
?>
	<p>
		<strong class="dark"><?php echo smartyTranslate(array('s'=>$_smarty_tpl->tpl_vars['one_day']->value['day']),$_smarty_tpl);?>
: </strong> &nbsp;<span><?php echo $_smarty_tpl->tpl_vars['one_day']->value['hours'];?>
</span>
	</p>
	<?php } ?>

<?php }} ?>
